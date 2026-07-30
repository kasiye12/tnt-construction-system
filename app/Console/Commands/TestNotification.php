<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class TestNotification extends Command
{
    protected $signature = 'notify:test {user_id?}';
    protected $description = 'Send test notification';

    public function handle()
    {
        $userId = $this->argument('user_id') ?? 1;
        $service = new NotificationService();
        
        $service->send($userId, 'test', '🔔 Test Notification', 
            'This is a test notification from TNT Construction System at ' . now()->format('H:i:s'),
            ['test' => true]
        );
        
        $this->info("✅ Test notification sent to user #{$userId}!");
    }
}
