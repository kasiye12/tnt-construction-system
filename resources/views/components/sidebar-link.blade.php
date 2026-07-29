@props(['href' => '#', 'icon' => null, 'active' => false])

<a href="{{ $href }}" 
   class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 group
          {{ $active ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
    <span class="mr-3 text-lg">{{ $icon }}</span>
    <span class="flex-1 text-sm font-medium">{{ $slot }}</span>
    @if($active)
        <span class="w-1.5 h-6 bg-white rounded-full ml-2"></span>
    @endif
</a>
