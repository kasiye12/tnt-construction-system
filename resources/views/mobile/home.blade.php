@extends('layouts.mobile')

@section('title', 'TNT Construction')

@section('content')
<div class="space-y-4">
    <!-- Welcome Card -->
    <div class="card bg-blue-500 text-white">
        <h2 class="text-xl font-bold">Welcome, {{ $user->full_name }}!</h2>
        <p class="text-blue-100 mt-1">{{ $user->position ?? 'Worker' }}</p>
        @if($user->site)
        <p class="text-blue-200 text-sm mt-2">📍 {{ $user->site->site_name }}</p>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 gap-3">
        <a href="/mobile/checkin" class="card text-center {{ $todayCheckin ? 'bg-green-50' : '' }}">
            <div class="text-3xl mb-2">{{ $todayCheckin ? '✅' : '📍' }}</div>
            <div class="font-semibold text-sm">
                {{ $todayCheckin ? 'Checked In' : 'Check In' }}
            </div>
            @if($todayCheckin)
            <div class="text-xs text-gray-500 mt-1">{{ $todayCheckin->check_in_time->format('H:i') }}</div>
            @endif
        </a>

        <a href="/mobile/reports/create" class="card text-center {{ $todayReport ? 'bg-green-50' : '' }}">
            <div class="text-3xl mb-2">{{ $todayReport ? '✅' : '📝' }}</div>
            <div class="font-semibold text-sm">
                {{ $todayReport ? 'Report Done' : 'Daily Report' }}
            </div>
        </a>

        <a href="/mobile/reports" class="card text-center">
            <div class="text-3xl mb-2">📊</div>
            <div class="font-semibold text-sm">My Reports</div>
            <div class="text-xs text-gray-500 mt-1">{{ $recentReports->count() }} this week</div>
        </a>

        <a href="/mobile/chat" class="card text-center">
            <div class="text-3xl mb-2">💬</div>
            <div class="font-semibold text-sm">Messages</div>
        </a>
    </div>

    <!-- Recent Reports -->
    <div class="card">
        <h3 class="font-bold mb-3">Recent Reports</h3>
        @forelse($recentReports as $report)
        <div class="flex justify-between items-center py-2 border-b last:border-0">
            <div>
                <p class="text-sm font-medium">{{ $report->site->site_name ?? 'N/A' }}</p>
                <p class="text-xs text-gray-500">{{ $report->report_date->format('M d, Y') }}</p>
            </div>
            <span class="text-xs px-2 py-1 rounded-full 
                @if($report->status == 'approved') bg-green-100 text-green-800
                @elseif($report->status == 'submitted') bg-yellow-100 text-yellow-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ $report->status }}
            </span>
        </div>
        @empty
        <p class="text-gray-500 text-sm text-center py-4">No reports yet</p>
        @endforelse
    </div>
</div>

<script>
// Check online status
function updateOnlineStatus() {
    if (!navigator.onLine) {
        document.body.insertAdjacentHTML('afterbegin', 
            '<div class="bg-yellow-500 text-white text-center py-2 text-sm">⚠️ Offline - Data will sync when connected</div>');
    }
}
updateOnlineStatus();
</script>
@endsection
