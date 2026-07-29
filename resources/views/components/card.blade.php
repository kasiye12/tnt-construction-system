@props(['padding' => true, 'header' => null, 'footer' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden']) }}>
    @if($header)
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
            <h3 class="text-lg font-semibold text-gray-900">{{ $header }}</h3>
        </div>
    @endif
    
    <div class="{{ $padding ? 'p-6' : '' }}">
        {{ $slot }}
    </div>
    
    @if($footer)
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
            {{ $footer }}
        </div>
    @endif
</div>
