<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TelegramAuthController extends Controller
{
    /**
     * Show Telegram login page
     */
    public function showLoginForm()
    {
        return view('auth.telegram-login');
    }

    /**
     * Handle Telegram authentication
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'first_name' => 'required',
            'last_name' => 'nullable',
            'username' => 'nullable',
            'photo_url' => 'nullable',
            'auth_date' => 'required',
            'hash' => 'required',
        ]);

        // Verify Telegram data
        if (!$this->verifyTelegramData($request->all())) {
            return redirect()->route('login')
                ->with('error', 'Invalid Telegram authentication data.');
        }

        $telegramId = $request->id;
        $fullName = trim($request->first_name . ' ' . ($request->last_name ?? ''));
        $username = $request->username;

        // Find or create user
        $user = User::where('telegram_id', $telegramId)->first();

        if (!$user) {
            // Create new user from Telegram
            $user = User::create([
                'uuid' => Str::uuid(),
                'full_name' => $fullName,
                'email' => $telegramId . '@telegram.tnt.com',
                'phone_number' => $telegramId,
                'telegram_id' => $telegramId,
                'password' => Hash::make(Str::random(32)),
                'profile_photo' => $request->photo_url,
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
            
            // Assign default worker role
            $user->assignRole('worker');
        } else {
            // Update profile info
            $user->update([
                'full_name' => $fullName,
                'profile_photo' => $request->photo_url,
            ]);
        }

        // Login the user
        Auth::login($user, true);

        return redirect()->intended('/mobile')
            ->with('success', 'Welcome, ' . $fullName . '! 👋');
    }

    /**
     * Verify Telegram authentication data
     */
    private function verifyTelegramData($data)
    {
        $checkHash = $data['hash'];
        unset($data['hash']);
        
        $dataCheckArr = [];
        foreach ($data as $key => $value) {
            $dataCheckArr[] = $key . '=' . $value;
        }
        sort($dataCheckArr);
        
        $dataCheckString = implode("\n", $dataCheckArr);
        
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $secretKey = hash('sha256', $botToken, true);
        $hash = hash_hmac('sha256', $dataCheckString, $secretKey);
        
        if (strcmp($hash, $checkHash) !== 0) {
            return false;
        }
        
        if ((time() - $data['auth_date']) > 86400) {
            return false;
        }
        
        return true;
    }

    /**
     * Telegram widget callback
     */
    public function callback(Request $request)
    {
        return $this->authenticate($request);
    }
}
