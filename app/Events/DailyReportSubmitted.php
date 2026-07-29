<?php
// File: app/Events/DailyReportSubmitted.php

namespace App\Events;

use App\Models\DailyReport;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DailyReportSubmitted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $report;

    public function __construct(DailyReport $report)
    {
        $this->report = $report->load(['submittedBy:id,full_name', 'site:id,site_name']);
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('project.' . $this->report->project_id),
            new PrivateChannel('reports.' . $this->report->site_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'report.submitted';
    }
}