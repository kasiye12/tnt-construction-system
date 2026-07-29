<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TNT Construction') | Professional System</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
        
        /* Animations */
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        .slide-in { animation: slideIn 0.3s ease-out; }
        @keyframes slideIn { from { transform: translateX(-20px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    </style>
</head>
<body class="h-full" x-data="{ sidebarOpen: true, darkMode: false }">
    <div class="flex h-full">
        <!-- Professional Sidebar -->
        <aside class="w-64 bg-gray-900 text-white flex-shrink-0 hidden lg:block" 
               :class="{ 'hidden': !sidebarOpen }">
            <div class="p-6 border-b border-gray-800">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <span class="text-xl font-bold">T</span>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold">TNT Construction</h1>
                        <p class="text-xs text-gray-400">Enterprise System</p>
                    </div>
                </div>
            </div>
            
            <nav class="p-4 space-y-1">
                <x-sidebar-link href="/dashboard" icon="📊" :active="request()->is('dashboard')">
                    Dashboard
                </x-sidebar-link>
                <x-sidebar-link href="/projects" icon="🏗️" :active="request()->is('projects*')">
                    Projects
                </x-sidebar-link>
                <x-sidebar-link href="/sites" icon="📍" :active="request()->is('sites*')">
                    Sites
                </x-sidebar-link>
                <x-sidebar-link href="/reports" icon="📝" :active="request()->is('reports*')">
                    Daily Reports
                </x-sidebar-link>
                <x-sidebar-link href="/chat" icon="💬" :active="request()->is('chat*')">
                    Messages
                    <span class="ml-auto bg-red-500 text-xs rounded-full px-2 py-1">3</span>
                </x-sidebar-link>
                <x-sidebar-link href="/users" icon="👥" :active="request()->is('users*')">
                    Users
                </x-sidebar-link>
                <x-sidebar-link href="/safety" icon="🦺" :active="request()->is('safety*')">
                    Safety
                </x-sidebar-link>
            </nav>
            
            <div class="absolute bottom-0 w-64 p-4 border-t border-gray-800">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        {{ strtoupper(substr(Auth::user()->full_name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ Auth::user()->full_name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ Auth::user()->position }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            @include('layouts.partials.professional-header')
            
            <div class="p-6 fade-in">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
