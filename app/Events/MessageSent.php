<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load(['sender:id,uuid,full_name,profile_photo']);
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('chat.' . $this->message->channel_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'uuid' => $this->message->uuid,
            'channel_id' => $this->message->channel_id,
            'sender' => [
                'id' => $this->message->sender->id,
                'name' => $this->message->sender->full_name,
                'avatar' => $this->message->sender->profile_photo,
            ],
            'type' => $this->message->type,
            'body' => $this->message->body,
            'media_urls' => $this->message->media_urls,
            'created_at' => $this->message->created_at->toISOString(),
            'is_edited' => $this->message->is_edited,
        ];
    }
}
