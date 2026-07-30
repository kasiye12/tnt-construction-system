@extends('layouts.mobile')

@section('title', $channel->display_name ?? $channel->name ?? 'Chat')

@section('content')
<div class="chat-container" style="display:flex;flex-direction:column;height:calc(100vh - 140px);">
    <!-- Messages -->
    <div class="messages-area" id="msgArea" style="flex:1;overflow-y:auto;padding:8px 0;">
        @forelse($messages ?? [] as $msg)
            @if($msg->type == 'system')
                <div class="system-msg">{{ $msg->body }}</div>
            @else
                <div class="msg {{ $msg->sender_id == Auth::id() ? 'mine' : '' }}" data-id="{{ $msg->id }}">
                    @if($msg->sender_id != Auth::id())
                    <div class="msg-avatar">{{ strtoupper(substr($msg->sender->full_name ?? 'U', 0, 1)) }}</div>
                    @endif
                    <div>
                        @if($msg->sender_id != Auth::id())
                        <div class="msg-name">{{ $msg->sender->full_name ?? 'Unknown' }}</div>
                        @endif
                        <div class="msg-bubble">
                            @if($msg->type == 'image' && $msg->media_urls)
                                @php $urls = json_decode($msg->media_urls); @endphp
                                <img src="{{ $urls->url }}" style="max-width:200px;border-radius:8px;margin-bottom:4px;">
                            @endif
                            {{ $msg->body }}
                        </div>
                        <div class="msg-time">{{ $msg->created_at->format('H:i') }}</div>
                    </div>
                    @if($msg->sender_id == Auth::id())
                    <div class="msg-avatar">{{ strtoupper(substr(Auth::user()->full_name ?? 'U', 0, 1)) }}</div>
                    @endif
                </div>
            @endif
        @empty
            <div style="text-align:center;padding:40px;color:#94a3b8;">
                <div style="font-size:48px;">💬</div>
                <div style="margin-top:12px;">No messages yet. Start the conversation!</div>
            </div>
        @endforelse
    </div>
    
    <!-- Input -->
    <div class="chat-input-area">
        <input type="text" id="msgInput" placeholder="Type a message..." onkeypress="if(event.key==='Enter')sendMsg()">
        <button class="send-btn" onclick="sendMsg()">➤</button>
    </div>
</div>

<script>
const msgArea = document.getElementById('msgArea');
msgArea.scrollTop = msgArea.scrollHeight;

async function sendMsg() {
    const input = document.getElementById('msgInput');
    const body = input.value.trim();
    if (!body) return;
    
    try {
        const res = await fetch('/chat/{{ $channel->id }}/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ body })
        });
        
        if (res.ok) {
            input.value = '';
            location.reload();
        }
    } catch(e) { console.error(e); }
}

// Auto-refresh every 5 seconds
setInterval(() => location.reload(), 8000);
</script>
@endsection
