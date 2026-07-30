<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - TNT Construction</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh; display: flex;
        }
        .login-container { display: flex; width: 100%; }
        
        /* Left Branding Panel */
        .login-left {
            flex: 1; display: none; 
            background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 50%, #8b5cf6 100%);
            position: relative; overflow: hidden; align-items: center; justify-content: center;
        }
        @media (min-width: 1024px) { .login-left { display: flex; } }
        .login-left-content { position: relative; z-index: 1; padding: 80px; color: white; }
        .login-left-content h1 { font-size: 38px; font-weight: 800; margin-bottom: 16px; }
        .login-left-content > p { font-size: 16px; opacity: 0.9; margin-bottom: 40px; }
        .feature { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
        .feature-icon { width: 44px; height: 44px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
        .feature h3 { font-size: 15px; font-weight: 600; }
        .feature p { font-size: 13px; opacity: 0.8; margin: 0; }
        
        /* Right Login Form */
        .login-right { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px; }
        .form-container { width: 100%; max-width: 420px; }
        
        .logo { text-align: center; margin-bottom: 36px; }
        .logo-icon {
            width: 60px; height: 60px; background: linear-gradient(135deg, #0ea5e9, #8b5cf6);
            border-radius: 18px; display: inline-flex; align-items: center; justify-content: center;
            font-size: 26px; font-weight: 800; color: white; margin-bottom: 12px;
            box-shadow: 0 10px 40px rgba(14,165,233,0.3);
        }
        .logo h2 { font-size: 22px; font-weight: 700; color: white; }
        .logo p { font-size: 13px; color: #94a3b8; margin-top: 4px; }
        
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 500; color: #cbd5e1; margin-bottom: 6px; }
        .form-group input {
            width: 100%; padding: 13px 16px; background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1); border-radius: 12px;
            font-size: 14px; color: white; outline: none; transition: all 0.3s;
        }
        .form-group input:focus { border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,0.1); }
        .form-group input::placeholder { color: #475569; }
        
        .remember-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .remember-row label { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #94a3b8; cursor: pointer; }
        .remember-row a { font-size: 13px; color: #0ea5e9; text-decoration: none; }
        
        .btn-login {
            width: 100%; padding: 15px; background: linear-gradient(135deg, #0ea5e9, #3b82f6);
            color: white; border: none; border-radius: 12px; font-size: 15px; font-weight: 600;
            cursor: pointer; transition: all 0.3s; letter-spacing: 0.3px;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 10px 40px rgba(14,165,233,0.4); }
        
        .alert { padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 16px; text-align: center; }
        .alert-error { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; }
        
        .divider { display: flex; align-items: center; gap: 14px; margin: 24px 0; color: #475569; font-size: 13px; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: rgba(255,255,255,0.08); }
        
        .btn-telegram {
            width: 100%; padding: 13px; background: rgba(0,136,204,0.12);
            border: 1px solid rgba(0,136,204,0.25); color: #38bdf8; border-radius: 12px;
            font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.3s;
            display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none;
        }
        .btn-telegram:hover { background: rgba(0,136,204,0.2); }
        
        .footer-text { text-align: center; margin-top: 28px; font-size: 13px; color: #64748b; }
        .footer-text a { color: #0ea5e9; text-decoration: none; }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Branding -->
        <div class="login-left">
            <div class="login-left-content">
                <h1>Build Smarter with TNT Construction</h1>
                <p>Complete construction management platform for your entire team.</p>
                <div class="feature"><div class="feature-icon">🏗️</div><div><h3>Project Management</h3><p>Track progress across all construction sites</p></div></div>
                <div class="feature"><div class="feature-icon">📊</div><div><h3>Real-time Reports</h3><p>Daily reports with instant approval workflow</p></div></div>
                <div class="feature"><div class="feature-icon">💬</div><div><h3>Team Communication</h3><p>Chat, share files, and stay connected</p></div></div>
                <div class="feature"><div class="feature-icon">📱</div><div><h3>Mobile Ready</h3><p>Access from any device, anywhere</p></div></div>
            </div>
        </div>
        
        <!-- Right Form -->
        <div class="login-right">
            <div class="form-container">
                <div class="logo">
                    <div class="logo-icon">T</div>
                    <h2>Welcome Back</h2>
                    <p>Sign in to your account</p>
                </div>
                
                @if($errors->any())
                    <div class="alert alert-error">{{ $errors->first() }}</div>
                @endif
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@tntconstruction.com" required autofocus>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                    <div class="remember-row">
                        <label><input type="checkbox" name="remember"> Remember me</label>
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}">Forgot password?</a>
                        @endif
                    </div>
                    <button type="submit" class="btn-login">Sign In</button>
                </form>
                
                <div class="divider">or continue with</div>
                
                <!-- Fixed: Links to the correct Telegram login page -->
                <a href="{{ url('/auth/telegram') }}" class="btn-telegram">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0z"/></svg>
                    Continue with Telegram
                </a>
                
                <p class="footer-text">
                    Don't have an account? <a href="{{ route('register') }}">Create one</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
