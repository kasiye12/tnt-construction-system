@extends('layouts.app')

@section('title', 'Export Reports')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <x-card header="📥 Export Daily Reports">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Excel Export -->
            <div class="border rounded-lg p-6 hover:shadow-lg transition">
                <div class="text-center">
                    <div class="text-5xl mb-4">📊</div>
                    <h3 class="text-xl font-bold mb-2">Export to Excel</h3>
                    <p class="text-gray-600 mb-4">Download reports in Excel format for analysis</p>
                    
                    <form action="{{ route('reports.export.excel') }}" method="GET" class="space-y-3">
                        <div>
                            <select name="project_id" class="w-full rounded border-gray-300">
                                <option value="">All Projects</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="date" name="date_from" placeholder="From" class="rounded border-gray-300">
                            <input type="date" name="date_to" placeholder="To" class="rounded border-gray-300">
                        </div>
                        <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            📥 Download Excel
                        </button>
                    </form>
                </div>
            </div>

            <!-- PDF Export -->
            <div class="border rounded-lg p-6 hover:shadow-lg transition">
                <div class="text-center">
                    <div class="text-5xl mb-4">📄</div>
                    <h3 class="text-xl font-bold mb-2">Export to PDF</h3>
                    <p class="text-gray-600 mb-4">Generate PDF reports for printing and sharing</p>
                    
                    <form action="{{ route('reports.export.pdf') }}" method="GET" class="space-y-3">
                        <div>
                            <select name="project_id" class="w-full rounded border-gray-300">
                                <option value="">All Projects</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="date" name="date_from" placeholder="From" class="rounded border-gray-300">
                            <input type="date" name="date_to" placeholder="To" class="rounded border-gray-300">
                        </div>
                        <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                            📄 Download PDF
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </x-card>

    <!-- Recent Reports Preview -->
    <x-card header="📋 Recent Reports">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Site</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Print</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reports as $report)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $report->report_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $report->project->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $report->site->site_name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $report->submittedBy->full_name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-badge color="{{ $report->status == 'approved' ? 'green' : ($report->status == 'submitted' ? 'blue' : 'gray') }}">
                                {{ ucfirst($report->status) }}
                            </x-badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('reports.print', $report) }}" 
                               class="text-blue-600 hover:text-blue-800">
                                🖨️ Print
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            No reports found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
