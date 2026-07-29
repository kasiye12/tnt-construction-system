@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between items-center">
            <h2 class="text-2xl font-bold">Notifications</h2>
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="text-blue-500 hover:underline text-sm">
                    Mark all as read
                </button>
            </form>
        </div>

        <div class="divide-y">
            @forelse($notifications as $notification)
            <div class="p-4 hover:bg-gray-50 {{ $notification->read_at ? '' : 'bg-blue-50' }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            @if(!$notification->read_at)
                                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            @endif
                            <h3 class="font-semibold">{{ $notification->data['title'] ?? 'Notification' }}</h3>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</span>
                    </div>
                    @if(!$notification->read_at)
                    <button onclick="markAsRead('{{ $notification->id }}')" 
                            class="text-xs text-blue-500 hover:underline">
                        Mark read
                    </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-gray-500">
                <div class="text-4xl mb-3">🔔</div>
                <p class="text-lg">No notifications</p>
            </div>
            @endforelse
        </div>

        <div class="p-4">
            {{ $notifications->links() }}
        </div>
    </div>
</div>

<script>
function markAsRead(id) {
    fetch(`/notifications/${id}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(() => location.reload());
}
</script>
@endsection
