@extends('layouts.app')
@section('title', 'Materials')
@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-5 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-sm border text-center">
            <div class="text-2xl font-bold">{{ $stats['total'] }}</div><div class="text-xs text-gray-500">Total Items</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['in_stock'] }}</div><div class="text-xs text-gray-500">In Stock</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['low_stock'] }}</div><div class="text-xs text-gray-500">Low Stock</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border text-center">
            <div class="text-2xl font-bold text-red-600">{{ $stats['out_of_stock'] }}</div><div class="text-xs text-gray-500">Out of Stock</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border text-center">
            <div class="text-2xl font-bold text-blue-600">${{ number_format($stats['total_value']) }}</div><div class="text-xs text-gray-500">Total Value</div>
        </div>
    </div>

    <!-- Materials Table -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="p-4 border-b flex justify-between">
            <h2 class="text-lg font-bold">Material Inventory</h2>
            <a href="{{ route('materials.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg text-sm">+ Add Material</a>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($materials as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium">{{ $item->material_code }}</td>
                    <td class="px-6 py-4 text-sm">{{ $item->name }}</td>
                    <td class="px-6 py-4 text-sm">{{ $item->category->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm">
                        <span class="font-bold @if($item->current_stock <= $item->minimum_stock && $item->current_stock > 0) text-yellow-600 @elseif($item->current_stock == 0) text-red-600 @else text-green-600 @endif">
                            {{ $item->current_stock }}
                        </span>
                        <span class="text-gray-500">/ {{ $item->minimum_stock }} {{ $item->unit }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="px-6 py-4 text-sm font-medium">${{ number_format($item->current_stock * $item->unit_price, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($item->current_stock > $item->minimum_stock) bg-green-100 text-green-800
                            @elseif($item->current_stock > 0) bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ $item->current_stock > $item->minimum_stock ? 'In Stock' : ($item->current_stock > 0 ? 'Low Stock' : 'Out') }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-gray-500">No materials found</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $materials->links() }}</div>
    </div>
</div>
@endsection
