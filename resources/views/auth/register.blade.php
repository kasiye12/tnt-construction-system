<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TNT Construction</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #0f172a; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 24px; padding: 40px; width: 100%; max-width: 480px; }
        .logo { text-align: center; margin-bottom: 28px; }
        .logo-icon { width: 56px; height: 56px; background: linear-gradient(135deg, #0ea5e9, #8b5cf6); border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 800; color: white; margin-bottom: 10px; }
        h2 { color: white; font-size: 20px; } p { color: #94a3b8; font-size: 13px; margin-top: 4px; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 12px; font-weight: 500; color: #cbd5e1; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 12px 14px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; font-size: 13px; color: white; outline: none; transition: all 0.3s; }
        .form-group input:focus { border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,0.1); }
        .btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #0ea5e9, #3b82f6); color: white; border: none; border-radius: 12px; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 8px; }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 10px 40px rgba(14,165,233,0.3); }
        .error { color: #fca5a5; font-size: 11px; margin-top: 3px; }
        .footer { text-align: center; margin-top: 20px; font-size: 13px; color: #64748b; }
        .footer a { color: #0ea5e9; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo"><div class="logo-icon">T</div><h2>Create Account</h2><p>Join TNT Construction System</p></div>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group"><label>Full Name</label><input type="text" name="full_name" value="{{ old('full_name') }}" placeholder="John Doe" required>@error('full_name')<p class="error">{{ $message }}</p>@enderror</div>
            <div class="row">
                <div class="form-group"><label>Email</label><input type="email" name="email" value="{{ old('email') }}" placeholder="you@tnt.com" required>@error('email')<p class="error">{{ $message }}</p>@enderror</div>
                <div class="form-group"><label>Phone</label><input type="text" name="phone_number" value="{{ old('phone_number') }}" placeholder="+251..." required>@error('phone_number')<p class="error">{{ $message }}</p>@enderror</div>
            </div>
            <div class="form-group"><label>Password</label><input type="password" name="password" placeholder="Min 8 characters" required>@error('password')<p class="error">{{ $message }}</p>@enderror</div>
            <div class="form-group"><label>Confirm Password</label><input type="password" name="password_confirmation" placeholder="Re-enter password" required></div>
            <button type="submit" class="btn">Create Account</button>
        </form>
        <p class="footer">Already have an account? <a href="{{ route('login') }}">Sign In</a></p>
    </div>
</body>
</html>
