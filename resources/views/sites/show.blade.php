@extends('layouts.app')

@section('title', $site->site_name)

@section('content')
<div class="space-y-6">
    <!-- Site Details -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold">{{ $site->site_name }}</h2>
                <p class="text-gray-600">{{ $site->site_code }} - {{ $site->project->name ?? 'N/A' }}</p>
            </div>
            <div class="flex space-x-2">
                <span class="px-3 py-1 rounded-full text-sm 
                    @if($site->status == 'active') bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($site->status) }}
                </span>
                <a href="{{ route('sites.edit', $site) }}" 
                   class="px-3 py-1 bg-blue-500 text-white rounded text-sm">Edit</a>
            </div>
        </div>

        <div class="p-6 grid grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600">Type</p>
                <p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $site->type)) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Area</p>
                <p class="font-semibold">{{ $site->area_sqm ? number_format($site->area_sqm) . ' m²' : 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Supervisor</p>
                <p class="font-semibold">{{ $site->supervisor->full_name ?? 'Not assigned' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Address</p>
                <p class="font-semibold">{{ $site->address ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Start Date</p>
                <p class="font-semibold">{{ $site->start_date ? $site->start_date->format('M d, Y') : 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Expected End</p>
                <p class="font-semibold">{{ $site->expected_end_date ? $site->expected_end_date->format('M d, Y') : 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Progress Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-4">Progress</h3>
        <div class="flex items-center">
            <div class="flex-1 bg-gray-200 rounded-full h-4">
                <div class="bg-blue-500 h-4 rounded-full" style="width: {{ $site->progress_percentage }}%"></div>
            </div>
            <span class="ml-4 text-2xl font-bold">{{ $site->progress_percentage }}%</span>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-bold">Recent Daily Reports</h3>
        </div>
        <div class="p-6">
            @if($site->dailyReports->count() > 0)
                <table class="w-full">
                    <thead>
                        <tr class="text-left bg-gray-50">
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Submitted By</th>
                            <th class="px-4 py-2">Workforce</th>
                            <th class="px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($site->dailyReports as $report)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $report->report_date->format('M d, Y') }}</td>
                            <td class="px-4 py-2">{{ $report->submittedBy->full_name ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $report->workforce_count }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($report->status == 'approved') bg-green-100 text-green-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500">No reports yet</p>
            @endif
        </div>
    </div>
</div>
@endsection
