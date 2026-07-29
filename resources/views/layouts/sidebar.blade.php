<div class="w-64 bg-gray-800 text-white flex-shrink-0 overflow-y-auto">
    <div class="p-4 border-b border-gray-700">
        <h1 class="text-xl font-bold">🏗️ TNT Construction</h1>
    </div>
    
    <nav class="mt-4">
        <div class="px-4 mb-2 text-xs text-gray-400 uppercase">Main Menu</div>
        
        <a href="{{ route('dashboard') }}" 
           class="flex items-center px-4 py-3 {{ request()->routeIs('dashboard') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }} hover:bg-gray-700">
            <span class="mr-3">📊</span>
            Dashboard
        </a>
        
        <a href="{{ route('projects.index') }}" 
           class="flex items-center px-4 py-3 {{ request()->routeIs('projects.*') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }} hover:bg-gray-700">
            <span class="mr-3">🏗️</span>
            Projects
        </a>
        
        <a href="{{ route('sites.index') }}" 
           class="flex items-center px-4 py-3 {{ request()->routeIs('sites.*') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }} hover:bg-gray-700">
            <span class="mr-3">📍</span>
            Sites
        </a>
        
        <a href="{{ route('reports.index') }}" 
           class="flex items-center px-4 py-3 {{ request()->routeIs('reports.*') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }} hover:bg-gray-700">
            <span class="mr-3">📝</span>
            Daily Reports
        </a>
        
        <div class="px-4 mt-6 mb-2 text-xs text-gray-400 uppercase">Communication</div>
        
        <a href="{{ route('chat.index') }}" 
           class="flex items-center px-4 py-3 {{ request()->routeIs('chat.*') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }} hover:bg-gray-700">
            <span class="mr-3">💬</span>
            Messages
        </a>
        
        <div class="px-4 mt-6 mb-2 text-xs text-gray-400 uppercase">Management</div>
        
        <a href="{{ route('safety.index') }}" 
           class="flex items-center px-4 py-3 {{ request()->routeIs('safety.*') ? 'bg-gray-700 border-l-4 border-red-500' : '' }} hover:bg-gray-700">
            <span class="mr-3">🦺</span>
            Safety
        </a>
        
        <a href="{{ route('users.index') }}" 
           class="flex items-center px-4 py-3 {{ request()->routeIs('users.*') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }} hover:bg-gray-700">
            <span class="mr-3">👥</span>
            Users
        </a>
        
        <a href="{{ route('equipment.index') }}" 
           class="flex items-center px-4 py-3 {{ request()->routeIs('equipment.*') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }} hover:bg-gray-700">
            <span class="mr-3">🚜</span>
            Equipment
        </a>
        
        <a href="{{ route('materials.index') }}" 
           class="flex items-center px-4 py-3 {{ request()->routeIs('materials.*') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }} hover:bg-gray-700">
            <span class="mr-3">📦</span>
            Materials
        </a>
    </nav>
    
    <div class="absolute bottom-0 w-64 p-4 border-t border-gray-700">
        <div class="text-xs text-gray-400">© 2024 TNT Construction</div>
        <div class="text-xs text-gray-500">v1.0.0</div>
    </div>
</div>

<!-- Add these new menu items -->
<div class="px-4 mt-6 mb-2 text-xs text-gray-400 uppercase">Tools</div>

<a href="{{ route('reports.export') }}" 
   class="flex items-center px-4 py-3 hover:bg-gray-700">
    <span class="mr-3">📥</span>
    Export Reports
</a>

<a href="{{ route('notifications.index') }}" 
   class="flex items-center px-4 py-3 {{ request()->routeIs('notifications.*') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }} hover:bg-gray-700">
    <span class="mr-3">🔔</span>
    Notifications
    <span id="notification-badge" class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1 hidden">0</span>
</a>
