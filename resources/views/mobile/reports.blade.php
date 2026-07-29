@extends('layouts.mobile')

@section('title', 'My Reports')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h2 class="text-lg font-bold">My Reports</h2>
        <a href="/mobile/reports/create" class="bg-blue-500 text-white px-4 py-2 rounded text-sm">
            + New Report
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 text-green-700 p-3 rounded">
        {{ session('success') }}
    </div>
    @endif

    @forelse($reports as $report)
    <div class="card">
        <div class="flex justify-between items-start">
            <div>
                <p class="font-semibold">{{ $report->site->site_name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-500">{{ $report->report_date->format('M d, Y') }}</p>
            </div>
            <span class="text-xs px-2 py-1 rounded-full 
                @if($report->status == 'approved') bg-green-100 text-green-800
                @elseif($report->status == 'submitted') bg-blue-100 text-blue-800
                @elseif($report->status == 'rejected') bg-red-100 text-red-800
                @else bg-gray-100 text-gray-800
                @endif">
                {{ ucfirst($report->status) }}
            </span>
        </div>
        @if($report->summary_text)
        <p class="text-sm mt-2 text-gray-600">{{ Str::limit($report->summary_text, 100) }}</p>
        @endif
        <div class="flex justify-between mt-3 text-sm">
            <span>👥 {{ $report->workforce_count }} workers</span>
            <span>📊 {{ $report->progress_percentage }}%</span>
        </div>
    </div>
    @empty
    <div class="card text-center py-8">
        <p class="text-gray-500">No reports yet</p>
        <a href="/mobile/reports/create" class="text-blue-500 text-sm mt-2">Create your first report</a>
    </div>
    @endforelse

    {{ $reports->links() }}
</div>
@endsection
