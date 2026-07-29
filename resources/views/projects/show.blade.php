@extends('layouts.app')

@section('title', $project->name)

@section('content')
<div class="space-y-6">
    <!-- Project Details -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between items-center">
            <h2 class="text-2xl font-bold">{{ $project->name }}</h2>
            <div class="flex space-x-2">
                <a href="{{ route('projects.edit', $project) }}" 
                   class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Edit</a>
                <form action="{{ route('projects.destroy', $project) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
                            onclick="return confirm('Delete this project?')">Delete</button>
                </form>
            </div>
        </div>
        
        <div class="p-6 grid grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm text-gray-600">Project Code</h3>
                <p class="font-semibold">{{ $project->code }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-600">Status</h3>
                <p class="font-semibold">{{ ucfirst($project->status) }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-600">Location</h3>
                <p class="font-semibold">{{ $project->location }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-600">Priority</h3>
                <p class="font-semibold">{{ ucfirst($project->priority) }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-600">Manager</h3>
                <p class="font-semibold">{{ $project->manager->full_name ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-600">Budget</h3>
                <p class="font-semibold">${{ number_format($project->budget, 2) }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-600">Start Date</h3>
                <p class="font-semibold">{{ $project->start_date->format('M d, Y') }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-600">End Date</h3>
                <p class="font-semibold">{{ $project->end_date ? $project->end_date->format('M d, Y') : 'Not set' }}</p>
            </div>
            @if($project->client_name)
            <div>
                <h3 class="text-sm text-gray-600">Client</h3>
                <p class="font-semibold">{{ $project->client_name }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Sites List -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold">Sites ({{ $project->sites->count() }})</h3>
        </div>
        <div class="p-6">
            @if($project->sites->count() > 0)
                <table class="w-full">
                    <thead>
                        <tr class="text-left bg-gray-50">
                            <th class="px-4 py-2">Site Code</th>
                            <th class="px-4 py-2">Site Name</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->sites as $site)
                        <tr class="border-b">
                            <td class="px-4 py-3">{{ $site->site_code }}</td>
                            <td class="px-4 py-3">{{ $site->site_name }}</td>
                            <td class="px-4 py-3">{{ ucfirst($site->status) }}</td>
                            <td class="px-4 py-3">{{ $site->progress_percentage }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500">No sites created yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
