<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Send notification to a user
     */
    public function send($userId, $type, $title, $message, $data = [])
    {
        DB::table('notifications')->insert([
            'id' => Str::uuid(),
            'type' => 'App\\Notifications\\' . ucfirst($type),
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $userId,
            'data' => json_encode(array_merge([
                'title' => $title,
                'message' => $message,
                'type' => $type,
            ], $data)),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send Telegram notification if user has Telegram
        $user = User::find($userId);
        if ($user && $user->telegram_id) {
            try {
                $telegram = new TelegramBotService();
                $telegram->sendMessage($user->telegram_id, "🔔 *{$title}*\n\n{$message}");
            } catch (\Exception $e) {}
        }
    }

    /**
     * Send to multiple users
     */
    public function sendToMany($userIds, $type, $title, $message, $data = [])
    {
        foreach ($userIds as $userId) {
            $this->send($userId, $type, $title, $message, $data);
        }
    }

    /**
     * Notify all admins and managers
     */
    public function notifyManagement($type, $title, $message, $data = [])
    {
        $managers = User::whereIn('position', ['Project Manager', 'Senior Project Manager', 'Administrator', 'System Administrator'])
            ->orWhere('email', 'like', '%admin%')
            ->orWhere('email', 'like', '%manager%')
            ->pluck('id')
            ->toArray();
        
        $this->sendToMany($managers, $type, $title, $message, $data);
    }
}
