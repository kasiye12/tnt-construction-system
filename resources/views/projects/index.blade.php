@extends('layouts.app')

@section('title', 'Projects')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b flex justify-between items-center">
        <h2 class="text-2xl font-bold">Projects</h2>
        <a href="{{ route('projects.create') }}" 
           class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            + New Project
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 m-6 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="p-6">
        <table class="w-full">
            <thead>
                <tr class="text-left bg-gray-50">
                    <th class="px-4 py-2">Code</th>
                    <th class="px-4 py-2">Project Name</th>
                    <th class="px-4 py-2">Location</th>
                    <th class="px-4 py-2">Manager</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Priority</th>
                    <th class="px-4 py-2">Budget</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $project->code }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:underline">
                            {{ $project->name }}
                        </a>
                    </td>
                    <td class="px-4 py-3">{{ $project->location }}</td>
                    <td class="px-4 py-3">{{ $project->manager->full_name ?? 'N/A' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($project->status == 'active') bg-green-100 text-green-800
                            @elseif($project->status == 'planning') bg-blue-100 text-blue-800
                            @elseif($project->status == 'on_hold') bg-yellow-100 text-yellow-800
                            @elseif($project->status == 'completed') bg-gray-100 text-gray-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($project->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($project->priority == 'critical') bg-red-100 text-red-800
                            @elseif($project->priority == 'high') bg-orange-100 text-orange-800
                            @elseif($project->priority == 'medium') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($project->priority) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">${{ number_format($project->budget, 2) }}</td>
                    <td class="px-4 py-3">
                        <div class="flex space-x-2">
                            <a href="{{ route('projects.edit', $project) }}" 
                               class="text-blue-600 hover:text-blue-800">Edit</a>
                            <form action="{{ route('projects.destroy', $project) }}" method="POST" 
                                  onsubmit="return confirm('Delete this project?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-8 text-gray-500">
                        No projects found. <a href="{{ route('projects.create') }}" class="text-blue-500">Create one</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="mt-4">
            {{ $projects->links() }}
        </div>
    </div>
</div>
@endsection
