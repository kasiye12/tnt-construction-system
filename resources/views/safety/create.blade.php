@extends('layouts.app')

@section('title', 'Report Incident')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b bg-red-50">
            <h2 class="text-2xl font-bold text-red-800">🦺 Report Safety Incident</h2>
        </div>

        <form action="{{ route('safety.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Project *</label>
                    <select name="project_id" required class="mt-1 block w-full rounded border-gray-300">
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Site *</label>
                    <select name="site_id" required class="mt-1 block w-full rounded border-gray-300">
                        <option value="">Select Site</option>
                        @foreach($sites as $site)
                        <option value="{{ $site->id }}">{{ $site->site_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date/Time *</label>
                    <input type="datetime-local" name="incident_datetime" required 
                           class="mt-1 block w-full rounded border-gray-300"
                           value="{{ now()->format('Y-m-d\TH:i') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Type *</label>
                    <select name="type" required class="mt-1 block w-full rounded border-gray-300">
                        <option value="injury">Injury</option>
                        <option value="near_miss">Near Miss</option>
                        <option value="property_damage">Property Damage</option>
                        <option value="environmental">Environmental</option>
                        <option value="equipment_failure">Equipment Failure</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Severity *</label>
                    <select name="severity" required class="mt-1 block w-full rounded border-gray-300">
                        <option value="minor">Minor</option>
                        <option value="moderate">Moderate</option>
                        <option value="major">Major</option>
                        <option value="fatal">Fatal</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description *</label>
                <textarea name="description" rows="4" required 
                          class="mt-1 block w-full rounded border-gray-300"
                          placeholder="Describe what happened..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Immediate Actions Taken</label>
                <textarea name="immediate_actions" rows="3" 
                          class="mt-1 block w-full rounded border-gray-300"
                          placeholder="What was done immediately after the incident?"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Affected Persons</label>
                    <input type="text" name="affected_persons" 
                           class="mt-1 block w-full rounded border-gray-300"
                           placeholder="Names of affected persons">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Injuries Sustained</label>
                    <input type="text" name="injuries_sustained" 
                           class="mt-1 block w-full rounded border-gray-300"
                           placeholder="Description of injuries">
                </div>
            </div>

            <div class="flex space-x-6">
                <label class="flex items-center">
                    <input type="checkbox" name="medical_treatment_required" value="1" class="rounded">
                    <span class="ml-2 text-sm">Medical treatment required</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="work_stoppage" value="1" class="rounded">
                    <span class="ml-2 text-sm">Work stoppage occurred</span>
                </label>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('safety.index') }}" class="px-4 py-2 border rounded">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    Report Incident
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
