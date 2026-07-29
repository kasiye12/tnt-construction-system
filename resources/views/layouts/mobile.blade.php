<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1a56db">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="TNT Con">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/pwa/icon-192.png">
    <title>@yield('title', 'TNT Construction')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Mobile-optimized styles */
        * { -webkit-tap-highlight-color: transparent; }
        body { 
            padding-bottom: 60px;
            -webkit-user-select: none;
            user-select: none;
        }
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-around;
            padding: 8px 0;
            z-index: 50;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        }
        .bottom-nav a {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #6b7280;
            font-size: 11px;
            padding: 4px 12px;
            transition: color 0.2s;
        }
        .bottom-nav a.active {
            color: #1a56db;
        }
        .bottom-nav a .icon {
            font-size: 20px;
            margin-bottom: 2px;
        }
        .mobile-header {
            background: #1a56db;
            color: white;
            padding: 12px 16px;
            position: sticky;
            top: 0;
            z-index: 40;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            border: none;
            cursor: pointer;
            width: 100%;
        }
        .btn-primary { background: #1a56db; color: white; }
        .btn-success { background: #059669; color: white; }
        .btn-danger { background: #dc2626; color: white; }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 16px;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
    <!-- Mobile Header -->
    <div class="mobile-header">
        <div class="flex justify-between items-center">
            <h1 class="text-lg font-bold">@yield('title', 'TNT Con')</h1>
            <div class="flex space-x-3">
                <a href="/notifications" class="text-white relative">
                    🔔
                    <span id="mobile-notif-badge" class="absolute -top-1 -right-1 bg-red-500 text-xs rounded-full w-4 h-4 flex items-center justify-center hidden">0</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="p-4">
        @yield('content')
    </main>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <a href="/mobile" class="{{ request()->is('mobile') ? 'active' : '' }}">
            <span class="icon">🏠</span>
            Home
        </a>
        <a href="/mobile/reports" class="{{ request()->is('mobile/reports*') ? 'active' : '' }}">
            <span class="icon">📝</span>
            Reports
        </a>
        <a href="/mobile/checkin" class="{{ request()->is('mobile/checkin*') ? 'active' : '' }}">
            <span class="icon">📍</span>
            Check In
        </a>
        <a href="/mobile/chat" class="{{ request()->is('mobile/chat*') ? 'active' : '' }}">
            <span class="icon">💬</span>
            Chat
        </a>
        <a href="/mobile/profile" class="{{ request()->is('mobile/profile*') ? 'active' : '' }}">
            <span class="icon">👤</span>
            Profile
        </a>
    </div>

    <script>
    // Register service worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js');
    }

    // Offline storage helper
    const offlineDB = {
        save: function(key, data) {
            localStorage.setItem('offline_' + key, JSON.stringify(data));
        },
        get: function(key) {
            const item = localStorage.getItem('offline_' + key);
            return item ? JSON.parse(item) : null;
        },
        remove: function(key) {
            localStorage.removeItem('offline_' + key);
        },
        getAll: function() {
            const items = {};
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                if (key.startsWith('offline_')) {
                    items[key] = JSON.parse(localStorage.getItem(key));
                }
            }
            return items;
        }
    };

    // Online/Offline detection
    window.addEventListener('online', () => {
        document.body.classList.remove('offline');
        syncOfflineData();
    });
    
    window.addEventListener('offline', () => {
        document.body.classList.add('offline');
    });

    async function syncOfflineData() {
        const pendingData = offlineDB.getAll();
        for (const [key, data] of Object.entries(pendingData)) {
            try {
                await fetch('/api/sync', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                offlineDB.remove(key.replace('offline_', ''));
            } catch (error) {
                console.error('Sync failed for:', key);
            }
        }
    }
    </script>
</body>
</html>
