@extends('layouts.app')

@section('title', $channel->type == 'direct' ? ($otherUser->full_name ?? 'Chat') : $channel->name)

@section('content')
<div class="flex h-[calc(100vh-10rem)] bg-gray-50 rounded-2xl overflow-hidden border border-gray-200">
    <!-- Sidebar -->
    <div class="w-80 bg-white border-r flex flex-col">
        <div class="p-4 border-b">
            <a href="{{ route('chat.index') }}" class="text-sky-500 hover:text-sky-600 text-sm flex items-center">
                ← Back
            </a>
            @if($channel->type == 'direct' && $otherUser)
                <div class="flex items-center mt-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white text-lg font-bold">
                        {{ strtoupper(substr($otherUser->full_name, 0, 1)) }}
                    </div>
                    <div class="ml-3">
                        <h2 class="text-lg font-bold">{{ $otherUser->full_name }}</h2>
                        <p class="text-sm text-gray-500">{{ $otherUser->position ?? 'Staff' }}</p>
                    </div>
                </div>
            @else
                <h2 class="text-lg font-bold mt-2"># {{ $channel->name }}</h2>
                <p class="text-sm text-gray-500">{{ $channel->members->count() }} members</p>
            @endif
        </div>
        
        @if($channel->type != 'direct')
        <div class="flex-1 overflow-y-auto p-4">
            <h3 class="text-xs font-semibold text-gray-400 uppercase mb-3">Members</h3>
            @foreach($channel->members as $member)
            <div class="flex items-center py-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center text-white text-sm font-bold">
                    {{ strtoupper(substr($member->full_name ?? 'U', 0, 1)) }}
                </div>
                <div class="ml-2">
                    <p class="text-sm font-medium">{{ $member->full_name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-400">{{ $member->pivot->role ?? 'member' }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Chat Area -->
    <div class="flex-1 flex flex-col">
        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4" id="messagesContainer">
            @foreach($messages as $message)
                @if($message->type == 'system')
                <div class="flex justify-center">
                    <span class="bg-gray-100 text-gray-500 text-xs px-3 py-1 rounded-full">{{ $message->body }}</span>
                </div>
                @else
                <div class="flex {{ $message->sender_id == Auth::id() ? 'justify-end' : 'justify-start' }}" data-id="{{ $message->id }}">
                    <div class="flex {{ $message->sender_id == Auth::id() ? 'flex-row-reverse' : '' }} items-end max-w-[70%] gap-2">
                        @if($message->sender_id != Auth::id())
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center text-white text-sm flex-shrink-0">
                            {{ strtoupper(substr($message->sender->full_name ?? 'U', 0, 1)) }}
                        </div>
                        @endif
                        
                        <div class="{{ $message->sender_id == Auth::id() ? 'bg-sky-500 text-white' : 'bg-white border' }} rounded-2xl px-4 py-2 shadow-sm">
                            @if($message->sender_id != Auth::id() && $channel->type != 'direct')
                            <p class="text-xs font-semibold text-sky-600 mb-1">{{ $message->sender->full_name ?? 'Unknown' }}</p>
                            @endif
                            
                            @if($message->type == 'image' && $message->media_urls)
                                @php $urls = json_decode($message->media_urls); @endphp
                                <img src="{{ $urls->url }}" class="max-w-xs rounded-lg mb-2">
                            @endif
                            
                            @if($message->body)
                            <p class="text-sm">{{ $message->body }}</p>
                            @endif
                            
                            <div class="flex items-center justify-end gap-2 mt-1">
                                <span class="text-xs {{ $message->sender_id == Auth::id() ? 'text-sky-100' : 'text-gray-400' }}">
                                    {{ $message->created_at->format('H:i') }}
                                </span>
                                @if($message->sender_id == Auth::id())
                                <button onclick="deleteMessage({{ $message->id }})" class="text-xs text-red-300 hover:text-red-100">🗑️</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <!-- Input -->
        <div class="p-4 bg-white border-t">
            <form id="messageForm" class="flex items-end gap-2">
                @csrf
                <button type="button" onclick="document.getElementById('fileInput').click()" class="p-2 text-gray-400 hover:text-sky-500 rounded-xl">📎</button>
                <input type="file" id="fileInput" name="file" class="hidden" onchange="sendMessage(event)">
                <textarea id="messageInput" name="body" rows="1" 
                          class="flex-1 border-0 bg-gray-50 rounded-xl px-4 py-3 resize-none text-sm"
                          placeholder="Type a message..."
                          onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); sendMessage(event); }"></textarea>
                <button type="submit" class="p-3 bg-sky-500 text-white rounded-xl hover:bg-sky-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
const container = document.getElementById('messagesContainer');
container.scrollTop = container.scrollHeight;

// Auto-refresh every 3 seconds for new messages
setInterval(() => {
    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newMessages = doc.getElementById('messagesContainer').innerHTML;
            if (newMessages !== container.innerHTML) {
                container.innerHTML = newMessages;
                container.scrollTop = container.scrollHeight;
            }
        });
}, 3000);

async function sendMessage(e) {
    e.preventDefault();
    const input = document.getElementById('messageInput');
    const body = input.value.trim();
    const file = document.getElementById('fileInput').files[0];
    
    if (!body && !file) return;
    
    const formData = new FormData();
    if (body) formData.append('body', body);
    if (file) formData.append('file', file);
    formData.append('_token', '{{ csrf_token() }}');
    
    try {
        await fetch('{{ url("/chat/".$channel->id."/send") }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        input.value = '';
        document.getElementById('fileInput').value = '';
        location.reload();
    } catch (error) {
        console.error('Error:', error);
    }
}

async function deleteMessage(id) {
    if (!confirm('Delete?')) return;
    await fetch('/chat/message/' + id + '/delete', {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
    document.querySelector('[data-id="' + id + '"]').remove();
}
</script>
@endsection
