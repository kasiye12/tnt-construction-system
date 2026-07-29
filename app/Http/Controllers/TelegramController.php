<?php

namespace App\Http\Controllers;

use App\Services\TelegramBotService;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramBotService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function webhook(Request $request)
    {
        $update = $request->all();
        $this->telegramService->handleWebhook($update);
        return response()->json(['status' => 'ok']);
    }

    public function setWebhook()
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $url = env('APP_URL') . '/api/telegram/webhook';
        
        $response = \Illuminate\Support\Facades\Http::get(
            "https://api.telegram.org/bot{$token}/setWebhook",
            ['url' => $url]
        );

        return response()->json($response->json());
    }
}
