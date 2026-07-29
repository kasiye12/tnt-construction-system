@extends('layouts.app')

@section('title', 'Create Site')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-2xl font-bold">Create New Site</h2>
        </div>

        <form action="{{ route('sites.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Project *</label>
                    <select name="project_id" required 
                            class="mt-1 block w-full rounded border-gray-300">
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Site Code *</label>
                    <input type="text" name="site_code" required 
                           class="mt-1 block w-full rounded border-gray-300"
                           value="{{ old('site_code') }}">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Site Name *</label>
                <input type="text" name="site_name" required 
                       class="mt-1 block w-full rounded border-gray-300"
                       value="{{ old('site_name') }}">
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Type *</label>
                    <select name="type" required class="mt-1 block w-full rounded border-gray-300">
                        <option value="main_site">Main Site</option>
                        <option value="sub_site">Sub Site</option>
                        <option value="temporary">Temporary</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status *</label>
                    <select name="status" required class="mt-1 block w-full rounded border-gray-300">
                        <option value="pending">Pending</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Address</label>
                <input type="text" name="address" 
                       class="mt-1 block w-full rounded border-gray-300"
                       value="{{ old('address') }}">
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Area (m²)</label>
                    <input type="number" name="area_sqm" step="0.01" min="0"
                           class="mt-1 block w-full rounded border-gray-300"
                           value="{{ old('area_sqm') }}">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Max Workers</label>
                    <input type="number" name="max_workers" min="1"
                           class="mt-1 block w-full rounded border-gray-300"
                           value="{{ old('max_workers') }}">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Supervisor</label>
                    <select name="supervisor_id" class="mt-1 block w-full rounded border-gray-300">
                        <option value="">Select Supervisor</option>
                        @foreach($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}">
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
                           value="{{ old('start_date') }}">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Expected End Date</label>
                    <input type="date" name="expected_end_date" 
                           class="mt-1 block w-full rounded border-gray-300"
                           value="{{ old('expected_end_date') }}">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" rows="3" 
                          class="mt-1 block w-full rounded border-gray-300">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('sites.index') }}" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Create Site
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
