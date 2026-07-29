@props(['type' => 'info', 'dismissible' => true])

@php
$types = [
    'info' => 'bg-blue-50 text-blue-800 border-blue-200',
    'success' => 'bg-green-50 text-green-800 border-green-200',
    'warning' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
    'error' => 'bg-red-50 text-red-800 border-red-200',
    'danger' => 'bg-red-50 text-red-800 border-red-200',
];
@endphp

<div x-data="{ show: true }" x-show="show" 
     class="border rounded-lg p-4 {{ $types[$type] }} mb-4">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            {{ $slot }}
        </div>
        @if($dismissible)
            <button @click="show = false" class="ml-3 hover:opacity-75">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        @endif
    </div>
</div>
