<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - TNT Construction</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #0f172a, #1e293b); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 24px; padding: 40px; width: 100%; max-width: 420px; text-align: center; }
        .icon { font-size: 48px; margin-bottom: 16px; }
        h2 { color: white; font-size: 20px; margin-bottom: 8px; }
        p { color: #94a3b8; font-size: 13px; margin-bottom: 24px; }
        input { width: 100%; padding: 13px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; font-size: 14px; color: white; outline: none; margin-bottom: 16px; }
        input:focus { border-color: #0ea5e9; }
        .btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #0ea5e9, #3b82f6); color: white; border: none; border-radius: 12px; font-size: 15px; font-weight: 600; cursor: pointer; }
        .link { color: #94a3b8; font-size: 13px; text-decoration: none; display: block; margin-top: 16px; }
        .link:hover { color: white; }
        .alert { padding: 12px; border-radius: 10px; font-size: 13px; margin-bottom: 16px; }
        .alert-success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); color: #6ee7b7; }
        .alert-error { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">🔑</div>
        <h2>Forgot Password?</h2>
        <p>Enter your email and we'll send you a reset link.</p>
        
        @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
        
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" class="btn">Send Reset Link</button>
        </form>
        <a href="{{ route('login') }}" class="link">← Back to Login</a>
    </div>
</body>
</html>
