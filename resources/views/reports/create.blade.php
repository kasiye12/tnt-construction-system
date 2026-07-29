@extends('layouts.app')

@section('title', 'Submit Daily Report')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-2xl font-bold">Submit Daily Report</h2>
        </div>

        <form action="{{ route('reports.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Site *</label>
                    <select name="site_id" id="site_select" required 
                            class="mt-1 block w-full rounded border-gray-300">
                        <option value="">Select Site</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" data-project="{{ $site->project_id }}">
                                {{ $site->site_name }} ({{ $site->project->name ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Report Date *</label>
                    <input type="date" name="report_date" required value="{{ date('Y-m-d') }}"
                           class="mt-1 block w-full rounded border-gray-300">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Workforce Count</label>
                    <input type="number" name="workforce_count" min="0" value="{{ old('workforce_count') }}"
                           class="mt-1 block w-full rounded border-gray-300">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Subcontractors</label>
                    <input type="number" name="subcontractor_count" min="0" value="{{ old('subcontractor_count') }}"
                           class="mt-1 block w-full rounded border-gray-300">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Absent Workers</label>
                    <input type="number" name="absent_count" min="0" value="{{ old('absent_count') }}"
                           class="mt-1 block w-full rounded border-gray-300">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Progress Percentage: <span id="progress_value">0%</span>
                </label>
                <input type="range" name="progress_percentage" min="0" max="100" value="0"
                       class="mt-1 block w-full" oninput="document.getElementById('progress_value').textContent = this.value + '%'">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Work Summary</label>
                <textarea name="summary_text" rows="4" 
                          class="mt-1 block w-full rounded border-gray-300"
                          placeholder="Describe the work done today...">{{ old('summary_text') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Challenges Encountered</label>
                <textarea name="challenges_encountered" rows="3" 
                          class="mt-1 block w-full rounded border-gray-300"
                          placeholder="Any issues or delays...">{{ old('challenges_encountered') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Safety Incidents</label>
                <textarea name="safety_incidents" rows="3" 
                          class="mt-1 block w-full rounded border-gray-300"
                          placeholder="Report any safety incidents...">{{ old('safety_incidents') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Material Deliveries</label>
                <textarea name="material_deliveries" rows="3" 
                          class="mt-1 block w-full rounded border-gray-300"
                          placeholder="List materials received today...">{{ old('material_deliveries') }}</textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="submit" name="status" value="draft" 
                        class="px-6 py-2 border rounded text-gray-700 hover:bg-gray-50">
                    Save as Draft
                </button>
                <button type="submit" name="status" value="submitted" 
                        class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Submit Report
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
