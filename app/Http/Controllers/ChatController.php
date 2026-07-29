<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function index()
    {
        $channels = Channel::with(['messages' => function($q) {
                $q->latest()->take(1);
            }])
            ->where(function($q) {
                $q->whereHas('members', function($q) {
                    $q->where('user_id', Auth::id());
                })->orWhere('type', 'announcement');
            })
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function($channel) {
                // For direct chats, show the other person's name
                if ($channel->type == 'direct') {
                    $otherUser = $channel->members()
                        ->where('user_id', '!=', Auth::id())
                        ->first();
                    $channel->display_name = $otherUser ? $otherUser->full_name : $channel->name;
                    $channel->avatar_letter = $otherUser ? strtoupper(substr($otherUser->full_name, 0, 1)) : '?';
                } else {
                    $channel->display_name = $channel->name;
                    $channel->avatar_letter = strtoupper(substr($channel->name, 0, 1));
                }
                return $channel;
            });

        // Separate channels and direct chats
        $groupChannels = $channels->where('type', '!=', 'direct');
        $directChats = $channels->where('type', 'direct');
        
        $users = User::where('id', '!=', Auth::id())
            ->where('status', 'active')
            ->get(['id', 'full_name', 'position', 'profile_photo']);

        return view('chat.index', compact('groupChannels', 'directChats', 'users'));
    }

    public function show($id)
    {
        $channel = Channel::with(['members', 'project', 'site'])->findOrFail($id);
        
        // Mark as read
        if ($channel->members()->where('user_id', Auth::id())->exists()) {
            $channel->members()->updateExistingPivot(Auth::id(), [
                'last_read_at' => now()
            ]);
        }

        // For direct chats, get the other user
        $otherUser = null;
        if ($channel->type == 'direct') {
            $otherUser = $channel->members()
                ->where('user_id', '!=', Auth::id())
                ->first();
        }

        $messages = $channel->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat.show', compact('channel', 'messages', 'otherUser'));
    }

    public function sendMessage(Request $request, $id)
    {
        $channel = Channel::findOrFail($id);
        
        $request->validate([
            'body' => 'required_without:file|string|max:5000',
            'file' => 'nullable|file|max:10240',
        ]);

        $type = 'text';
        $mediaUrls = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('chat/' . $channel->id, 'public');
            $type = str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file';
            $mediaUrls = json_encode([
                'url' => asset('storage/' . $path),
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ]);
        }

        $message = Message::create([
            'uuid' => Str::uuid(),
            'channel_id' => $channel->id,
            'sender_id' => Auth::id(),
            'type' => $type,
            'body' => $request->body,
            'media_urls' => $mediaUrls,
        ]);

        $channel->update(['last_message_at' => now()]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $message->load('sender')
            ]);
        }

        return back();
    }

    /**
     * Start or get existing direct chat with a user
     */
    public function startDirectChat(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $otherUserId = $request->user_id;
        $currentUserId = Auth::id();

        // Check if direct chat already exists
        $existingChannel = Channel::where('type', 'direct')
            ->whereHas('members', function($q) use ($currentUserId) {
                $q->where('user_id', $currentUserId);
            })
            ->whereHas('members', function($q) use ($otherUserId) {
                $q->where('user_id', $otherUserId);
            })
            ->first();

        if ($existingChannel) {
            return response()->json([
                'success' => true,
                'redirect' => route('chat.show', $existingChannel)
            ]);
        }

        // Create new direct chat
        $otherUser = User::find($otherUserId);
        
        $channel = Channel::create([
            'uuid' => Str::uuid(),
            'name' => 'Direct Chat',
            'type' => 'direct',
            'is_private' => true,
            'created_by' => $currentUserId,
            'last_message_at' => now(),
        ]);

        // Add both users
        $channel->members()->attach($currentUserId, [
            'role' => 'member',
            'joined_at' => now()
        ]);
        $channel->members()->attach($otherUserId, [
            'role' => 'member',
            'joined_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'redirect' => route('chat.show', $channel)
        ]);
    }

    public function createChannel(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'member_ids' => 'required|array|min:1',
            'member_ids.*' => 'exists:users,id',
        ]);

        $channel = Channel::create([
            'uuid' => Str::uuid(),
            'name' => $request->name,
            'type' => 'group',
            'description' => 'Group chat',
            'created_by' => Auth::id(),
            'last_message_at' => now(),
        ]);

        // Add creator
        $channel->members()->attach(Auth::id(), [
            'role' => 'admin',
            'joined_at' => now()
        ]);

        // Add members
        foreach ($request->member_ids as $userId) {
            if ($userId != Auth::id()) {
                $channel->members()->attach($userId, [
                    'role' => 'member',
                    'joined_at' => now()
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => ['id' => $channel->id],
            'redirect' => route('chat.show', $channel)
        ]);
    }

    public function deleteMessage($id)
    {
        $message = Message::findOrFail($id);
        
        if ($message->sender_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->delete();
        return response()->json(['success' => true]);
    }
}
