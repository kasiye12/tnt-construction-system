<?php
// File: app/Services/ChatService.php

namespace App\Services;

use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChatService
{
    public function sendMessage(Channel $channel, User $sender, array $data): Message
    {
        return DB::transaction(function () use ($channel, $sender, $data) {
            $messageData = [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'channel_id' => $channel->id,
                'sender_id' => $sender->id,
                'type' => $data['type'] ?? 'text',
                'body' => $data['body'] ?? null,
                'metadata' => $data['metadata'] ?? null,
            ];

            if (isset($data['media_urls'])) {
                $messageData['media_urls'] = $data['media_urls'];
            }

            if (isset($data['parent_message_id'])) {
                $messageData['parent_message_id'] = $data['parent_message_id'];
            }

            $message = Message::create($messageData);

            // Update channel last message timestamp
            $channel->touch();

            return $message;
        });
    }

    public function createGroupChannel(int $projectId, string $name, array $memberIds): Channel
    {
        return DB::transaction(function () use ($projectId, $name, $memberIds) {
            $channel = Channel::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'project_id' => $projectId,
                'name' => $name,
                'type' => 'group',
                'created_by' => auth()->id(),
            ]);

            // Add all members including creator
            $allMembers = array_unique(array_merge($memberIds, [auth()->id()]));
            
            foreach ($allMembers as $userId) {
                $channel->members()->attach($userId, [
                    'role' => $userId === auth()->id() ? 'admin' : 'member',
                    'joined_at' => now(),
                ]);
            }

            // Create system message
            $this->sendSystemMessage($channel, 'Group created');

            return $channel;
        });
    }

    public function sendSystemMessage(Channel $channel, string $message): Message
    {
        return Message::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'channel_id' => $channel->id,
            'sender_id' => auth()->id(),
            'type' => 'system',
            'body' => $message,
        ]);
    }

    public function handleFileUpload(UploadedFile $file, Channel $channel): array
    {
        $path = $file->store(
            'chat-media/' . $channel->id . '/' . date('Y/m/d'),
            's3'
        );

        return [
            'url' => Storage::disk('s3')->url($path),
            'path' => $path,
            'type' => $this->getFileType($file->getMimeType()),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'original_name' => $file->getClientOriginalName(),
        ];
    }

    protected function getFileType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) return 'image';
        if (str_starts_with($mimeType, 'video/')) return 'video';
        if (str_starts_with($mimeType, 'audio/')) return 'voice';
        return 'file';
    }

    public function markChannelAsRead(Channel $channel, User $user): void
    {
        $channel->members()->updateExistingPivot($user->id, [
            'last_read_at' => now(),
        ]);
    }

    public function getUnreadCount(User $user): array
    {
        return $user->channels()
            ->get()
            ->mapWithKeys(function ($channel) use ($user) {
                return [$channel->id => $channel->getUnreadCount($user->id)];
            })
            ->toArray();
    }

    public function searchMessages(Channel $channel, string $query, int $limit = 50)
    {
        return $channel->messages()
            ->where('body', 'ilike', "%{$query}%")
            ->with('sender:id,uuid,full_name,profile_photo')
            ->latest()
            ->paginate($limit);
    }
}