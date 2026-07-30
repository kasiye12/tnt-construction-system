@extends('layouts.mobile')

@section('title', 'Messages')

@section('content')
<div class="animate-in">
    <style>
        .chat-container { display: flex; flex-direction: column; height: calc(100vh - 140px); }
        .messages-area { flex: 1; overflow-y: auto; padding: 8px 0; }
        .msg { display: flex; margin-bottom: 16px; gap: 8px; }
        .msg.mine { justify-content: flex-end; }
        .msg-avatar { width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 14px; flex-shrink: 0; background: linear-gradient(135deg, #0ea5e9, #3b82f6); }
        .msg.mine .msg-avatar { background: linear-gradient(135deg, #10b981, #059669); }
        .msg-bubble { max-width: 80%; padding: 10px 14px; border-radius: 16px; font-size: 14px; line-height: 1.5; position: relative; }
        .msg:not(.mine) .msg-bubble { background: white; border: 1px solid #e2e8f0; border-top-left-radius: 4px; }
        .msg.mine .msg-bubble { background: #0ea5e9; color: white; border-top-right-radius: 4px; }
        .msg-name { font-size: 11px; font-weight: 600; color: #0ea5e9; margin-bottom: 2px; }
        .msg-time { font-size: 10px; color: #94a3b8; margin-top: 4px; text-align: right; }
        .msg.mine .msg-time { color: rgba(255,255,255,0.7); }
        .system-msg { text-align: center; font-size: 11px; color: #94a3b8; padding: 8px; }
        
        .chat-input-area { display: flex; gap: 8px; padding: 12px 0; background: #f1f5f9; position: sticky; bottom: 0; }
        .chat-input-area input { flex: 1; padding: 12px 16px; border: 1.5px solid #e2e8f0; border-radius: 24px; font-size: 14px; outline: none; background: white; }
        .chat-input-area input:focus { border-color: #0ea5e9; }
        .send-btn { width: 44px; height: 44px; background: #0ea5e9; color: white; border: none; border-radius: 50%; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        
        .channel-item { display: flex; align-items: center; gap: 12px; padding: 14px; background: white; border-radius: 12px; margin-bottom: 8px; text-decoration: none; color: inherit; cursor: pointer; border: 1px solid #e2e8f0; }
        .channel-item:active { background: #f8fafc; }
        .channel-avatar { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 18px; flex-shrink: 0; }
        .channel-info { flex: 1; min-width: 0; }
        .channel-name { font-weight: 600; font-size: 14px; }
        .channel-last { font-size: 12px; color: #94a3b8; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .channel-time { font-size: 11px; color: #94a3b8; }
        .unread-badge { background: #0ea5e9; color: white; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 10px; min-width: 20px; text-align: center; }
    </style>

    <!-- Channels List -->
    <div style="margin-bottom: 12px;">
        <button onclick="showChannels()" class="btn btn-outline" style="margin-bottom:8px;">
            📋 Channels ({{ count($channels ?? []) }})
        </button>
    </div>
    
    <div id="channelsList">
        @forelse($channels ?? [] as $channel)
        <a href="/mobile/chat/{{ $channel->id }}" class="channel-item">
            <div class="channel-avatar" style="background:linear-gradient(135deg, #0ea5e9, #3b82f6);">
                {{ strtoupper(substr($channel->display_name ?? $channel->name, 0, 1)) }}
            </div>
            <div class="channel-info">
                <div class="channel-name">{{ $channel->display_name ?? $channel->name }}</div>
                <div class="channel-last">{{ $channel->lastMessage->body ?? 'No messages' }}</div>
            </div>
            @if(($channel->unread_count ?? 0) > 0)
            <span class="unread-badge">{{ $channel->unread_count }}</span>
            @endif
        </a>
        @empty
        <div class="card" style="text-align:center;padding:30px;">
            <div style="font-size:40px;">💬</div>
            <div style="color:#94a3b8;margin-top:8px;">No conversations yet</div>
            <button onclick="window.location='/chat'" class="btn btn-primary" style="margin-top:12px;">Start Chat</button>
        </div>
        @endforelse
    </div>
</div>
@endsection
