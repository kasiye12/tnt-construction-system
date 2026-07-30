@extends('layouts.app')

@section('title', $channel->type == 'direct' ? ($otherUser->full_name ?? 'Chat') : '#'.$channel->name)

@section('content')
<div class="flex h-[calc(100vh-8rem)] bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-200">
    <div class="w-[320px] border-r flex flex-col bg-gray-50/50">
        <div class="p-4 border-b bg-white">
            <a href="{{ route('chat.index') }}" class="text-sky-500 text-sm">← Back</a>
            <h2 class="font-bold mt-2">{{ $channel->type == 'direct' ? ($otherUser->full_name ?? 'Chat') : '#'.$channel->name }}</h2>
        </div>
    </div>

    <div class="flex-1 flex flex-col">
        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-3" id="messagesContainer">
            @foreach($messages as $msg)
                @if($msg->type == 'system')
                <div class="text-center py-2"><span class="bg-gray-100 text-gray-500 text-xs px-4 py-1.5 rounded-full">{{ $msg->body }}</span></div>
                @else
                <div class="flex {{ $msg->sender_id == Auth::id() ? 'justify-end' : 'justify-start' }} mb-1" data-id="{{ $msg->id }}">
                    <div class="flex {{ $msg->sender_id == Auth::id() ? 'flex-row-reverse' : '' }} items-end max-w-[70%] gap-2">
                        @if($msg->sender_id != Auth::id())
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center text-white text-sm flex-shrink-0">
                            {{ strtoupper(substr($msg->sender->full_name ?? 'U', 0, 1)) }}
                        </div>
                        @endif
                        
                        <div class="{{ $msg->sender_id == Auth::id() ? 'bg-sky-500 text-white' : 'bg-gray-100' }} rounded-2xl px-4 py-2.5 max-w-[300px]">
                            @if($msg->media_urls)
                                @php $media = json_decode($msg->media_urls); @endphp
                                
                                {{-- Image --}}
                                @if($msg->type == 'image')
                                <img src="{{ $media->url }}" class="max-w-[250px] rounded-xl mb-2 cursor-pointer" 
                                     onclick="window.open('{{ $media->url }}')" loading="lazy">
                                @endif
                                
                                {{-- Video --}}
                                @if($msg->type == 'video')
                                <video controls class="max-w-[250px] rounded-xl mb-2" style="max-height:200px;">
                                    <source src="{{ $media->url }}" type="{{ $media->mime }}">
                                </video>
                                @endif
                                
                                {{-- PDF --}}
                                @if($msg->type == 'pdf')
                                <a href="{{ $media->url }}" target="_blank" 
                                   class="flex items-center gap-3 p-3 bg-white/20 rounded-xl hover:bg-white/30 transition">
                                    <span class="text-2xl">📄</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium truncate">{{ $media->name }}</p>
                                        <p class="text-xs opacity-70">{{ $media->size_formatted }}</p>
                                    </div>
                                </a>
                                @endif
                                
                                {{-- Word/Excel Document --}}
                                @if($msg->type == 'document')
                                <a href="{{ $media->url }}" target="_blank" 
                                   class="flex items-center gap-3 p-3 bg-white/20 rounded-xl hover:bg-white/30 transition">
                                    <span class="text-2xl">{{ str_contains($media->mime, 'sheet') || str_contains($media->mime, 'excel') ? '📊' : '📝' }}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium truncate">{{ $media->name }}</p>
                                        <p class="text-xs opacity-70">{{ $media->size_formatted }}</p>
                                    </div>
                                </a>
                                @endif
                                
                                {{-- Other files --}}
                                @if($msg->type == 'file')
                                <a href="{{ $media->url }}" download 
                                   class="flex items-center gap-3 p-3 bg-white/20 rounded-xl hover:bg-white/30 transition">
                                    <span class="text-2xl">📎</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium truncate">{{ $media->name }}</p>
                                        <p class="text-xs opacity-70">{{ $media->size_formatted }}</p>
                                    </div>
                                </a>
                                @endif
                            @endif
                            
                            @if($msg->body)
                            <p class="text-sm leading-relaxed">{{ $msg->body }}</p>
                            @endif
                            
                            <div class="flex items-center justify-end gap-1 mt-1">
                                <span class="text-xs {{ $msg->sender_id == Auth::id() ? 'text-sky-100' : 'text-gray-400' }}">{{ $msg->created_at->format('H:i') }}</span>
                                @if($msg->sender_id == Auth::id())
                                <button onclick="deleteMessage({{ $msg->id }})" class="text-xs">🗑️</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <div id="typingIndicator" class="px-6 py-1 text-xs text-gray-400 hidden"></div>

        <!-- Input with File Upload -->
        <div class="p-4 border-t bg-white">
            <form id="messageForm" class="flex items-end gap-2" enctype="multipart/form-data">
                @csrf
                <input type="file" id="fileInput" name="file" class="hidden" 
                       accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip"
                       onchange="handleFileSelect(this)">
                
                <button type="button" onclick="document.getElementById('fileInput').click()" 
                        class="p-2.5 text-gray-400 hover:text-sky-500 hover:bg-gray-100 rounded-xl transition flex-shrink-0"
                        title="Attach file (Max 5MB)">
                    📎
                </button>
                
                <div class="flex-1 relative">
                    <textarea id="messageInput" rows="1" 
                              class="w-full border-0 bg-gray-100 rounded-2xl px-5 py-3 resize-none text-sm focus:ring-2 focus:ring-sky-500"
                              placeholder="Type a message or attach a file... (Enter to send)"
                              onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMessage();}"></textarea>
                    
                    <!-- File preview -->
                    <div id="filePreview" class="hidden mt-2 p-2 bg-blue-50 rounded-xl flex items-center gap-2">
                        <span id="filePreviewIcon"></span>
                        <span id="filePreviewName" class="text-sm text-gray-700"></span>
                        <span id="filePreviewSize" class="text-xs text-gray-500"></span>
                        <button type="button" onclick="clearFile()" class="text-red-500 text-xs">✕</button>
                    </div>
                </div>
                
                <button type="submit" class="p-3 bg-sky-500 text-white rounded-xl hover:bg-sky-600 transition flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
            <p class="text-xs text-gray-400 mt-2">📎 Supports: Images, Videos, PDF, Word, Excel (Max 5MB)</p>
        </div>
    </div>
