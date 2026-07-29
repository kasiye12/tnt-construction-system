<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $channel_id;
    public $user;
    public $is_typing;

    public function __construct($channelId, $user, $isTyping)
    {
        $this->channel_id = $channelId;
        $this->user = [
            'id' => $user->id,
            'name' => $user->full_name,
        ];
        $this->is_typing = $isTyping;
    }

    public function broadcastOn(): array
    {
        return [new Channel('chat.' . $this->channel_id)];
    }

    public function broadcastAs(): string
    {
        return 'user.typing';
    }
}
