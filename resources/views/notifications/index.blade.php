@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        <div class="p-6 border-b flex justify-between items-center">
            <h2 class="text-xl font-bold">🔔 Notifications</h2>
            <div class="flex items-center gap-3">
                <button onclick="testNotification()" class="text-sm bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                    Test Notification
                </button>
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-blue-500 hover:text-blue-600">
                        Mark All Read
                    </button>
                </form>
            </div>
        </div>

        <div class="divide-y">
            @forelse($notifications ?? [] as $notification)
            <div class="p-4 hover:bg-gray-50 flex items-start gap-3 {{ $notification->read_at ? '' : 'bg-blue-50' }}">
                @if(!$notification->read_at)
                    <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></span>
                @endif
                <div class="flex-1">
                    @php $data = is_string($notification->data) ? json_decode($notification->data, true) : ($notification->data ?? []); @endphp
                    <p class="font-semibold text-sm">{{ $data['title'] ?? 'Notification' }}</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $data['message'] ?? '' }}</p>
                    <span class="text-xs text-gray-400 mt-2 block">
                        {{ $notification->created_at ? $notification->created_at->diffForHumans() : '' }}
                    </span>
                </div>
                @if(!$notification->read_at)
                <button onclick="markAsRead('{{ $notification->id }}')" class="text-xs text-blue-500 hover:underline">
                    Read
                </button>
                @endif
            </div>
            @empty
            <div class="p-12 text-center text-gray-500">
                <div class="text-5xl mb-4">🔔</div>
                <p class="text-lg">No notifications</p>
                <button onclick="testNotification()" class="text-blue-500 hover:underline mt-2">Send test notification</button>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
function markAsRead(id) {
    fetch(`/notifications/${id}/mark-read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => location.reload());
}

function testNotification() {
    if (window.notificationManager) {
        window.notificationManager.test();
    } else {
        alert('Notification system initializing...');
    }
}
</script>
@endsection
