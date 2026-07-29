@extends('layouts.app')

@section('title', 'Equipment')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b flex justify-between">
        <h2 class="text-2xl font-bold">Equipment & Machinery</h2>
        <button class="bg-blue-500 text-white px-4 py-2 rounded">+ Add Equipment</button>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($equipment as $item)
            <div class="border rounded-lg p-4 hover:shadow-lg transition">
                <div class="text-3xl mb-3">🚜</div>
                <h3 class="font-bold text-lg">{{ $item->name }}</h3>
                <p class="text-sm text-gray-600">{{ $item->equipment_code }}</p>
                <div class="mt-3 flex justify-between items-center">
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                        {{ $item->status }}
                    </span>
                    <span class="text-sm text-gray-500">{{ $item->type }}</span>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-12 text-gray-500">
                <p class="text-lg">No equipment registered</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