</div>

<audio id="msgSound" src="/sounds/notification.wav" preload="auto"></audio>

<script>
const msgContainer = document.getElementById('messagesContainer');
msgContainer.scrollTop = msgContainer.scrollHeight;

// Real-time listener
if (typeof Echo !== 'undefined') {
    Echo.channel('chat.{{ $channel->id }}')
        .listen('.message.sent', (e) => {
            if (e.message.sender_id != {{ Auth::id() }}) {
                appendMessage(e.message);
                playAlert();
            }
        });
}

function handleFileSelect(input) {
    const file = input.files[0];
    if (!file) return;
    
    if (file.size > 5242880) {
        alert('❌ File too large! Maximum size is 5MB.');
        input.value = '';
        return;
    }
    
    const preview = document.getElementById('filePreview');
    document.getElementById('filePreviewName').textContent = file.name;
    document.getElementById('filePreviewSize').textContent = formatSize(file.size);
    
    if (file.type.startsWith('image/')) {
        document.getElementById('filePreviewIcon').textContent = '🖼️';
    } else if (file.type.startsWith('video/')) {
        document.getElementById('filePreviewIcon').textContent = '🎬';
    } else if (file.type.includes('pdf')) {
        document.getElementById('filePreviewIcon').textContent = '📄';
    } else {
        document.getElementById('filePreviewIcon').textContent = '📎';
    }
    
    preview.classList.remove('hidden');
}

function clearFile() {
    document.getElementById('fileInput').value = '';
    document.getElementById('filePreview').classList.add('hidden');
}

function formatSize(bytes) {
    if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
    return (bytes / 1024).toFixed(0) + ' KB';
}

async function sendMessage() {
    const input = document.getElementById('messageInput');
    const fileInput = document.getElementById('fileInput');
    const body = input.value.trim();
    const file = fileInput.files[0];
    
    if (!body && !file) return;
    
    const formData = new FormData();
    if (body) formData.append('body', body);
    if (file) formData.append('file', file);
    formData.append('_token', '{{ csrf_token() }}');
    
    try {
        const res = await fetch('{{ route("chat.send", $channel) }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.success) {
            input.value = '';
            clearFile();
            input.style.height = 'auto';
        }
    } catch(e) { console.error(e); }
}

function appendMessage(msg) {
    // Reload to show complex file attachments properly
    location.reload();
}

function playAlert() {
    const audio = document.getElementById('msgSound');
    if (audio) { audio.currentTime = 0; audio.play().catch(() => {}); }
    if (Notification.permission === 'granted' && document.hidden) {
        new Notification('💬 New Message', { body: 'New message received', icon: '/icons/icon-192x192.png' });
    }
}

async function deleteMessage(id) {
    if (!confirm('Delete?')) return;
    await fetch('/chat/message/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
    document.querySelector('[data-id="' + id + '"]').remove();
}

if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}
</script>
@endsection
