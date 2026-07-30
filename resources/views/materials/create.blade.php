@extends('layouts.app')

@section('title', 'Add Material')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <h2 class="text-xl font-bold mb-6">📦 Add New Material</h2>
        
        <form action="{{ route('materials.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Material Name *</label>
                    <input type="text" name="name" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" value="{{ old('name') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Material Code *</label>
                    <input type="text" name="material_code" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" value="{{ old('material_code') }}">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" class="w-full rounded-lg border-gray-300">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit *</label>
                    <select name="unit" required class="w-full rounded-lg border-gray-300">
                        <option value="pieces">Pieces</option>
                        <option value="bags">Bags</option>
                        <option value="tons">Tons</option>
                        <option value="kg">Kilograms</option>
                        <option value="liters">Liters</option>
                        <option value="meters">Meters</option>
                        <option value="rolls">Rolls</option>
                        <option value="bundles">Bundles</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Stock *</label>
                    <input type="number" name="current_stock" required min="0" step="0.01" class="w-full rounded-lg border-gray-300" value="{{ old('current_stock', 0) }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Stock *</label>
                    <input type="number" name="minimum_stock" required min="0" step="0.01" class="w-full rounded-lg border-gray-300" value="{{ old('minimum_stock', 10) }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price ($)</label>
                    <input type="number" name="unit_price" step="0.01" min="0" class="w-full rounded-lg border-gray-300" value="{{ old('unit_price') }}">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Name</label>
                    <input type="text" name="supplier_name" class="w-full rounded-lg border-gray-300" value="{{ old('supplier_name') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Storage Location</label>
                    <input type="text" name="storage_location" class="w-full rounded-lg border-gray-300" value="{{ old('storage_location') }}">
                </div>
            </div>
            
            <div class="flex gap-3 pt-4">
                <a href="{{ route('materials.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Save Material</button>
            </div>
        </form>
    </div>
</div>
@endsection
