@extends('layouts.mobile')

@section('title', 'TNT Construction')

@section('content')
<div class="animate-in">
    <!-- Welcome -->
    <div class="card" style="background: linear-gradient(135deg, #0ea5e9, #3b82f6); color: white;">
        <p style="opacity:0.9;font-size:13px;">Welcome back</p>
        <h2 style="font-size:20px;font-weight:700;margin-top:2px;">{{ $user->full_name ?? 'Worker' }}</h2>
        <p style="opacity:0.8;font-size:12px;margin-top:4px;">{{ $user->site->site_name ?? 'No site assigned' }}</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid-2">
        <div class="card text-center" onclick="window.location='/mobile/checkin'">
            <div style="font-size:32px;">{{ isset($todayCheckin) && $todayCheckin && !$todayCheckin->check_out_time ? '✅' : '📍' }}</div>
            <div class="stat-label">{{ isset($todayCheckin) && $todayCheckin && !$todayCheckin->check_out_time ? 'On Site' : 'Check In' }}</div>
            @if(isset($todayCheckin) && $todayCheckin)
            <div style="font-size:11px;color:#64748b;">{{ $todayCheckin->check_in_time->format('H:i') }}</div>
            @endif
        </div>
        <div class="card text-center" onclick="window.location='/mobile/reports/create'">
            <div style="font-size:32px;">{{ isset($todayReport) && $todayReport ? '✅' : '📝' }}</div>
            <div class="stat-label">{{ isset($todayReport) && $todayReport ? 'Report Done' : 'Submit Report' }}</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid-3">
        <a href="/mobile/checkin" class="card text-center" style="text-decoration:none;">
            <div style="font-size:24px;">📍</div>
            <div style="font-size:10px;color:#64748b;margin-top:4px;">Check In</div>
        </a>
        <a href="/mobile/reports/create" class="card text-center" style="text-decoration:none;">
            <div style="font-size:24px;">📝</div>
            <div style="font-size:10px;color:#64748b;margin-top:4px;">Report</div>
        </a>
        <a href="/chat" class="card text-center" style="text-decoration:none;">
            <div style="font-size:24px;">💬</div>
            <div style="font-size:10px;color:#64748b;margin-top:4px;">Chat</div>
        </a>
    </div>

    <!-- Recent Reports -->
    @if(isset($recentReports) && $recentReports->count() > 0)
    <div class="card">
        <div class="card-header">📋 Recent Reports</div>
        @foreach($recentReports->take(5) as $report)
        <div class="list-item">
            <div>
                <div style="font-size:14px;font-weight:600;">{{ $report->site->site_name ?? 'N/A' }}</div>
                <div style="font-size:12px;color:#64748b;">{{ $report->report_date->format('M d, Y') }}</div>
            </div>
            <span class="tag {{ $report->status == 'approved' ? 'tag-success' : ($report->status == 'submitted' ? 'tag-info' : 'tag-gray') }}">
                {{ ucfirst($report->status) }}
            </span>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
