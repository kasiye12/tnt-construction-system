@props([
    'title',
    'value',
    'icon' => null,
    'change' => null,
    'color' => 'blue',
])

@php
$colors = [
    'blue' => 'bg-blue-50 text-blue-600',
    'green' => 'bg-green-50 text-green-600',
    'red' => 'bg-red-50 text-red-600',
    'yellow' => 'bg-yellow-50 text-yellow-600',
    'purple' => 'bg-purple-50 text-purple-600',
    'orange' => 'bg-orange-50 text-orange-600',
];
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600">{{ $title }}</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $value }}</p>
            @if($change)
                <p class="text-sm mt-2 {{ $change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $change >= 0 ? '↑' : '↓' }} {{ abs($change) }}%
                    <span class="text-gray-500">vs last month</span>
                </p>
            @endif
        </div>
        @if($icon)
            <div class="p-3 rounded-lg {{ $colors[$color] }}">
                <span class="text-2xl">{{ $icon }}</span>
            </div>
        @endif
    </div>
</div>
