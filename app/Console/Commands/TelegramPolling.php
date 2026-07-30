<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TelegramPolling extends Command
{
    protected $signature = 'telegram:poll';
    protected $description = 'Poll Telegram for updates and respond to messages';

    public function handle()
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        
        if (!$token) {
            $this->error('TELEGRAM_BOT_TOKEN not set in .env');
            return;
        }
        
        $offset = 0;
        
        $this->info('🤖 TNT Construction Bot started!');
        $this->info('Bot: @TNTConstructionBot');
        $this->info('Waiting for messages...');
        $this->line('');
        
        while (true) {
            try {
                $response = Http::timeout(30)->get("https://api.telegram.org/bot{$token}/getUpdates", [
                    'offset' => $offset,
                    'timeout' => 30,
                ]);
                
                if (!$response->successful()) {
                    $this->error('API Error: ' . $response->body());
                    sleep(5);
                    continue;
                }
                
                $updates = $response->json('result', []);
                
                foreach ($updates as $update) {
                    $offset = $update['update_id'] + 1;
                    $this->processUpdate($update);
                }
            } catch (\Exception $e) {
                $this->error('Error: ' . $e->getMessage());
                sleep(5);
            }
            
            sleep(2);
        }
    }
    
    private function processUpdate($update)
    {
        // Handle text messages
        if (isset($update['message']['text'])) {
            $this->handleMessage($update['message']);
        }
        
        // Handle location sharing
        if (isset($update['message']['location'])) {
            $this->handleLocation($update['message']);
        }
    }
    
    private function handleMessage($message)
    {
        $chatId = $message['chat']['id'];
        $text = trim($message['text']);
        $from = $message['from'];
        $telegramId = $from['id'];
        $firstName = $from['first_name'] ?? '';
        $lastName = $from['last_name'] ?? '';
        $fullName = trim($firstName . ' ' . $lastName);
        
        $this->info("📩 {$fullName} ({$telegramId}): {$text}");
        
        switch ($text) {
            case '/start':
                $this->cmdStart($chatId, $telegramId, $fullName);
                break;
            case '/login':
                $this->cmdLogin($chatId, $telegramId, $fullName);
                break;
            case '/checkin':
                $this->cmdCheckin($chatId, $telegramId);
                break;
            case '/report':
                $this->cmdReport($chatId);
                break;
            case '/status':
                $this->cmdStatus($chatId, $telegramId);
                break;
            case '/help':
                $this->cmdHelp($chatId);
                break;
            default:
                $this->sendMessage($chatId, "I didn't understand. Type /help for available commands.");
        }
    }
    
    private function handleLocation($message)
    {
        $chatId = $message['chat']['id'];
        $telegramId = $message['from']['id'];
        $location = $message['location'];
        
        $user = User::where('telegram_id', $telegramId)->first();
        
        if (!$user) {
            $this->sendMessage($chatId, "❌ Please send /start first to register.");
            return;
        }
        
        // Create check-in
        \App\Models\WorkerCheckin::create([
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
        
        $this->sendMessage($chatId, 
            "✅ *Check-in successful!*\n\n" .
            "📍 Location: " . $location['latitude'] . ", " . $location['longitude'] . "\n" .
            "🕐 Time: " . now()->format('H:i A') . "\n" .
            "📅 Date: " . now()->format('Y-m-d')
        );
        
        $this->info("✅ Check-in for user {$user->id}");
    }
    
    private function cmdStart($chatId, $telegramId, $fullName)
    {
        $user = User::where('telegram_id', $telegramId)->first();
        
        if ($user) {
            $msg = "👋 Welcome back, *{$user->full_name}*!\n\n";
            $msg .= "🏗️ Site: " . ($user->site->site_name ?? 'Not assigned') . "\n";
            $msg .= "📋 Position: " . ($user->position ?? 'N/A') . "\n\n";
            $msg .= "*Commands:*\n";
            $msg .= "📍 Share Location - Check in\n";
            $msg .= "/report - Daily report\n";
            $msg .= "/status - Your status\n";
            $msg .= "/help - Help menu";
        } else {
            $user = User::create([
                'uuid' => Str::uuid(),
                'full_name' => $fullName,
                'email' => $telegramId . '@telegram.tnt.com',
                'phone_number' => (string)$telegramId,
                'telegram_id' => $telegramId,
                'password' => Hash::make(Str::random(32)),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
            
            $msg = "👋 Welcome to *TNT Construction*, {$fullName}!\n\n";
            $msg .= "✅ Account created successfully!\n\n";
            $msg .= "*Get Started:*\n";
            $msg .= "📍 Share your location to check in\n";
            $msg .= "/report - Submit daily report\n";
            $msg .= "/status - View attendance\n";
            $msg .= "/help - All commands";
        }
        
        $this->sendMessage($chatId, $msg);
    }
    
    private function cmdLogin($chatId, $telegramId, $fullName)
    {
        $user = User::where('telegram_id', $telegramId)->first();
        
        if (!$user) {
            $user = User::create([
                'uuid' => Str::uuid(),
                'full_name' => $fullName,
                'email' => $telegramId . '@telegram.tnt.com',
                'phone_number' => (string)$telegramId,
                'telegram_id' => $telegramId,
                'password' => Hash::make(Str::random(32)),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        }
        
        $this->sendMessage($chatId, "✅ Logged in as *{$user->full_name}*!\n\nUse /checkin to mark attendance.");
    }
    
    private function cmdCheckin($chatId, $telegramId)
    {
        $user = User::where('telegram_id', $telegramId)->first();
        
        if (!$user) {
            $this->sendMessage($chatId, "❌ Please /start first.");
            return;
        }
        
        $this->sendMessage($chatId, "📍 Please *share your location* to check in.\n\n📎 → Location → Send");
    }
    
    private function cmdReport($chatId)
    {
        $webAppUrl = url('/telegram/report.html');
        
        $keyboard = [
            'inline_keyboard' => [[
                ['text' => '📝 Open Report Form', 'web_app' => ['url' => $webAppUrl]]
            ]]
        ];
        
        $this->sendMessage($chatId, "📝 Click below to submit your daily report:", $keyboard);
    }
    
    private function cmdStatus($chatId, $telegramId)
    {
        $user = User::where('telegram_id', $telegramId)->with('site')->first();
        
        if (!$user) {
            $this->sendMessage($chatId, "❌ Not registered. /start first.");
            return;
        }
        
        $todayCheckin = \App\Models\WorkerCheckin::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->first();
        
        $msg = "📊 *Your Status*\n\n";
        $msg .= "👤 {$user->full_name}\n";
        $msg .= "🏗️ " . ($user->site->site_name ?? 'Not assigned') . "\n";
        $msg .= "📅 " . now()->format('Y-m-d') . "\n\n";
        
        if ($todayCheckin) {
            $msg .= "✅ Checked in: " . $todayCheckin->check_in_time->format('H:i') . "\n";
            if ($todayCheckin->check_out_time) {
                $msg .= "🚪 Checked out: " . $todayCheckin->check_out_time->format('H:i') . "\n";
            } else {
                $msg .= "📍 Currently on site\n";
            }
        } else {
            $msg .= "❌ Not checked in today\n";
        }
        
        $this->sendMessage($chatId, $msg);
    }
    
    private function cmdHelp($chatId)
    {
        $msg = "🤖 *TNT Construction Bot*\n\n";
        $msg .= "*/start* - Register\n";
        $msg .= "*/login* - Login\n";
        $msg .= "📍 *Share Location* - Check in\n";
        $msg .= "*/report* - Daily report\n";
        $msg .= "*/status* - View status\n";
        $msg .= "*/help* - This menu\n\n";
        $msg .= "📞 Support: IT Department";
        
        $this->sendMessage($chatId, $msg);
    }
    
    private function sendMessage($chatId, $text, $replyMarkup = null)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ];
        
        if ($replyMarkup) {
            $data['reply_markup'] = json_encode($replyMarkup);
        }
        
        Http::post("https://api.telegram.org/bot{$token}/sendMessage", $data);
    }
}
