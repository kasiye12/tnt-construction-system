@extends('layouts.app')

@section('title', 'Edit Site')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-2xl font-bold">Edit Site</h2>
        </div>

        <form action="{{ route('sites.update', $site->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Project *</label>
                    <select name="project_id" required 
                            class="mt-1 block w-full rounded border-gray-300">
                        <option value="">Select Project</option>
                        @foreach(\App\Models\Project::all() as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $site->project_id) == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Site Code *</label>
                    <input type="text" name="site_code" required 
                           class="mt-1 block w-full rounded border-gray-300"
                           value="{{ old('site_code', $site->site_code) }}">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Site Name *</label>
                <input type="text" name="site_name" required 
                       class="mt-1 block w-full rounded border-gray-300"
                       value="{{ old('site_name', $site->site_name) }}">
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Type *</label>
                    <select name="type" required class="mt-1 block w-full rounded border-gray-300">
                        <option value="main_site" {{ old('type', $site->type) == 'main_site' ? 'selected' : '' }}>Main Site</option>
                        <option value="sub_site" {{ old('type', $site->type) == 'sub_site' ? 'selected' : '' }}>Sub Site</option>
                        <option value="temporary" {{ old('type', $site->type) == 'temporary' ? 'selected' : '' }}>Temporary</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status *</label>
                    <select name="status" required class="mt-1 block w-full rounded border-gray-300">
                        <option value="pending" {{ old('status', $site->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="active" {{ old('status', $site->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $site->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="completed" {{ old('status', $site->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Address</label>
                <input type="text" name="address" 
                       class="mt-1 block w-full rounded border-gray-300"
                       value="{{ old('address', $site->address) }}">
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Area (m²)</label>
                    <input type="number" name="area_sqm" step="0.01" min="0"
                           class="mt-1 block w-full rounded border-gray-300"
                           value="{{ old('area_sqm', $site->area_sqm) }}">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Max Workers</label>
                    <input type="number" name="max_workers" min="1"
                           class="mt-1 block w-full rounded border-gray-300"
                           value="{{ old('max_workers', $site->max_workers) }}">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Supervisor</label>
                    <select name="supervisor_id" class="mt-1 block w-full rounded border-gray-300">
                        <option value="">Select Supervisor</option>
                        @foreach(\App\Models\User::where('status', 'active')->get() as $supervisor)
                            <option value="{{ $supervisor->id }}" {{ old('supervisor_id', $site->supervisor_id) == $supervisor->id ? 'selected' : '' }}>
                                {{ $supervisor->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" name="start_date" 
                           class="mt-1 block w-full rounded border-gray-300"
                           value="{{ old('start_date', $site->start_date ? $site->start_date->format('Y-m-d') : '') }}">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Expected End Date</label>
                    <input type="date" name="expected_end_date" 
                           class="mt-1 block w-full rounded border-gray-300"
                           value="{{ old('expected_end_date', $site->expected_end_date ? $site->expected_end_date->format('Y-m-d') : '') }}">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" rows="3" 
                          class="mt-1 block w-full rounded border-gray-300">{{ old('notes', $site->notes) }}</textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('sites.show', $site) }}" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Update Site
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
