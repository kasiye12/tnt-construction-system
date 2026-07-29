@props(['id' => 'modal', 'title' => '', 'maxWidth' => 'lg'])

@php
$maxWidths = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
];
@endphp

<div x-data="{ show: false }" 
     x-show="show" 
     x-on:open-modal.window="if ($event.detail.id === '{{ $id }}') show = true"
     x-on:close-modal.window="if ($event.detail.id === '{{ $id }}') show = false"
     x-on:keydown.escape.window="show = false"
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;">
    
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
             x-show="show" 
             x-on:click="show = false"></div>
        
        <div class="relative bg-white rounded-xl shadow-xl {{ $maxWidths[$maxWidth] }} w-full p-6"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100">
            
            @if($title)
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">{{ $title }}</h3>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif
            
            {{ $slot }}
        </div>
    </div>
</div>
