<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkerCheckin;
use App\Models\DailyReport;
use App\Models\Site;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TelegramBotService
{
    protected $token;
    protected $baseUrl;

    public function __construct()
    {
        $this->token = env('TELEGRAM_BOT_TOKEN');
        $this->baseUrl = "https://api.telegram.org/bot{$this->token}";
    }

    /**
     * የቴሌግራም ዌብሁክ ማስተናገጃ
     */
    public function handleWebhook($update)
    {
        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        }
        
        if (isset($update['callback_query'])) {
            $this->handleCallback($update['callback_query']);
        }
    }

    /**
     * መልእክት ማስተናገጃ
     */
    protected function handleMessage($message)
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $telegramId = $message['from']['id'];

        // ትዕዛዞችን ማስተናገጃ
        match(true) {
            str_starts_with($text, '/start') => $this->cmdStart($chatId, $telegramId),
            str_starts_with($text, '/checkin') || $text === '📍 ቼክ ኢን' => $this->cmdCheckIn($chatId, $telegramId),
            str_starts_with($text, '/checkout') || $text === '🚪 ቼክ አውት' => $this->cmdCheckOut($chatId, $telegramId),
            str_starts_with($text, '/report') || $text === '📝 ሪፖርት' => $this->cmdReport($chatId, $telegramId),
            str_starts_with($text, '/status') || $text === '📊 ሁኔታ' => $this->cmdStatus($chatId, $telegramId),
            str_starts_with($text, '/help') || $text === '❓ እርዳታ' => $this->cmdHelp($chatId),
            default => $this->sendMessage($chatId, "ይቅርታ፣ ትዕዛዙ አልተረዳሁም። /help ይጠቀሙ።")
        };

        // የቦታ መገኛ ሲላክ
        if (isset($message['location'])) {
            $this->handleLocation($chatId, $telegramId, $message['location']);
        }
    }

    /**
     * /start - መጀመሪያ ማያ ገጽ
     */
    protected function cmdStart($chatId, $telegramId)
    {
        $user = User::where('telegram_id', $telegramId)->first();
        
        if (!$user) {
            $this->sendMessage($chatId, 
                "👋 እንኳን ወደ *TNT Construction* ሲስተም በሰላም መጡ!\n\n" .
                "ለመመዝገብ እባክዎን የሰው ሀብት አስተዳደርን (HR) ያነጋግሩ።\n\n" .
                "📞 +251-xxx-xxxxxx\n" .
                "📧 hr@tntconstruction.com"
            );
            return;
        }

        $keyboard = [
            'keyboard' => [
                [
                    ['text' => '📍 ቼክ ኢን', 'request_location' => true],
                    ['text' => '🚪 ቼክ አውት']
                ],
                [
                    ['text' => '📝 ሪፖርት'],
                    ['text' => '📊 ሁኔታ']
                ],
                [
                    ['text' => '📸 ፎቶ ላክ'],
                    ['text' => '❓ እርዳታ']
                ]
            ],
            'resize_keyboard' => true,
            'persistent_keyboard' => true
        ];

        $greeting = match(Carbon::now()->format('H')) {
            '6', '7', '8', '9', '10', '11' => 'እንደምን አደሩ',
            '12', '13', '14', '15', '16' => 'እንደምን ዋሉ',
            default => 'እንደምን አመሹ'
        };

        $this->sendMessage($chatId, 
            "{$greeting} *{$user->full_name}*! 👋\n\n" .
            "🏗️ የስራ ቦታ: " . ($user->site->site_name ?? 'አልተመደበም') . "\n" .
            "📋 ስራ: " . ($user->position ?? 'N/A') . "\n" .
            "📅 ቀን: " . Carbon::now()->format('Y-m-d') . "\n\n" .
            "እባክዎ ከታች ያለውን ምናሌ ይጠቀሙ:",
            json_encode($keyboard)
        );
    }

    /**
     * ቼክ ኢን - ወደ ስራ መግባት
     */
    protected function cmdCheckIn($chatId, $telegramId)
    {
        $user = User::where('telegram_id', $telegramId)->first();
        
        if (!$user || !$user->site_id) {
            $this->sendMessage($chatId, '❌ የስራ ቦታ አልተመደበልዎትም። HR ያነጋግሩ።');
            return;
        }

        // አስቀድሞ ቼክ ኢን መደረጉን አረጋግጥ
        $existingCheckin = WorkerCheckin::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->where('status', 'checked_in')
            ->first();

        if ($existingCheckin) {
            $this->sendMessage($chatId, 
                "⚠️ አስቀድመው ቼክ ኢን አድርገዋል!\n\n" .
                "⏰ የገቡበት ሰዓት: " . $existingCheckin->check_in_time->format('H:i A')
            );
            return;
        }

        $keyboard = [
            'keyboard' => [[
                ['text' => '📍 ቦታዬን አጋራ (Share Location)', 'request_location' => true]
            ]],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        $this->sendMessage($chatId, 
            "📍 *ቼክ ኢን ለማድረግ*\n\n" .
            "እባክዎ የአሁኑን ቦታዎን (Location) ያጋሩ።\n\n" .
            "⏰ የአሁኑ ሰዓት: " . Carbon::now()->format('H:i A'),
            json_encode($keyboard)
        );
    }

    /**
     * ቼክ አውት - ከስራ መውጣት
     */
    protected function cmdCheckOut($chatId, $telegramId)
    {
        $user = User::where('telegram_id', $telegramId)->first();
        
        if (!$user) {
            $this->sendMessage($chatId, '❌ ተጠቃሚ አልተገኘም።');
            return;
        }

        $activeCheckin = WorkerCheckin::where('user_id', $user->id)
            ->where('status', 'checked_in')
            ->whereNull('check_out_time')
            ->first();

        if (!$activeCheckin) {
            $this->sendMessage($chatId, '❌ ምንም ንቁ ቼክ ኢን አልተገኘም።');
            return;
        }

        $activeCheckin->update([
            'check_out_time' => now(),
            'status' => 'checked_out',
            'check_out_method' => 'telegram',
        ]);

        $hoursWorked = round($activeCheckin->check_in_time->diffInHours(now()), 1);

        $this->sendMessage($chatId, 
            "✅ *ቼክ አውት ተሳክቷል!*\n\n" .
            "⏰ የገቡት: " . $activeCheckin->check_in_time->format('H:i A') . "\n" .
            "🚪 የወጡት: " . now()->format('H:i A') . "\n" .
            "⏱️ የሰሩት ሰዓት: {$hoursWorked} ሰዓታት\n" .
            "🏗️ ሳይት: " . $user->site->site_name . "\n\n" .
            "መልካም እረፍት! 🌙"
        );
    }

    /**
     * የቦታ መገኛ ማስተናገጃ (Check-in with GPS)
     */
    protected function handleLocation($chatId, $telegramId, $location)
    {
        $user = User::where('telegram_id', $telegramId)->first();
        
        if (!$user || !$user->site_id) {
            $this->sendMessage($chatId, '❌ የስራ ቦታ አልተመደበልዎትም።');
            return;
        }

        // ቼክ ኢን ፈጠራ
        $checkin = WorkerCheckin::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'site_id' => $user->site_id,
            'check_in_time' => now(),
            'check_in_latitude' => $location['latitude'],
            'check_in_longitude' => $location['longitude'],
            'check_in_location' => json_encode($location),
            'check_in_method' => 'telegram',
            'status' => 'checked_in',
        ]);

        // ለሱፐርቫይዘሩ ማሳወቂያ ላክ
        if ($user->site->supervisor) {
            $this->sendMessage($user->site->supervisor->telegram_id ?? $chatId,
                "🔵 *አዲስ ቼክ ኢን*\n\n" .
                "👤 {$user->full_name}\n" .
                "⏰ " . now()->format('H:i A') . "\n" .
                "🏗️ {$user->site->site_name}\n" .
                "📍 [" . $location['latitude'] . ", " . $location['longitude'] . "]"
            );
        }

        $this->sendMessage($chatId,
            "✅ *ቼክ ኢን ተሳክቷል!*\n\n" .
            "⏰ ሰዓት: " . now()->format('h:i A') . "\n" .
            "📅 ቀን: " . now()->format('Y-m-d') . "\n" .
            "🏗️ ሳይት: " . $user->site->site_name . "\n\n" .
            "መልካም የስራ ቀን! 💪"
        );
    }

    /**
     * ዕለታዊ ሪፖርት - Mini App ክፈት
     */
    protected function cmdReport($chatId, $telegramId)
    {
        $webAppUrl = env('APP_URL') . '/telegram/report.html';
        
        $keyboard = [
            'inline_keyboard' => [[[
                'text' => '📝 ሪፖርት ፎርም ክፈት',
                'web_app' => ['url' => $webAppUrl]
            ]]]
        ];

        $this->sendMessage($chatId, 
            "📝 *ዕለታዊ የሳይት ሪፖርት*\n\n" .
            "እባክዎ ከታች ያለውን ቁልፍ በመጫን የዛሬውን ሪፖርት ያስገቡ።",
            json_encode($keyboard)
        );
    }

    /**
     * የሰራተኛውን ሁኔታ አሳይ
     */
    protected function cmdStatus($chatId, $telegramId)
    {
        $user = User::where('telegram_id', $telegramId)->first();
        
        if (!$user) {
            $this->sendMessage($chatId, '❌ ተጠቃሚ አልተገኘም።');
            return;
        }

        $todayCheckin = WorkerCheckin::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->first();

        $todayReport = DailyReport::where('submitted_by', $user->id)
            ->whereDate('report_date', today())
            ->first();

        $status = "*📊 የዛሬ ሁኔታ*\n\n";
        $status .= "👤 ስም: {$user->full_name}\n";
        $status .= "🏗️ ሳይት: " . ($user->site->site_name ?? 'N/A') . "\n";
        $status .= "📅 ቀን: " . now()->format('Y-m-d') . "\n\n";
        
        // ቼክ ኢን ሁኔታ
        if ($todayCheckin) {
            $status .= "✅ *ቼክ ኢን:* " . $todayCheckin->check_in_time->format('H:i A') . "\n";
            if ($todayCheckin->check_out_time) {
                $status .= "🚪 *ቼክ አውት:* " . $todayCheckin->check_out_time->format('H:i A') . "\n";
            } else {
                $status .= "📍 *በሳይት ላይ ነው*\n";
            }
        } else {
            $status .= "❌ *ገና ቼክ ኢን አላደረጉም*\n";
        }

        // ሪፖርት ሁኔታ
        $status .= "\n📝 *ሪፖርት:* ";
        if ($todayReport) {
            $status .= "✅ " . ($todayReport->status == 'approved' ? 'ጸድቋል' : 'ተልኳል');
        } else {
            $status .= "❌ ገና አልተላከም";
        }

        $this->sendMessage($chatId, $status);
    }

    /**
     * እርዳታ ማሳያ
     */
    protected function cmdHelp($chatId)
    {
        $help = "*🤖 TNT Construction Bot - እርዳታ*\n\n";
        $help .= "*የሚገኙ ትዕዛዞች:*\n\n";
        $help .= "📍 *ቼክ ኢን* - ወደ ስራ ሲገቡ\n";
        $help .= "🚪 *ቼክ አውት* - ከስራ ሲወጡ\n";
        $help .= "📝 *ሪፖርት* - ዕለታዊ ሪፖርት ለማስገባት\n";
        $help .= "📊 *ሁኔታ* - የአሁኑን ሁኔታ ለማየት\n";
        $help .= "📸 *ፎቶ ላክ* - የሳይት ፎቶ ለመላክ\n";
        $help .= "❓ *እርዳታ* - ይህን መልእክት ለማሳየት\n\n";
        $help .= "📞 *የቴክኒክ ድጋፍ:*\n";
        $help .= "📧 it@tntconstruction.com\n";
        $help .= "📱 +251-xxx-xxxxxx";

        $this->sendMessage($chatId, $help);
    }

    /**
     * መልእክት መላኪያ ፈንክሽን
     */
    public function sendMessage($chatId, $text, $replyMarkup = null)
    {
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ];

        if ($replyMarkup) {
            $data['reply_markup'] = $replyMarkup;
        }

        return Http::post("{$this->baseUrl}/sendMessage", $data);
    }

    /**
     * ለቡድን/ቻናል ማሳወቂያ መላኪያ
     */
    public function sendToChannel($channelId, $text)
    {
        return $this->sendMessage($channelId, $text);
    }

    /**
     * የሪፖርት ማሳሰቢያ (በየቀኑ 11:00)
     */
    public function sendDailyReminders()
    {
        $users = User::where('status', 'active')
            ->whereNotNull('telegram_id')
            ->whereNotNull('site_id')
            ->get();

        foreach ($users as $user) {
            $hasReport = DailyReport::where('submitted_by', $user->id)
                ->whereDate('report_date', today())
                ->exists();

            if (!$hasReport && $user->telegram_id) {
                $this->sendMessage($user->telegram_id,
                    "⏰ *ማሳሰቢያ*\n\n" .
                    "እስካሁን የዛሬውን ዕለታዊ ሪፖርት አላስገቡም።\n\n" .
                    "እባክዎ /report በመጠቀም ሪፖርትዎን ያስገቡ።"
                );
            }
        }
    }
}
