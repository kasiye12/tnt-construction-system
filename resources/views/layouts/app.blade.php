<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#0ea5e9">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="TNT Con">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <title>@yield('title', 'Dashboard') - TNT Construction</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary: #0ea5e9;
            --primary-dark: #0284c7;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --text-light: #94a3b8;
            --bg-main: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-main);
            color: #1e293b;
        }
        
        /* Sidebar Styles */
        .sidebar {
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            border-right: 1px solid rgba(255,255,255,0.05);
        }
        
        .sidebar-link {
            position: relative;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .sidebar-link:hover {
            background: rgba(14, 165, 233, 0.1);
            border-left-color: var(--primary);
        }
        
        .sidebar-link.active {
            background: rgba(14, 165, 233, 0.15);
            border-left-color: var(--primary);
            color: var(--primary) !important;
        }
        
        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--primary);
            border-radius: 0 3px 3px 0;
        }
        
        /* Card Styles */
        .card {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }
        
        .stat-card {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border);
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        }
        
        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);
            color: white;
            border: none;
            padding: 0.625rem 1.25rem;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        
        /* Badge Styles */
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        
        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: #f8fafc;
        }
        
        th {
            padding: 0.875rem 1.5rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
        }
        
        td {
            padding: 0.875rem 1.5rem;
            border-top: 1px solid #f1f5f9;
        }
        
        tr:hover {
            background: #f8fafc;
        }
        
        /* Progress Bar */
        .progress-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #0ea5e9, #3b82f6);
            border-radius: 4px;
            transition: width 0.6s ease;
        }
        
        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-in {
            animation: fadeInUp 0.5s ease forwards;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Header */
        .header {
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
        }
        
        /* Logo */
        .logo {
            background: linear-gradient(135deg, #0ea5e9, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="h-screen overflow-hidden">
    <div class="flex h-full">
        <!-- Sidebar -->
        <aside class="sidebar w-72 flex-shrink-0 flex flex-col">
            <!-- Logo -->
            <div class="p-6 border-b border-white/5">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" 
                         style="background: linear-gradient(135deg, #0ea5e9, #8b5cf6);">
                        <span class="text-2xl font-bold text-white">T</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">TNT Construction</h1>
                        <p class="text-xs" style="color: var(--text-light);">Enterprise System</p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
                <p class="px-3 text-xs font-semibold uppercase tracking-wider mb-3" style="color: var(--text-light);">
                    Main Menu
                </p>
                
                <a href="{{ url('/dashboard') }}" 
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-300 {{ request()->is('dashboard') ? 'active' : '' }}">
                    <span class="text-lg mr-3">📊</span>
                    <span class="text-sm font-medium">Dashboard</span>
                    @if(request()->is('dashboard'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                    @endif
                </a>
                
                <a href="{{ url('/projects') }}" 
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-300 {{ request()->is('projects*') ? 'active' : '' }}">
                    <span class="text-lg mr-3">🏗️</span>
                    <span class="text-sm font-medium">Projects</span>
                </a>
                
                <a href="{{ url('/sites') }}" 
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-300 {{ request()->is('sites*') ? 'active' : '' }}">
                    <span class="text-lg mr-3">📍</span>
                    <span class="text-sm font-medium">Sites</span>
                </a>
                
                <a href="{{ url('/reports') }}" 
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-300 {{ request()->is('reports*') ? 'active' : '' }}">
                    <span class="text-lg mr-3">📝</span>
                    <span class="text-sm font-medium">Daily Reports</span>
                </a>
                
                <p class="px-3 text-xs font-semibold uppercase tracking-wider mt-8 mb-3" style="color: var(--text-light);">
                    Communication
                </p>
                
                <a href="{{ url('/chat') }}" 
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-300 {{ request()->is('chat*') ? 'active' : '' }}">
                    <span class="text-lg mr-3">💬</span>
                    <span class="text-sm font-medium">Messages</span>
                    <span class="ml-auto px-2 py-0.5 text-xs rounded-full bg-red-500 text-white">3</span>
                </a>
                
                <a href="{{ url('/notifications') }}" 
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-300 {{ request()->is('notifications*') ? 'active' : '' }}">
                    <span class="text-lg mr-3">🔔</span>
                    <span class="text-sm font-medium">Notifications</span>
                </a>
                
                <p class="px-3 text-xs font-semibold uppercase tracking-wider mt-8 mb-3" style="color: var(--text-light);">
                    Management
                </p>
                
                <a href="{{ url('/users') }}" 
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-300 {{ request()->is('users*') ? 'active' : '' }}">
                    <span class="text-lg mr-3">👥</span>
                    <span class="text-sm font-medium">Users</span>
                </a>
                
                <a href="{{ url('/equipment') }}" 
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-300 {{ request()->is('equipment*') ? 'active' : '' }}">
                    <span class="text-lg mr-3">🚜</span>
                    <span class="text-sm font-medium">Equipment</span>
                </a>
                
                <a href="{{ url('/materials') }}" 
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-300 {{ request()->is('materials*') ? 'active' : '' }}">
                    <span class="text-lg mr-3">📦</span>
                    <span class="text-sm font-medium">Materials</span>
                </a>
                
                <a href="{{ url('/safety') }}" 
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-300 {{ request()->is('safety*') ? 'active' : '' }}">
                    <span class="text-lg mr-3">🦺</span>
                    <span class="text-sm font-medium">Safety</span>
                </a>
                
                <a href="{{ url('/reports/export') }}" 
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl text-gray-300 {{ request()->is('reports/export*') ? 'active' : '' }}">
                    <span class="text-lg mr-3">📥</span>
                    <span class="text-sm font-medium">Export Reports</span>
                </a>
            </nav>
            
            <!-- User Info -->
            <div class="p-4 border-t border-white/5">
                <div class="flex items-center space-x-3 bg-white/5 rounded-xl p-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold"
                         style="background: linear-gradient(135deg, #10b981, #059669);">
                        {{ strtoupper(substr(Auth::user()->full_name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->full_name ?? 'User' }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ Auth::user()->position ?? 'Staff' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-red-400 transition" title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="header px-8 py-5 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">@yield('title', 'Dashboard')</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ date('l, F d, Y') }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    <button class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-8">
                @if(session('success'))
                    <div class="flex items-center space-x-2 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6 animate-in">
                        <span>✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="flex items-center space-x-2 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 animate-in">
                        <span>❌</span>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>
