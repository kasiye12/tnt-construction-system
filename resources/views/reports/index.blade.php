@extends('layouts.app')

@section('title', 'Daily Reports')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-4 gap-4">
            <div>
                <label class="block text-sm text-gray-600">Project</label>
                <select name="project_id" class="mt-1 block w-full rounded border-gray-300">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600">Site</label>
                <select name="site_id" class="mt-1 block w-full rounded border-gray-300">
                    <option value="">All Sites</option>
                    @foreach($sites as $site)
                        <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                            {{ $site->site_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600">Status</label>
                <select name="status" class="mt-1 block w-full rounded border-gray-300">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between items-center">
            <h2 class="text-2xl font-bold">Daily Reports</h2>
            <a href="{{ route('reports.create') }}" 
               class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                + New Report
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 m-6 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="p-6 overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left bg-gray-50">
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Project</th>
                        <th class="px-4 py-2">Site</th>
                        <th class="px-4 py-2">Submitted By</th>
                        <th class="px-4 py-2">Workforce</th>
                        <th class="px-4 py-2">Progress</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <a href="{{ route('reports.show', $report) }}" class="text-blue-600 hover:underline">
                                {{ $report->report_date->format('M d, Y') }}
                            </a>
                        </td>
                        <td class="px-4 py-3">{{ $report->project->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $report->site->site_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $report->submittedBy->full_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $report->workforce_count ?? 0 }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" 
                                         style="width: {{ $report->progress_percentage ?? 0 }}%"></div>
                                </div>
                                <span class="ml-2 text-sm">{{ $report->progress_percentage ?? 0 }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($report->status == 'approved') bg-green-100 text-green-800
                                @elseif($report->status == 'submitted') bg-blue-100 text-blue-800
                                @elseif($report->status == 'rejected') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($report->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex space-x-2">
                                <a href="{{ route('reports.show', $report) }}" 
                                   class="text-blue-600 hover:underline">View</a>
                                @if($report->status !== 'approved')
                                <a href="{{ route('reports.edit', $report) }}" 
                                   class="text-green-600 hover:underline">Edit</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-gray-500">
                            No reports found. <a href="{{ route('reports.create') }}" class="text-blue-500">Create one</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="mt-4">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
