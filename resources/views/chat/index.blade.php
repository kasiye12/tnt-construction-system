@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div class="flex h-[calc(100vh-10rem)] bg-gray-50 rounded-2xl overflow-hidden border border-gray-200">
    <!-- Sidebar -->
    <div class="w-80 bg-white border-r flex flex-col">
        <div class="p-4 border-b">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-bold text-gray-900">Messages</h2>
                <button onclick="toggleNewChat()" class="w-8 h-8 bg-sky-500 text-white rounded-lg flex items-center justify-center hover:bg-sky-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            </div>
            <input type="text" placeholder="Search messages..." 
                   class="w-full px-4 py-2 bg-gray-50 border-0 rounded-xl text-sm focus:ring-2 focus:ring-sky-500">
        </div>

        <div class="flex-1 overflow-y-auto">
            <!-- Direct Messages -->
            @if($directChats->count() > 0)
            <div class="px-4 py-2">
                <p class="text-xs font-semibold text-gray-400 uppercase">Direct Messages</p>
            </div>
            @foreach($directChats as $channel)
            <a href="{{ route('chat.show', $channel) }}" 
               class="flex items-center px-4 py-3 hover:bg-gray-50 transition {{ request()->route('channel')?->id == $channel->id ? 'bg-sky-50 border-l-2 border-sky-500' : '' }}">
                <div class="relative">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white font-bold">
                        {{ $channel->avatar_letter }}
                    </div>
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
                </div>
                <div class="ml-3 flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $channel->display_name }}</p>
                        @if($channel->last_message_at)
                            <span class="text-xs text-gray-400">{{ $channel->last_message_at->format('H:i') }}</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 truncate mt-1">
                        {{ $channel->messages->first()?->body ?? 'Start chatting' }}
                    </p>
                </div>
            </a>
            @endforeach
            @endif

            <!-- Group Channels -->
            <div class="px-4 py-2 mt-2">
                <p class="text-xs font-semibold text-gray-400 uppercase">Channels</p>
            </div>
            @forelse($groupChannels as $channel)
            <a href="{{ route('chat.show', $channel) }}" 
               class="flex items-center px-4 py-3 hover:bg-gray-50 transition {{ request()->route('channel')?->id == $channel->id ? 'bg-sky-50 border-l-2 border-sky-500' : '' }}">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center text-white font-bold">
                    {{ $channel->avatar_letter }}
                </div>
                <div class="ml-3 flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $channel->display_name }}</p>
                        @if($channel->last_message_at)
                            <span class="text-xs text-gray-400">{{ $channel->last_message_at->format('H:i') }}</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 truncate mt-1">
                        {{ $channel->messages->first()?->body ?? 'No messages yet' }}
                    </p>
                </div>
            </a>
            @empty
            <div class="text-center py-4 text-gray-500">
                <p class="text-sm">No channels yet</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Main Area -->
    <div class="flex-1 flex items-center justify-center bg-gradient-to-br from-gray-50 to-white">
        <div class="text-center">
            <div class="w-24 h-24 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center">
                <span class="text-4xl">💬</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">TNT Messages</h3>
            <p class="text-gray-500 mb-6">Chat with your team in real-time</p>
            <div class="flex gap-3 justify-center">
                <button onclick="showUserList()" class="bg-sky-500 text-white px-6 py-3 rounded-xl font-medium hover:bg-sky-600 transition">
                    💬 Direct Message
                </button>
                <button onclick="toggleNewChat()" class="bg-white border border-gray-300 text-gray-700 px-6 py-3 rounded-xl font-medium hover:bg-gray-50 transition">
                    👥 New Group
                </button>
            </div>
        </div>
    </div>
</div>

<!-- New Group Modal -->
<div id="newChatModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center" style="display: none;">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold">New Group Chat</h3>
            <button onclick="toggleNewChat()" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <input type="text" id="channelName" placeholder="Group name..." 
               class="w-full px-4 py-2 border rounded-xl text-sm mb-3">
        <div class="max-h-48 overflow-y-auto border rounded-xl mb-4">
            @foreach($users as $user)
            <label class="flex items-center p-3 hover:bg-gray-50 cursor-pointer border-b last:border-0">
                <input type="checkbox" value="{{ $user->id }}" class="member-checkbox rounded text-sky-500">
                <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-emerald-500 rounded-lg flex items-center justify-center text-white text-sm ml-3">
                    {{ strtoupper(substr($user->full_name, 0, 1)) }}
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ $user->full_name }}</p>
                    <p class="text-xs text-gray-500">{{ $user->position ?? 'Staff' }}</p>
                </div>
            </label>
            @endforeach
        </div>
        <button onclick="createChannel()" class="w-full bg-sky-500 text-white py-3 rounded-xl font-medium hover:bg-sky-600">
            Create Group
        </button>
    </div>
</div>

<!-- Direct Message Modal -->
<div id="userListModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center" style="display: none;">
    <div class="bg-white rounded-2xl p-6 w-full max-w-sm mx-4 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold">New Message</h3>
            <button onclick="hideUserList()" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <input type="text" placeholder="Search users..." 
               class="w-full px-4 py-2 border rounded-xl text-sm mb-3"
               oninput="filterUsers(this.value)">
        <div class="max-h-64 overflow-y-auto" id="userList">
            @foreach($users as $user)
            <div onclick="startDirectChat({{ $user->id }})" 
                 class="flex items-center p-3 hover:bg-gray-50 rounded-xl cursor-pointer user-item">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr($user->full_name, 0, 1)) }}
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ $user->full_name }}</p>
                    <p class="text-xs text-gray-500">{{ $user->position ?? 'Staff' }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
function toggleNewChat() {
    const modal = document.getElementById('newChatModal');
    modal.style.display = modal.style.display === 'flex' ? 'none' : 'flex';
}

function showUserList() {
    document.getElementById('userListModal').style.display = 'flex';
}

function hideUserList() {
    document.getElementById('userListModal').style.display = 'none';
}

function filterUsers(query) {
    document.querySelectorAll('.user-item').forEach(item => {
        const name = item.querySelector('.text-sm').textContent.toLowerCase();
        item.style.display = name.includes(query.toLowerCase()) ? 'flex' : 'none';
    });
}

async function startDirectChat(userId) {
    try {
        const response = await fetch('{{ route("chat.direct") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ user_id: userId })
        });
        
        const data = await response.json();
        if (data.success && data.redirect) {
            window.location.href = data.redirect;
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function createChannel() {
    const name = document.getElementById('channelName').value.trim() || 'New Group';
    const members = Array.from(document.querySelectorAll('.member-checkbox:checked')).map(cb => cb.value);
    
    if (members.length === 0) {
        alert('Select at least one member');
        return;
    }
    
    try {
        const response = await fetch('{{ route("chat.create") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name, member_ids: members })
        });
        
        const data = await response.json();
        if (data.success && data.redirect) {
            window.location.href = data.redirect;
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Close modals on Escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.getElementById('newChatModal').style.display = 'none';
        document.getElementById('userListModal').style.display = 'none';
    }
});
</script>
@endsection
