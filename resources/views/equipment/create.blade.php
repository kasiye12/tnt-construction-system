@extends('layouts.app')
@section('title', 'Add Equipment')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <h2 class="text-xl font-bold mb-6">Add New Equipment</h2>
        <form action="{{ route('equipment.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium mb-1">Name *</label><input type="text" name="name" required class="w-full rounded-lg border-gray-300"></div>
                <div><label class="block text-sm font-medium mb-1">Code *</label><input type="text" name="equipment_code" required class="w-full rounded-lg border-gray-300"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium mb-1">Type *</label>
                    <select name="type" required class="w-full rounded-lg border-gray-300">
                        <option value="">Select</option>
                        <option value="excavator">Excavator</option>
                        <option value="bulldozer">Bulldozer</option>
                        <option value="crane">Crane</option>
                        <option value="concrete_mixer">Concrete Mixer</option>
                        <option value="generator">Generator</option>
                        <option value="truck">Truck</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div><label class="block text-sm font-medium mb-1">Status *</label>
                    <select name="status" required class="w-full rounded-lg border-gray-300">
                        <option value="available">Available</option>
                        <option value="in_use">In Use</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="repair">Repair</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium mb-1">Model</label><input type="text" name="model" class="w-full rounded-lg border-gray-300"></div>
                <div><label class="block text-sm font-medium mb-1">Manufacturer</label><input type="text" name="manufacturer" class="w-full rounded-lg border-gray-300"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium mb-1">Hourly Rate ($)</label><input type="number" name="hourly_rate" step="0.01" class="w-full rounded-lg border-gray-300"></div>
                <div><label class="block text-sm font-medium mb-1">Assign to Site</label>
                    <select name="current_site_id" class="w-full rounded-lg border-gray-300">
                        <option value="">None</option>
                        @foreach($sites as $site)
                        <option value="{{ $site->id }}">{{ $site->site_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div><label class="block text-sm font-medium mb-1">Notes</label><textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300"></textarea></div>
            <div class="flex gap-3">
                <a href="{{ route('equipment.index') }}" class="px-4 py-2 border rounded-lg">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg">Save Equipment</button>
            </div>
        </form>
    </div>
</div>
@endsection
