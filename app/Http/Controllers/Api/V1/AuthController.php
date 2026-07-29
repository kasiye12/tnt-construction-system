<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }

    public function telegramLogin(Request $request)
    {
        $request->validate([
            'telegram_id' => 'required|string',
            'full_name' => 'required|string',
        ]);

        $user = User::where('telegram_id', $request->telegram_id)->first();

        if (!$user) {
            $user = User::create([
                'uuid' => Str::uuid(),
                'full_name' => $request->full_name,
                'telegram_id' => $request->telegram_id,
                'email' => $request->telegram_id . '@telegram.tnt.com',
                'phone_number' => $request->phone_number ?? $request->telegram_id,
                'password' => Hash::make(Str::random(16)),
                'status' => 'active',
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
