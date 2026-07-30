@extends('layouts.mobile')

@section('title', 'My Reports')

@section('content')
<div class="animate-in">
    <a href="/mobile/reports/create" class="btn btn-primary" style="margin-bottom:16px;">📝 New Report</a>
    
    @forelse($reports ?? [] as $report)
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:start;">
            <div>
                <div style="font-weight:600;">{{ $report->site->site_name ?? 'N/A' }}</div>
                <div style="font-size:12px;color:#64748b;">{{ $report->report_date->format('M d, Y') }}</div>
            </div>
            <span class="tag {{ $report->status == 'approved' ? 'tag-success' : ($report->status == 'submitted' ? 'tag-info' : 'tag-gray') }}">
                {{ ucfirst($report->status) }}
            </span>
        </div>
        @if($report->summary_text)
        <div style="font-size:13px;color:#475569;margin-top:8px;">{{ Str::limit($report->summary_text, 80) }}</div>
        @endif
        <div style="display:flex;gap:16px;margin-top:8px;font-size:12px;color:#64748b;">
            <span>👥 {{ $report->workforce_count }} workers</span>
            <span>📊 {{ $report->progress_percentage }}%</span>
        </div>
    </div>
    @empty
    <div class="card" style="text-align:center;padding:40px;">
        <div style="font-size:48px;margin-bottom:12px;">📝</div>
        <div style="color:#64748b;">No reports yet</div>
    </div>
    @endforelse
    
    @if(isset($reports) && method_exists($reports, 'links'))
    <div style="margin-top:16px;">{{ $reports->links() }}</div>
    @endif
</div>
@endsection
