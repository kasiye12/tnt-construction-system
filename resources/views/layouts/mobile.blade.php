<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0ea5e9">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="TNT Con">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TNT Construction')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; padding-bottom: 70px; -webkit-tap-highlight-color: transparent; }
        
        .mobile-header {
            background: linear-gradient(135deg, #0ea5e9, #3b82f6);
            color: white; padding: 16px 20px; position: sticky; top: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 2px 20px rgba(14,165,233,0.3);
        }
        .mobile-header h1 { font-size: 18px; font-weight: 700; }
        .header-actions { display: flex; gap: 12px; }
        .header-actions a { color: white; text-decoration: none; font-size: 20px; position: relative; }
        .badge { position: absolute; top: -6px; right: -8px; background: #ef4444; color: white; font-size: 10px; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; }
        
        .content { padding: 16px; }
        .card { background: white; border-radius: 16px; padding: 20px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
        .card-header { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
        
        .btn { display: block; width: 100%; padding: 14px; border: none; border-radius: 12px; font-size: 15px; font-weight: 600; cursor: pointer; text-align: center; text-decoration: none; transition: all 0.2s; }
        .btn:active { transform: scale(0.98); }
        .btn-primary { background: linear-gradient(135deg, #0ea5e9, #3b82f6); color: white; }
        .btn-success { background: linear-gradient(135deg, #10b981, #059669); color: white; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-outline { background: white; border: 2px solid #0ea5e9; color: #0ea5e9; }
        
        .input-group { margin-bottom: 14px; }
        .input-group label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 5px; }
        .input-group input, .input-group select, .input-group textarea {
            width: 100%; padding: 12px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px;
            font-size: 14px; color: #1e293b; background: #f8fafc; outline: none; transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }
        .input-group input:focus, .input-group select:focus, .input-group textarea:focus { border-color: #0ea5e9; background: white; }
        .input-group textarea { resize: vertical; min-height: 80px; }
        
        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0; background: white; border-top: 1px solid #e2e8f0;
            display: flex; justify-content: space-around; padding: 8px 0 12px; z-index: 100;
            box-shadow: 0 -2px 20px rgba(0,0,0,0.05);
        }
        .bottom-nav a {
            display: flex; flex-direction: column; align-items: center; text-decoration: none;
            color: #94a3b8; font-size: 10px; font-weight: 500; gap: 4px; padding: 4px 12px;
            transition: all 0.2s; border-radius: 12px;
        }
        .bottom-nav a.active { color: #0ea5e9; font-weight: 700; }
        .bottom-nav a .nav-icon { font-size: 22px; }
        
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        
        .stat-value { font-size: 28px; font-weight: 800; color: #1e293b; }
        .stat-label { font-size: 12px; color: #64748b; margin-top: 2px; }
        
        .list-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 0; border-bottom: 1px solid #f1f5f9;
        }
        .list-item:last-child { border-bottom: none; }
        
        .avatar {
            width: 44px; height: 44px; border-radius: 14px; display: flex; align-items: center;
            justify-content: center; color: white; font-weight: 700; font-size: 18px; flex-shrink: 0;
        }
        .avatar-sm { width: 36px; height: 36px; border-radius: 10px; font-size: 14px; }
        
        .tag {
            display: inline-block; padding: 4px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
        }
        .tag-success { background: #d1fae5; color: #065f46; }
        .tag-warning { background: #fef3c7; color: #92400e; }
        .tag-info { background: #dbeafe; color: #1e40af; }
        .tag-danger { background: #fee2e2; color: #991b1b; }
        .tag-gray { background: #f1f5f9; color: #475569; }
        
        .progress-bar { width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #0ea5e9, #3b82f6); border-radius: 3px; }
        
        .alert { padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 12px; }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-in { animation: slideUp 0.3s ease forwards; }
    </style>
</head>
<body>
    <div class="mobile-header">
        <h1>@yield('title', 'TNT Con')</h1>
        <div class="header-actions">
            <a href="/notifications">🔔<span class="badge" id="notif-badge">0</span></a>
        </div>
    </div>
    
    <div class="content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @yield('content')
    </div>
    
    <div class="bottom-nav">
        <a href="/mobile" class="{{ request()->is('mobile') ? 'active' : '' }}">
            <span class="nav-icon">🏠</span>Home
        </a>
        <a href="/mobile/reports" class="{{ request()->is('mobile/reports*') ? 'active' : '' }}">
            <span class="nav-icon">📝</span>Reports
        </a>
        <a href="/mobile/checkin" class="{{ request()->is('mobile/checkin*') ? 'active' : '' }}">
            <span class="nav-icon">📍</span>Check In
        </a>
        <a href="/chat" class="{{ request()->is('chat*') ? 'active' : '' }}">
            <span class="nav-icon">💬</span>Chat
        </a>
        <a href="/mobile/profile" class="{{ request()->is('mobile/profile*') ? 'active' : '' }}">
            <span class="nav-icon">👤</span>Profile
        </a>
    </div>
</body>
</html>
