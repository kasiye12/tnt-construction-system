@extends('layouts.app')

@section('title', 'Equipment')

@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-5 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-sm border text-center">
            <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
            <div class="text-xs text-gray-500">Total</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['available'] }}</div>
            <div class="text-xs text-gray-500">Available</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['in_use'] }}</div>
            <div class="text-xs text-gray-500">In Use</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['maintenance'] }}</div>
            <div class="text-xs text-gray-500">Maintenance</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border text-center">
            <div class="text-2xl font-bold text-red-600">{{ $stats['repair'] }}</div>
            <div class="text-xs text-gray-500">Repair</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl p-4 shadow-sm border">
        <form class="flex gap-4">
            <select name="status" class="rounded border-gray-300 text-sm">
                <option value="">All Status</option>
                <option value="available">Available</option>
                <option value="in_use">In Use</option>
                <option value="maintenance">Maintenance</option>
            </select>
            <select name="site_id" class="rounded border-gray-300 text-sm">
                <option value="">All Sites</option>
                @foreach($sites as $site)
                <option value="{{ $site->id }}">{{ $site->site_name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm">Filter</button>
            <a href="{{ route('equipment.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg text-sm">+ Add</a>
        </form>
    </div>

    <!-- Equipment Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($equipment as $item)
        <div class="bg-white rounded-xl shadow-sm border p-6 hover:shadow-md transition">
            <div class="flex justify-between items-start mb-4">
                <div class="text-4xl">
                    @if($item->type == 'excavator') 🚜
                    @elseif($item->type == 'crane') 🏗️
                    @elseif($item->type == 'truck') 🚛
                    @elseif($item->type == 'generator') ⚡
                    @elseif($item->type == 'concrete_mixer') 🏭
                    @else 🔧
                    @endif
                </div>
                <span class="px-2 py-1 text-xs rounded-full 
                    @if($item->status == 'available') bg-green-100 text-green-800
                    @elseif($item->status == 'in_use') bg-blue-100 text-blue-800
                    @elseif($item->status == 'maintenance') bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                </span>
            </div>
            <h3 class="font-bold text-lg">{{ $item->name }}</h3>
            <p class="text-sm text-gray-500">{{ $item->equipment_code }}</p>
            <div class="mt-3 space-y-1 text-sm">
                <p><span class="text-gray-500">Type:</span> {{ ucfirst(str_replace('_', ' ', $item->type)) }}</p>
                <p><span class="text-gray-500">Site:</span> {{ $item->currentSite->site_name ?? 'Not assigned' }}</p>
                @if($item->hourly_rate)
                <p><span class="text-gray-500">Rate:</span> ${{ number_format($item->hourly_rate) }}/hr</p>
                @endif
            </div>
            <div class="mt-4 flex gap-2">
                <a href="{{ route('equipment.show', $item) }}" class="text-blue-500 text-sm">View</a>
                <a href="{{ route('equipment.edit', $item) }}" class="text-green-500 text-sm">Edit</a>
                <form action="{{ route('equipment.destroy', $item) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-500 text-sm" onclick="return confirm('Delete?')">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12 text-gray-500">No equipment found</div>
        @endforelse
    </div>
</div>
@endsection
