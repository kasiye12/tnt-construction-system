@extends('layouts.app')

@section('title', 'Materials')

@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total Materials</p>
            <p class="text-2xl font-bold">156</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Low Stock</p>
            <p class="text-2xl font-bold text-yellow-600">12</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Out of Stock</p>
            <p class="text-2xl font-bold text-red-600">3</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Orders Pending</p>
            <p class="text-2xl font-bold text-blue-600">8</p>
        </div>
    </div>

    <!-- Materials Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between">
            <h2 class="text-2xl font-bold">Material Inventory</h2>
            <button class="bg-green-500 text-white px-4 py-2 rounded">+ Add Material</button>
        </div>
        <div class="p-6">
            <table class="w-full">
                <thead>
                    <tr class="text-left bg-gray-50">
                        <th class="px-4 py-2">Code</th>
                        <th class="px-4 py-2">Material Name</th>
                        <th class="px-4 py-2">Category</th>
                        <th class="px-4 py-2">Stock</th>
                        <th class="px-4 py-2">Unit</th>
                        <th class="px-4 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="px-4 py-3">MAT-001</td>
                        <td class="px-4 py-3">Cement</td>
                        <td class="px-4 py-3">Construction</td>
                        <td class="px-4 py-3">500</td>
                        <td class="px-4 py-3">Bags</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">In Stock</span>
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-4 py-3">MAT-002</td>
                        <td class="px-4 py-3">Steel Rebar</td>
                        <td class="px-4 py-3">Metal</td>
                        <td class="px-4 py-3">150</td>
                        <td class="px-4 py-3">Tons</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Low Stock</span>
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-4 py-3">MAT-003</td>
                        <td class="px-4 py-3">Sand</td>
                        <td class="px-4 py-3">Aggregate</td>
                        <td class="px-4 py-3">1000</td>
                        <td class="px-4 py-3">Tons</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">In Stock</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
