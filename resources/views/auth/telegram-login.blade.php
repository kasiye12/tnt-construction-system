<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login with Telegram - TNT Construction</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;
        }
        .card {
            background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px; padding: 48px; width: 100%; max-width: 440px; text-align: center;
        }
        .icon {
            width: 80px; height: 80px; background: linear-gradient(135deg, #0088cc, #00a8e8);
            border-radius: 24px; display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 24px; box-shadow: 0 20px 60px rgba(0,136,204,0.3);
        }
        .icon svg { width: 44px; height: 44px; fill: white; }
        h2 { color: white; font-size: 24px; font-weight: 700; margin-bottom: 8px; }
        p { color: #94a3b8; font-size: 14px; margin-bottom: 24px; line-height: 1.6; }
        
        .btn {
            display: inline-flex; align-items: center; gap: 10px;
            background: linear-gradient(135deg, #0088cc, #00a8e8);
            color: white; text-decoration: none; padding: 16px 32px;
            border-radius: 14px; font-weight: 600; font-size: 16px;
            transition: all 0.3s; margin-bottom: 16px;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 40px rgba(0,136,204,0.4); }
        
        .btn-outline {
            display: block; color: #94a3b8; text-decoration: none;
            font-size: 14px; transition: color 0.2s;
        }
        .btn-outline:hover { color: white; }
        
        .steps { text-align: left; margin: 20px 0; }
        .step {
            display: flex; align-items: center; gap: 12px; padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .step-num {
            width: 30px; height: 30px; background: #0088cc;
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: 13px; flex-shrink: 0;
        }
        .step-text { color: #cbd5e1; font-size: 13px; }
        .step-text strong { color: white; }
        
        .bot-tag {
            background: rgba(0,136,204,0.15); border: 1px solid rgba(0,136,204,0.3);
            border-radius: 12px; padding: 12px 20px; margin: 16px 0;
            color: #38bdf8; font-size: 16px; font-weight: 600;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.18.717-.962 4.085-1.362 5.424-.168.597-.5.797-.82.817-.697.064-1.226-.461-1.901-.903-1.056-.692-1.653-1.123-2.678-1.799-1.185-.781-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.329-.913.489-1.302.481-.428-.009-1.252-.242-1.865-.441-.752-.244-1.349-.374-1.297-.789.027-.216.324-.437.893-.663 3.498-1.524 5.831-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635.099-.002.321.023.465.139.121.098.154.229.171.322.016.093.036.306.02.472z"/></svg>
        </div>
        
        <h2>Login with Telegram</h2>
        <p>Open the bot on Telegram and send <strong>/start</strong> to authenticate</p>
        
        <div class="bot-tag">@TNTConstructionTradingBot</div>
        
        <div class="steps">
            <div class="step">
                <div class="step-num">1</div>
                <div class="step-text">Open <strong>Telegram</strong> app</div>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <div class="step-text">Search <strong>@TNTConstructionTradingBot</strong></div>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <div class="step-text">Send <strong>/start</strong> to register</div>
            </div>
            <div class="step">
                <div class="step-num">4</div>
                <div class="step-text">Use <strong>/checkin</strong> or <strong>/report</strong></div>
            </div>
        </div>
        
        <a href="https://t.me/TNTConstructionTradingBot" target="_blank" class="btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0z"/></svg>
            Open @TNTConstructionTradingBot
        </a>
        
        <a href="{{ route('login') }}" class="btn-outline">← Back to Email Login</a>
    </div>
</body>
</html>
