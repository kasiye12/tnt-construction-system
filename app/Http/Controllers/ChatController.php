<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use App\Events\UserTyping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $allChannels = Channel::with(['lastMessage.sender'])
            ->where(function($q) use ($userId) {
                $q->whereHas('members', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })->orWhere('type', 'announcement');
            })
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function($channel) use ($userId) {
                if ($channel->type === 'direct') {
                    $other = $channel->members()->where('user_id', '!=', $userId)->first();
                    $channel->display_name = $other->full_name ?? 'Chat';
                    $channel->display_avatar = $other ? strtoupper(substr($other->full_name, 0, 1)) : '?';
                } else {
                    $channel->display_name = $channel->name;
                    $channel->display_avatar = strtoupper(substr($channel->name, 0, 1));
                }
                $lastRead = $channel->members()->where('user_id', $userId)->first()?->pivot?->last_read_at;
                $channel->unread_count = $channel->messages()
                    ->where('sender_id', '!=', $userId)
                    ->when($lastRead, fn($q) => $q->where('created_at', '>', $lastRead))
                    ->count();
                return $channel;
            });

        $directChats = $allChannels->where('type', 'direct');
        $groupChannels = $allChannels->where('type', '!=', 'direct');
        $users = User::where('id', '!=', $userId)->where('status', 'active')->get();
        $currentUser = Auth::user();

        return view('chat.index', compact('directChats', 'groupChannels', 'users', 'currentUser'));
    }

    public function show($id)
    {
        $channel = Channel::with(['members', 'project', 'site'])->findOrFail($id);
        $userId = Auth::id();
        
        if ($channel->members()->where('user_id', $userId)->exists()) {
            $channel->members()->updateExistingPivot($userId, ['last_read_at' => now()]);
        }
        
        $messages = $channel->messages()->with(['sender'])->orderBy('created_at', 'asc')->get();
        $currentUser = Auth::user();
        $otherUser = $channel->type === 'direct' ? $channel->members()->where('user_id', '!=', $userId)->first() : null;
        
        return view('chat.show', compact('channel', 'messages', 'otherUser', 'currentUser'));
    }

    public function sendMessage(Request $request, $id)
    {
        $channel = Channel::findOrFail($id);
        
        $request->validate([
            'body' => 'nullable|string|max:5000',
            'file' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,gif,mp4,avi,mov,pdf,doc,docx,xls,xlsx,txt,zip',
        ]);

        if (!$request->body && !$request->hasFile('file')) {
            return response()->json(['error' => 'Message or file required'], 400);
        }

        $type = 'text';
        $mediaUrls = null;

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('chat-files/' . $channel->id, 'public');
            $mime = $file->getMimeType();
            
            // Determine file type
            if (str_starts_with($mime, 'image/')) $type = 'image';
            elseif (str_starts_with($mime, 'video/')) $type = 'video';
            elseif (str_contains($mime, 'pdf')) $type = 'pdf';
            elseif (str_contains($mime, 'word') || str_contains($mime, 'document')) $type = 'document';
            elseif (str_contains($mime, 'sheet') || str_contains($mime, 'excel')) $type = 'document';
            else $type = 'file';
            
            $mediaUrls = json_encode([
                'url' => asset('storage/' . $path),
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'size_formatted' => $this->formatSize($file->getSize()),
                'mime' => $mime,
                'type' => $type,
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
        // Send notification to other members
        $notifService = new \App\Services\NotificationService();
        $otherMembers = $channel->members()->where('user_id', '!=', Auth::id())->pluck('user_id');
        foreach ($otherMembers as $uid) {
            $notifService->send($uid, 'new_message', '💬 ' . Auth::user()->full_name, \Illuminate\Support\Str::limit($request->body ?? 'Sent a file', 100), ['channel_id' => $channel->id, 'message_id' => $message->id]);
        }
        broadcast(new MessageSent($message))->toOthers();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $message->load('sender')]);
        }
        return back();
    }

    public function typing(Request $request, $id)
    {
        broadcast(new UserTyping($id, Auth::user(), $request->is_typing))->toOthers();
        return response()->json(['success' => true]);
    }

    public function startDirectChat(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $userId = Auth::id(); $otherId = $request->user_id;
        
        $existing = Channel::where('type', 'direct')
            ->whereHas('members', fn($q) => $q->where('user_id', $userId))
            ->whereHas('members', fn($q) => $q->where('user_id', $otherId))
            ->first();
            
        if ($existing) return response()->json(['success' => true, 'redirect' => route('chat.show', $existing)]);
        
        $channel = Channel::create([
            'uuid' => Str::uuid(), 'name' => 'Direct Chat', 'type' => 'direct',
            'is_private' => true, 'created_by' => $userId, 'last_message_at' => now(),
        ]);
        $channel->members()->attach([$userId, $otherId], ['role' => 'member', 'joined_at' => now()]);
        return response()->json(['success' => true, 'redirect' => route('chat.show', $channel)]);
    }

    public function createChannel(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'member_ids' => 'required|array']);
        $channel = Channel::create([
            'uuid' => Str::uuid(), 'name' => $request->name, 'type' => 'group',
            'created_by' => Auth::id(), 'last_message_at' => now(),
        ]);
        $channel->members()->attach(Auth::id(), ['role' => 'admin', 'joined_at' => now()]);
        foreach ($request->member_ids as $uid) {
            if ($uid != Auth::id()) $channel->members()->attach($uid, ['role' => 'member', 'joined_at' => now()]);
        }
        return response()->json(['success' => true, 'redirect' => route('chat.show', $channel)]);
    }

    public function deleteMessage($id)
    {
        $msg = Message::findOrFail($id);
        if ($msg->sender_id !== Auth::id()) return response()->json(['error' => 'Unauthorized'], 403);
        // Delete file if exists
        if ($msg->media_urls) {
            $urls = json_decode($msg->media_urls, true);
            if (isset($urls['url'])) {
                $path = str_replace(asset('storage/'), '', $urls['url']);
                Storage::disk('public')->delete($path);
            }
        }
        $msg->delete();
        return response()->json(['success' => true]);
    }

    private function formatSize($bytes)
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' bytes';
    }
}
