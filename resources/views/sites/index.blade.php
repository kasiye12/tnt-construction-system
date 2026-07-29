@extends('layouts.app')

@section('title', 'Sites')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
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
                <label class="block text-sm text-gray-600">Status</label>
                <select name="status" class="mt-1 block w-full rounded border-gray-300">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Filter
                </button>
            </div>
            <div>
                <a href="{{ route('sites.create') }}" 
                   class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 inline-block">
                    + New Site
                </a>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Sites Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($sites as $site)
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold">
                            <a href="{{ route('sites.show', $site) }}" class="text-blue-600 hover:underline">
                                {{ $site->site_name }}
                            </a>
                        </h3>
                        <p class="text-sm text-gray-600">{{ $site->site_code }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full 
                        @if($site->status == 'active') bg-green-100 text-green-800
                        @elseif($site->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($site->status == 'completed') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($site->status) }}
                    </span>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Project:</span>
                        <span class="font-medium">{{ $site->project->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Type:</span>
                        <span>{{ ucfirst(str_replace('_', ' ', $site->type)) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Area:</span>
                        <span>{{ $site->area_sqm ? number_format($site->area_sqm) . ' m²' : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Supervisor:</span>
                        <span>{{ $site->supervisor->full_name ?? 'Not assigned' }}</span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span>Progress</span>
                        <span>{{ $site->progress_percentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" 
                             style="width: {{ $site->progress_percentage }}%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-3 rounded-b-lg flex justify-between">
                <a href="{{ route('sites.edit', $site) }}" 
                   class="text-blue-600 hover:underline text-sm">Edit</a>
                <form action="{{ route('sites.destroy', $site) }}" method="POST"
                      onsubmit="return confirm('Delete this site?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12">
            <p class="text-gray-500 text-lg">No sites found</p>
            <a href="{{ route('sites.create') }}" class="text-blue-500 hover:underline">Create your first site</a>
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $sites->links() }}
    </div>
</div>
@endsection
