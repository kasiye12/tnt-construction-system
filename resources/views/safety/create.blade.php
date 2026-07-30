@extends('layouts.app')
@section('title', 'Report Incident')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <div class="bg-red-50 p-4 rounded-xl mb-6">
            <h2 class="text-xl font-bold text-red-800">🦺 Report Safety Incident</h2>
        </div>
        <form action="{{ route('safety.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium mb-1">Project *</label>
                    <select name="project_id" required class="w-full rounded-lg border-gray-300">@foreach($projects as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium mb-1">Site *</label>
                    <select name="site_id" required class="w-full rounded-lg border-gray-300">@foreach($sites as $s)<option value="{{ $s->id }}">{{ $s->site_name }}</option>@endforeach</select></div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div><label class="block text-sm font-medium mb-1">Date/Time *</label><input type="datetime-local" name="incident_datetime" required value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full rounded-lg border-gray-300"></div>
                <div><label class="block text-sm font-medium mb-1">Type *</label>
                    <select name="type" required class="w-full rounded-lg border-gray-300"><option value="injury">Injury</option><option value="near_miss">Near Miss</option><option value="property_damage">Property Damage</option><option value="other">Other</option></select></div>
                <div><label class="block text-sm font-medium mb-1">Severity *</label>
                    <select name="severity" required class="w-full rounded-lg border-gray-300"><option value="minor">Minor</option><option value="moderate">Moderate</option><option value="major">Major</option><option value="fatal">Fatal</option></select></div>
            </div>
            <div><label class="block text-sm font-medium mb-1">Location</label><input type="text" name="location" class="w-full rounded-lg border-gray-300"></div>
            <div><label class="block text-sm font-medium mb-1">Description *</label><textarea name="description" rows="4" required class="w-full rounded-lg border-gray-300" placeholder="Describe what happened..."></textarea></div>
            <div><label class="block text-sm font-medium mb-1">Immediate Actions</label><textarea name="immediate_actions" rows="2" class="w-full rounded-lg border-gray-300"></textarea></div>
            <div><label class="block text-sm font-medium mb-1">Affected Persons</label><input type="text" name="affected_persons" class="w-full rounded-lg border-gray-300"></div>
            <div class="flex gap-3">
                <a href="{{ route('safety.index') }}" class="px-4 py-2 border rounded-lg">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-red-500 text-white rounded-lg">Report Incident</button>
            </div>
        </form>
    </div>
</div>
@endsection
