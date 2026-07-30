@extends('layouts.app')

@section('title', 'Incident #' . $incident->incident_number)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Incident Details -->
    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-xl font-bold">{{ $incident->incident_number }}</h2>
                <p class="text-gray-500">{{ $incident->incident_datetime->format('F d, Y H:i') }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-medium
                @if($incident->status == 'resolved') bg-green-100 text-green-800
                @elseif($incident->status == 'investigating') bg-blue-100 text-blue-800
                @else bg-yellow-100 text-yellow-800 @endif">
                {{ ucfirst($incident->status) }}
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
            <div>
                <p class="text-xs text-gray-500 uppercase">Severity</p>
                <span class="px-2 py-1 text-xs rounded-full 
                    @if($incident->severity == 'minor') bg-green-100 text-green-800
                    @elseif($incident->severity == 'moderate') bg-yellow-100 text-yellow-800
                    @elseif($incident->severity == 'major') bg-orange-100 text-orange-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ ucfirst($incident->severity) }}
                </span>
            </div>
            <div><p class="text-xs text-gray-500 uppercase">Type</p><p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $incident->type)) }}</p></div>
            <div><p class="text-xs text-gray-500 uppercase">Project</p><p class="font-semibold">{{ $incident->project->name ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-500 uppercase">Site</p><p class="font-semibold">{{ $incident->site->site_name ?? 'N/A' }}</p></div>
            <div><p class="text-xs text-gray-500 uppercase">Reported By</p><p class="font-semibold">{{ $incident->reportedBy->full_name ?? 'N/A' }}</p></div>
            @if($incident->location)
            <div><p class="text-xs text-gray-500 uppercase">Location</p><p class="font-semibold">{{ $incident->location }}</p></div>
            @endif
        </div>

        <div class="border-t pt-4 space-y-4">
            <div>
                <h3 class="font-semibold text-gray-700 mb-1">Description</h3>
                <p class="text-gray-600">{{ $incident->description }}</p>
            </div>
            @if($incident->immediate_actions)
            <div>
                <h3 class="font-semibold text-gray-700 mb-1">Immediate Actions Taken</h3>
                <p class="text-gray-600">{{ $incident->immediate_actions }}</p>
            </div>
            @endif
            @if($incident->affected_persons)
            <div>
                <h3 class="font-semibold text-gray-700 mb-1">Affected Persons</h3>
                <p class="text-gray-600">{{ $incident->affected_persons }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Investigation (if resolved) -->
    @if($incident->status == 'resolved')
    <div class="bg-green-50 rounded-2xl border border-green-200 p-6">
        <h3 class="font-semibold text-green-800 mb-3">✅ Resolution Details</h3>
        <div class="space-y-3">
            @if($incident->root_cause)
            <div><p class="text-sm text-green-700 font-medium">Root Cause</p><p class="text-green-600">{{ $incident->root_cause }}</p></div>
            @endif
            @if($incident->corrective_actions)
            <div><p class="text-sm text-green-700 font-medium">Corrective Actions</p><p class="text-green-600">{{ $incident->corrective_actions }}</p></div>
            @endif
            @if($incident->preventive_measures)
            <div><p class="text-sm text-green-700 font-medium">Preventive Measures</p><p class="text-green-600">{{ $incident->preventive_measures }}</p></div>
            @endif
            <p class="text-sm text-green-500">Resolved by {{ $incident->investigatedBy->full_name ?? 'N/A' }} on {{ $incident->resolved_at ? $incident->resolved_at->format('M d, Y') : '' }}</p>
        </div>
    </div>
    @endif

    <!-- Resolve Form (if not resolved) -->
    @if($incident->status != 'resolved')
    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-700 mb-4">🔍 Investigation & Resolution</h3>
        <form action="{{ route('safety.resolve', $incident) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Root Cause</label>
                <textarea name="root_cause" rows="2" class="w-full rounded-lg border-gray-300" placeholder="What caused this incident?"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Corrective Actions</label>
                <textarea name="corrective_actions" rows="2" class="w-full rounded-lg border-gray-300" placeholder="What actions were taken?"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Preventive Measures</label>
                <textarea name="preventive_measures" rows="2" class="w-full rounded-lg border-gray-300" placeholder="How to prevent this in future?"></textarea>
            </div>
            <button type="submit" class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">✅ Resolve Incident</button>
        </form>
    </div>
    @endif

    <a href="{{ route('safety.index') }}" class="text-blue-500 hover:underline">← Back to Safety Incidents</a>
</div>
@endsection
