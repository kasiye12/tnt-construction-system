@props(['color' => 'gray', 'size' => 'sm'])

@php
$colors = [
    'gray' => 'bg-gray-100 text-gray-800',
    'red' => 'bg-red-100 text-red-800',
    'green' => 'bg-green-100 text-green-800',
    'blue' => 'bg-blue-100 text-blue-800',
    'yellow' => 'bg-yellow-100 text-yellow-800',
    'orange' => 'bg-orange-100 text-orange-800',
    'purple' => 'bg-purple-100 text-purple-800',
];

$sizes = [
    'xs' => 'px-2 py-0.5 text-xs',
    'sm' => 'px-2.5 py-0.5 text-xs',
    'md' => 'px-3 py-1 text-sm',
];
@endphp

<span class="inline-flex items-center font-medium rounded-full {{ $colors[$color] }} {{ $sizes[$size] }}">
    {{ $slot }}
</span>
