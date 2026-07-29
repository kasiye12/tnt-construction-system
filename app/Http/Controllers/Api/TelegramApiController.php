<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\Site;
use App\Models\User;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramApiController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramBotService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * የቴሌግራም ዌብሁክ መቀበያ
     */
    public function webhook(Request $request)
    {
        try {
            $update = $request->all();
            $this->telegramService->handleWebhook($update);
            
            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Telegram Webhook Error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * የተጠቃሚ መረጃ ለMini App
     */
    public function getUserInfo(Request $request)
    {
        $initData = $request->header('X-Telegram-Init-Data');
        
        // Parse Telegram init data (simplified)
        parse_str($initData, $data);
        $telegramId = $data['user_id'] ?? null;

        if (!$telegramId) {
            return response()->json(['success' => false], 401);
        }

        $user = User::where('telegram_id', $telegramId)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'ተጠቃሚ አልተገኘም'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'site_id' => $user->site_id,
                'site_name' => $user->site->site_name ?? null,
                'position' => $user->position,
            ]
        ]);
    }

    /**
     * ሪፖርት ከMini App መቀበያ
     */
    public function submitReport(Request $request)
    {
        $initData = $request->header('X-Telegram-Init-Data');
        parse_str($initData, $data);
        $telegramId = $data['user_id'] ?? null;

        $user = User::where('telegram_id', $telegramId)->first();

        if (!$user || !$user->site_id) {
            return response()->json([
                'success' => false,
                'message' => 'የስራ ቦታ አልተመደበልዎትም'
            ], 400);
        }

        try {
            $site = Site::find($user->site_id);

            $report = DailyReport::create([
                'uuid' => Str::uuid(),
                'site_id' => $user->site_id,
                'project_id' => $site->project_id,
                'submitted_by' => $user->id,
                'report_date' => $request->report_date ?? now()->format('Y-m-d'),
                'workforce_count' => $request->workforce_count,
                'progress_percentage' => $request->progress_percentage,
                'summary_text' => $request->summary,
                'challenges_encountered' => $request->challenges,
                'equipment_hours' => $request->equipment_hours,
                'weather_conditions' => $request->weather,
                'status' => $request->status ?? 'submitted',
                'is_offline_submission' => false,
            ]);

            // የፎቶ ፋይሎችን አስቀምጥ
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('reports/' . $report->id, 'public');
                    $report->attachments()->create([
                        'uuid' => Str::uuid(),
                        'file_path' => $path,
                        'file_type' => 'image',
                        'original_name' => $photo->getClientOriginalName(),
                        'file_size' => $photo->getSize(),
                        'mime_type' => $photo->getMimeType(),
                        'uploaded_by' => $user->id,
                    ]);
                }
            }

            // ለፕሮጀክት ማናጀሩ ማሳወቂያ ላክ
            $project = $site->project;
            if ($project && $project->manager && $project->manager->telegram_id) {
                $this->telegramService->sendMessage(
                    $project->manager->telegram_id,
                    "📝 *አዲስ ዕለታዊ ሪፖርት*\n\n" .
                    "👤 {$user->full_name}\n" .
                    "🏗️ {$site->site_name}\n" .
                    "📊 ሂደት: {$request->progress_percentage}%\n" .
                    "👥 ሰራተኞች: {$request->workforce_count}\n" .
                    "📅 " . ($request->report_date ?? now()->format('Y-m-d'))
                );
            }

            return response()->json([
                'success' => true,
                'message' => '✅ ሪፖርት ተልኳል!',
                'data' => ['id' => $report->id]
            ]);

        } catch (\Exception $e) {
            Log::error('Report submission error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ሪፖርቱን መላክ አልተቻለም'
            ], 500);
        }
    }

    /**
     * ዌብሁክ ማዘጋጀት (Set Webhook)
     */
    public function setWebhook()
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $url = env('APP_URL') . '/api/telegram/webhook';
        
        $response = \Illuminate\Support\Facades\Http::get(
            "https://api.telegram.org/bot{$token}/setWebhook",
            [
                'url' => $url,
                'allowed_updates' => ['message', 'callback_query']
            ]
        );

        // ትዕዛዞችን አዘጋጅ
        $commands = [
            ['command' => 'start', 'description' => '🤖 ቦቱን ማስጀመሪያ'],
            ['command' => 'checkin', 'description' => '📍 ወደ ስራ መግቢያ'],
            ['command' => 'checkout', 'description' => '🚪 ከስራ መውጫ'],
            ['command' => 'report', 'description' => '📝 ዕለታዊ ሪፖርት ማስገቢያ'],
            ['command' => 'status', 'description' => '📊 የአሁን ሁኔታ ማሳያ'],
            ['command' => 'help', 'description' => '❓ እርዳታ'],
        ];

        \Illuminate\Support\Facades\Http::post(
            "https://api.telegram.org/bot{$token}/setMyCommands",
            ['commands' => $commands]
        );

        return response()->json([
            'success' => true,
            'webhook' => $response->json(),
            'message' => '✅ ዌብሁክ ተዘጋጅቷል!'
        ]);
    }
}
