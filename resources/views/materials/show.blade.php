@extends('layouts.app')
@section('title', $material->name)
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-bold">{{ $material->name }}</h2>
                <p class="text-gray-500">{{ $material->material_code }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm 
                @if($material->current_stock > $material->minimum_stock) bg-green-100 text-green-800
                @elseif($material->current_stock > 0) bg-yellow-100 text-yellow-800
                @else bg-red-100 text-red-800 @endif">
                {{ $material->current_stock > $material->minimum_stock ? 'In Stock' : ($material->current_stock > 0 ? 'Low Stock' : 'Out of Stock') }}
            </span>
        </div>
        <div class="grid grid-cols-3 gap-6">
            <div><p class="text-sm text-gray-500">Category</p><p class="font-semibold">{{ $material->category->name ?? 'N/A' }}</p></div>
            <div><p class="text-sm text-gray-500">Current Stock</p><p class="font-semibold">{{ $material->current_stock }} {{ $material->unit }}</p></div>
            <div><p class="text-sm text-gray-500">Minimum Stock</p><p class="font-semibold">{{ $material->minimum_stock }} {{ $material->unit }}</p></div>
            <div><p class="text-sm text-gray-500">Unit Price</p><p class="font-semibold">${{ number_format($material->unit_price, 2) }}</p></div>
            <div><p class="text-sm text-gray-500">Total Value</p><p class="font-semibold">${{ number_format($material->current_stock * $material->unit_price, 2) }}</p></div>
            <div><p class="text-sm text-gray-500">Supplier</p><p class="font-semibold">{{ $material->supplier_name ?? 'N/A' }}</p></div>
        </div>
    </div>
</div>
@endsection
