@extends('layouts.app')

@section('title', 'Safety Incidents')

@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Total Incidents</p>
            <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Minor</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['minor'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Moderate</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['moderate'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Major</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['major'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Open Cases</p>
            <p class="text-2xl font-bold text-orange-600">{{ $stats['open'] }}</p>
        </div>
    </div>

    <!-- Incidents Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between items-center">
            <h2 class="text-2xl font-bold">Safety Incidents</h2>
            <a href="{{ route('safety.create') }}" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                + Report Incident
            </a>
        </div>

        @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 m-6 rounded">
            {{ session('success') }}
        </div>
        @endif

        <div class="p-6 overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left bg-gray-50">
                        <th class="px-4 py-2">Incident #</th>
                        <th class="px-4 py-2">Date/Time</th>
                        <th class="px-4 py-2">Type</th>
                        <th class="px-4 py-2">Severity</th>
                        <th class="px-4 py-2">Site</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incidents as $incident)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $incident->incident_number }}</td>
                        <td class="px-4 py-3">{{ $incident->incident_datetime->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $incident->type)) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($incident->severity == 'minor') bg-green-100 text-green-800
                                @elseif($incident->severity == 'moderate') bg-yellow-100 text-yellow-800
                                @elseif($incident->severity == 'major') bg-orange-100 text-orange-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($incident->severity) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $incident->site->site_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($incident->status == 'resolved') bg-green-100 text-green-800
                                @elseif($incident->status == 'investigating') bg-blue-100 text-blue-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst($incident->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('safety.show', $incident) }}" class="text-blue-600 hover:underline">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-500">
                            No incidents reported. Safety first! 🦺
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $incidents->links() }}
        </div>
    </div>
</div>
@endsection
