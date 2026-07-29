<?php
// File: app/Http/Controllers/Api/V1/ChatController.php

namespace App\Http\Controllers\Api\V1;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Models\Channel;
use App\Models\Message;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function getChannels(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $channels = $user->channels()
            ->with(['project:id,name', 'site:id,site_name'])
            ->withCount(['messages as unread_count' => function ($query) use ($user) {
                $query->where('created_at', '>', function ($subQuery) use ($user) {
                    $subQuery->select('last_read_at')
                        ->from('channel_members')
                        ->where('user_id', $user->id)
                        ->whereColumn('channel_id', 'channels.id');
                });
            }])
            ->get()
            ->map(function ($channel) {
                return [
                    'id' => $channel->id,
                    'uuid' => $channel->uuid,
                    'name' => $channel->name,
                    'type' => $channel->type,
                    'project_name' => $channel->project->name ?? null,
                    'site_name' => $channel->site->site_name ?? null,
                    'unread_count' => $channel->unread_count ?? 0,
                    'last_message' => $channel->messages()->latest()->first(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $channels
        ]);
    }

    public function getMessages(Channel $channel, Request $request): JsonResponse
    {
        $this->authorize('view', $channel);

        $messages = $channel->messages()
            ->with(['sender:id,uuid,full_name,profile_photo'])
            ->latest()
            ->paginate($request->per_page ?? 50);

        // Mark channel as read for current user
        $channel->members()->updateExistingPivot(Auth::id(), [
            'last_read_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    public function sendMessage(SendMessageRequest $request, Channel $channel): JsonResponse
    {
        $this->authorize('sendMessage', $channel);

        $message = $this->chatService->sendMessage(
            $channel,
            Auth::user(),
            $request->validated()
        );

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'data' => $message->load('sender:id,uuid,full_name,profile_photo')
        ], 201);
    }

    public function uploadMedia(Request $request, Channel $channel): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'type' => 'required|in:image,file,voice'
        ]);

        $path = $request->file('file')->store(
            'chat-media/' . $channel->id . '/' . date('Y/m/d'),
            's3'
        );

        return response()->json([
            'success' => true,
            'data' => [
                'url' => Storage::disk('s3')->url($path),
                'path' => $path,
                'type' => $request->type,
                'size' => $request->file('file')->getSize(),
                'mime_type' => $request->file('file')->getMimeType(),
            ]
        ]);
    }

    public function markAsRead(Channel $channel): JsonResponse
    {
        $channel->members()->updateExistingPivot(Auth::id(), [
            'last_read_at' => now()
        ]);

        return response()->json(['success' => true]);
    }

    public function createGroup(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:users,id',
        ]);

        $channel = $this->chatService->createGroupChannel(
            $request->project_id,
            $request->name,
            $request->member_ids
        );

        return response()->json([
            'success' => true,
            'data' => $channel->load('members:id,uuid,full_name')
        ], 201);
    }
}