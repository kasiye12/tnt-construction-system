@extends('layouts.app')

@section('title', 'Report - ' . $report->report_date->format('M d, Y'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold">Daily Report</h2>
                <p class="text-gray-600">{{ $report->report_date->format('l, F d, Y') }}</p>
            </div>
            <div class="flex space-x-3">
                <span class="px-3 py-1 text-sm rounded-full 
                    @if($report->status == 'approved') bg-green-100 text-green-800
                    @elseif($report->status == 'submitted') bg-blue-100 text-blue-800
                    @elseif($report->status == 'rejected') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ ucfirst($report->status) }}
                </span>
                
                @if($report->status == 'submitted')
                    <form action="{{ route('reports.approve', $report) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded text-sm hover:bg-green-600">
                            ✓ Approve
                        </button>
                    </form>
                    <button onclick="document.getElementById('reject_form').classList.toggle('hidden')" 
                            class="px-3 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600">
                        ✗ Reject
                    </button>
                @endif
            </div>
        </div>

        <div id="reject_form" class="hidden p-6 bg-red-50 border-b">
            <form action="{{ route('reports.reject', $report) }}" method="POST">
                @csrf
                <label class="block text-sm font-medium text-gray-700">Rejection Reason</label>
                <textarea name="rejection_reason" rows="2" required 
                          class="mt-1 block w-full rounded border-gray-300"></textarea>
                <button type="submit" class="mt-2 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    Submit Rejection
                </button>
            </form>
        </div>

        <div class="p-6 grid grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm text-gray-600">Project</h3>
                <p class="font-semibold">{{ $report->project->name ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-600">Site</h3>
                <p class="font-semibold">{{ $report->site->site_name ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-600">Submitted By</h3>
                <p class="font-semibold">{{ $report->submittedBy->full_name ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-600">Progress</h3>
                <div class="flex items-center mt-1">
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-500 h-3 rounded-full" 
                             style="width: {{ $report->progress_percentage ?? 0 }}%"></div>
                    </div>
                    <span class="ml-2 font-semibold">{{ $report->progress_percentage ?? 0 }}%</span>
                </div>
            </div>
        </div>

        <div class="p-6 border-t space-y-4">
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded">
                    <p class="text-sm text-gray-600">Workforce</p>
                    <p class="text-2xl font-bold">{{ $report->workforce_count ?? 0 }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded">
                    <p class="text-sm text-gray-600">Subcontractors</p>
                    <p class="text-2xl font-bold">{{ $report->subcontractor_count ?? 0 }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded">
                    <p class="text-sm text-gray-600">Absent</p>
                    <p class="text-2xl font-bold">{{ $report->absent_count ?? 0 }}</p>
                </div>
            </div>

            @if($report->summary_text)
            <div>
                <h4 class="font-semibold mb-2">Work Summary</h4>
                <p class="text-gray-700">{{ $report->summary_text }}</p>
            </div>
            @endif

            @if($report->challenges_encountered)
            <div>
                <h4 class="font-semibold mb-2 text-orange-600">Challenges</h4>
                <p class="text-gray-700">{{ $report->challenges_encountered }}</p>
            </div>
            @endif

            @if($report->safety_incidents)
            <div>
                <h4 class="font-semibold mb-2 text-red-600">Safety Incidents</h4>
                <p class="text-gray-700">{{ $report->safety_incidents }}</p>
            </div>
            @endif

            @if($report->material_deliveries)
            <div>
                <h4 class="font-semibold mb-2">Material Deliveries</h4>
                <p class="text-gray-700">{{ $report->material_deliveries }}</p>
            </div>
            @endif

            @if($report->approved_by)
            <div class="bg-green-50 p-4 rounded">
                <p class="text-sm text-green-600">
                    ✓ Approved by {{ $report->approvedBy->full_name ?? 'N/A' }} 
                    on {{ $report->approved_at ? $report->approved_at->format('M d, Y H:i') : '' }}
                </p>
            </div>
            @endif

            @if($report->rejection_reason)
            <div class="bg-red-50 p-4 rounded">
                <p class="text-sm text-red-600">
                    ✗ Rejected: {{ $report->rejection_reason }}
                </p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
