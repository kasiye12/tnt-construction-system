<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send notification to user
     */
    public function send($userId, $type, $title, $message, $data = [])
    {
        $notification = Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);

        // Send email if user has email notifications enabled
        $user = User::find($userId);
        if ($user && $user->email) {
            $this->sendEmail($user->email, $title, $message);
        }

        // Send Telegram notification if user has Telegram
        if ($user && $user->telegram_id) {
            $telegram = new TelegramBotService();
            $telegram->sendMessage($user->telegram_id, "🔔 *{$title}*\n\n{$message}");
        }

        return $notification;
    }

    /**
     * Send report submitted notification
     */
    public function reportSubmitted($report)
    {
        // Notify project manager
        $project = $report->project;
        if ($project && $project->manager) {
            $this->send(
                $project->manager->id,
                'report_submitted',
                'New Daily Report Submitted',
                "{$report->submittedBy->full_name} submitted a report for {$report->site->site_name}",
                ['report_id' => $report->id]
            );
        }
    }

    /**
     * Send report approved notification
     */
    public function reportApproved($report)
    {
        $this->send(
            $report->submitted_by,
            'report_approved',
            'Report Approved',
            "Your report for {$report->report_date->format('M d, Y')} has been approved",
            ['report_id' => $report->id]
        );
    }

    /**
     * Send check-in reminder
     */
    public function checkinReminder($user)
    {
        $this->send(
            $user->id,
            'checkin_reminder',
            'Check-in Reminder',
            "Don't forget to check in at your site: {$user->site->site_name}",
            []
        );
    }

    /**
     * Send low stock alert
     */
    public function lowStockAlert($material)
    {
        // Get all managers
        $managers = User::where('position', 'like', '%manager%')->get();
        
        foreach ($managers as $manager) {
            $this->send(
                $manager->id,
                'low_stock',
                'Low Stock Alert',
                "{$material->name} is low on stock ({$material->current_stock} {$material->unit} remaining)",
                ['material_id' => $material->id]
            );
        }
    }

    /**
     * Send email notification
     */
    private function sendEmail($email, $subject, $message)
    {
        try {
            Mail::raw($message, function($mail) use ($email, $subject) {
                $mail->to($email)
                     ->subject("[TNT Construction] {$subject}");
            });
        } catch (\Exception $e) {
            \Log::error("Email notification failed: " . $e->getMessage());
        }
    }
}
