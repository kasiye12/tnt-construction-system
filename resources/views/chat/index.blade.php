@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div class="flex h-[calc(100vh-8rem)] bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-200">
    <!-- Conversations Sidebar -->
    <div class="w-[360px] border-r border-gray-200 flex flex-col bg-gray-50/50">
        <!-- Header -->
        <div class="p-5 border-b border-gray-200 bg-white">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-gray-900">Messages</h1>
                <div class="flex items-center space-x-2">
                    <button onclick="showNewGroupModal()" class="p-2 hover:bg-gray-100 rounded-xl transition" title="New Group">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="relative">
                <input type="text" placeholder="Search messages..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border-0 rounded-xl text-sm focus:ring-2 focus:ring-sky-500">
                <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex bg-white border-b border-gray-200">
            <button class="flex-1 py-3 text-sm font-medium text-sky-600 border-b-2 border-sky-600">All</button>
            <button class="flex-1 py-3 text-sm font-medium text-gray-500 hover:text-gray-700">Direct</button>
            <button class="flex-1 py-3 text-sm font-medium text-gray-500 hover:text-gray-700">Groups</button>
        </div>

        <!-- Conversations List -->
        <div class="flex-1 overflow-y-auto">
            <!-- Direct Messages -->
            @if($directChats->count() > 0)
            <div class="px-4 py-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Direct Messages</p>
            </div>
            @foreach($directChats as $chat)
            <a href="{{ route('chat.show', $chat) }}" 
               class="flex items-center px-4 py-3 hover:bg-gray-100 transition mx-2 rounded-xl mb-1
                      {{ request()->route('channel')?->id == $chat->id ? 'bg-sky-50 border border-sky-200' : '' }}">
                <div class="relative flex-shrink-0">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white font-bold text-lg">
                        {{ $chat->display_avatar }}
                    </div>
                    @if($chat->other_user?->last_seen_at?->diffInMinutes(now()) < 5)
                    <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-white"></span>
                    @endif
                </div>
                <div class="ml-3 flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $chat->display_name }}</p>
                        <span class="text-xs text-gray-400">{{ $chat->last_message_at?->format('H:i') }}</span>
                    </div>
                    <p class="text-xs text-gray-500 truncate mt-0.5">
                        {{ $chat->lastMessage?->body ?? 'Start conversation' }}
                    </p>
                </div>
                @if($chat->unread_count > 0)
                <span class="ml-2 min-w-[22px] h-5 bg-sky-500 text-white text-xs font-bold rounded-full flex items-center justify-center px-1.5">
                    {{ $chat->unread_count }}
                </span>
                @endif
            </a>
            @endforeach
            @endif

            <!-- Group Channels -->
            <div class="px-4 py-2 mt-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Channels</p>
            </div>
            @forelse($groupChannels as $channel)
            <a href="{{ route('chat.show', $channel) }}" 
               class="flex items-center px-4 py-3 hover:bg-gray-100 transition mx-2 rounded-xl mb-1
                      {{ request()->route('channel')?->id == $channel->id ? 'bg-sky-50 border border-sky-200' : '' }}">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                    {{ $channel->display_avatar }}
                </div>
                <div class="ml-3 flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $channel->display_name }}</p>
                        <span class="text-xs text-gray-400">{{ $channel->last_message_at?->format('H:i') }}</span>
                    </div>
                    <p class="text-xs text-gray-500 truncate mt-0.5">
                        {{ $channel->lastMessage?->body ?? 'No messages yet' }}
                    </p>
                </div>
            </a>
            @empty
            <div class="text-center py-8 px-4">
                <p class="text-sm text-gray-500">No channels yet</p>
                <button onclick="showNewGroupModal()" class="text-sky-500 text-sm hover:underline mt-1">Create one</button>
            </div>
            @endforelse
        </div>

        <!-- Bottom: Start New Chat -->
        <div class="p-4 border-t border-gray-200 bg-white">
            <button onclick="showUserList()" 
                    class="w-full flex items-center justify-center space-x-2 bg-sky-500 text-white py-3 rounded-xl font-medium hover:bg-sky-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>New Conversation</span>
            </button>
        </div>
    </div>

    <!-- Main Chat Area (Welcome Screen) -->
    <div class="flex-1 flex items-center justify-center bg-gradient-to-br from-gray-50 to-white">
        <div class="text-center max-w-md">
            <div class="w-32 h-32 mx-auto mb-6 rounded-3xl bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center shadow-lg shadow-sky-200">
                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">TNT Messages</h2>
            <p class="text-gray-500 mb-8">Connect with your team instantly. Select a conversation or start a new one.</p>
            <div class="flex flex-col space-y-3">
                <button onclick="showUserList()" class="w-full bg-sky-500 text-white py-3 rounded-xl font-medium hover:bg-sky-600 transition">
                    💬 Direct Message
                </button>
                <button onclick="showNewGroupModal()" class="w-full bg-white border border-gray-300 text-gray-700 py-3 rounded-xl font-medium hover:bg-gray-50 transition">
                    👥 Create Group
                </button>
            </div>
        </div>
    </div>
</div>

<!-- User List Modal -->
@include('chat.partials.user-list-modal')

<!-- New Group Modal -->
@include('chat.partials.new-group-modal')

<script>
function showUserList() {
    document.getElementById('userListModal').classList.remove('hidden');
}
function hideUserList() {
    document.getElementById('userListModal').classList.add('hidden');
}
function showNewGroupModal() {
    document.getElementById('newGroupModal').classList.remove('hidden');
}
function hideNewGroupModal() {
    document.getElementById('newGroupModal').classList.add('hidden');
}

async function startDirectChat(userId) {
    try {
        const res = await fetch('{{ route("chat.direct") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ user_id: userId })
        });
        const data = await res.json();
        if (data.redirect) window.location.href = data.redirect;
    } catch(e) { console.error(e); }
}

async function createGroup() {
    const name = document.getElementById('groupName').value.trim();
    const members = Array.from(document.querySelectorAll('.member-checkbox:checked')).map(cb => cb.value);
    if (!name) return alert('Enter group name');
    if (members.length === 0) return alert('Select members');
    
    try {
        const res = await fetch('{{ route("chat.create") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name, member_ids: members })
        });
        const data = await res.json();
        if (data.redirect) window.location.href = data.redirect;
    } catch(e) { console.error(e); }
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { hideUserList(); hideNewGroupModal(); }
});
</script>
@endsection
