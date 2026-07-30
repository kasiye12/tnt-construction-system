@extends('layouts.app')
@section('title', 'Safety Incidents')
@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-7 gap-3">
        @foreach(['total'=>'Total','minor'=>'Minor','moderate'=>'Moderate','major'=>'Major','fatal'=>'Fatal','open'=>'Open','resolved'=>'Resolved'] as $key => $label)
        <div class="bg-white rounded-xl p-4 shadow-sm border text-center">
            <div class="text-xl font-bold @if($key=='fatal') text-red-600 @elseif($key=='open') text-orange-600 @elseif($key=='resolved') text-green-600 @endif">{{ $stats[$key] }}</div>
            <div class="text-xs text-gray-500">{{ $label }}</div>
        </div>
        @endforeach
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="p-4 border-b flex justify-between">
            <h2 class="text-lg font-bold">🦺 Safety Incidents</h2>
            <a href="{{ route('safety.create') }}" class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm">+ Report Incident</a>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Incident #</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Severity</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Site</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($incidents as $inc)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-medium">{{ $inc->incident_number }}</td>
                    <td class="px-4 py-3 text-sm">{{ $inc->incident_datetime->format('M d, Y H:i') }}</td>
                    <td class="px-4 py-3 text-sm">{{ ucfirst(str_replace('_',' ',$inc->type)) }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($inc->severity=='minor') bg-green-100 text-green-800
                            @elseif($inc->severity=='moderate') bg-yellow-100 text-yellow-800
                            @elseif($inc->severity=='major') bg-orange-100 text-orange-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($inc->severity) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm">{{ $inc->site->site_name ?? 'N/A' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($inc->status=='resolved') bg-green-100 text-green-800
                            @elseif($inc->status=='investigating') bg-blue-100 text-blue-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($inc->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3"><a href="{{ route('safety.show', $inc) }}" class="text-blue-500 text-sm">View</a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-gray-500">No incidents - Safety first! 🦺</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $incidents->links() }}</div>
    </div>
</div>
@endsection
