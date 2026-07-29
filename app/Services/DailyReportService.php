<?php
// File: app/Services/DailyReportService.php

namespace App\Services;

use App\Models\DailyReport;
use App\Models\Site;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DailyReportService
{
    public function createReport(array $data, User $user): DailyReport
    {
        return DB::transaction(function () use ($data, $user) {
            $site = Site::findOrFail($data['site_id']);

            $reportData = [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'site_id' => $data['site_id'],
                'project_id' => $site->project_id,
                'submitted_by' => $user->id,
                'report_date' => $data['report_date'],
                'workforce_count' => $data['workforce_count'] ?? null,
                'subcontractor_count' => $data['subcontractor_count'] ?? null,
                'progress_percentage' => $data['progress_percentage'] ?? null,
                'equipment_hours' => $data['equipment_hours'] ?? null,
                'weather_conditions' => $data['weather_conditions'] ?? null,
                'summary_text' => $data['summary_text'] ?? null,
                'challenges_encountered' => $data['challenges_encountered'] ?? null,
                'safety_incidents' => $data['safety_incidents'] ?? null,
                'material_deliveries' => $data['material_deliveries'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'location_data' => $data['location_data'] ?? null,
                'is_offline_submission' => $data['is_offline_submission'] ?? false,
            ];

            $report = DailyReport::create($reportData);

            // Handle file attachments
            if (isset($data['attachments'])) {
                foreach ($data['attachments'] as $attachment) {
                    $this->attachFile($report, $attachment);
                }
            }

            // Update site progress if report is submitted
            if ($report->status === 'submitted' && $report->progress_percentage) {
                $site->update(['progress_percentage' => $report->progress_percentage]);
            }

            return $report;
        });
    }

    public function attachFile(DailyReport $report, $file)
    {
        $path = $file->store(
            'reports/' . $report->id . '/' . date('Y/m/d'),
            's3'
        );

        return $report->attachments()->create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'file_path' => $path,
            'file_type' => $this->getFileCategory($file->getMimeType()),
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);
    }

    protected function getFileCategory(string $mimeType): string
    {
        return match(true) {
            str_starts_with($mimeType, 'image/') => 'image',
            str_starts_with($mimeType, 'application/pdf') => 'document',
            str_starts_with($mimeType, 'application/msword'),
            str_starts_with($mimeType, 'application/vnd.openxmlformats') => 'document',
            default => 'other'
        };
    }

    public function getDailyStatistics(?int $projectId, ?string $date): array
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        $query = DailyReport::whereDate('report_date', $date);

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        $reports = $query->get();

        return [
            'total_reports' => $reports->count(),
            'submitted_reports' => $reports->where('status', 'submitted')->count(),
            'approved_reports' => $reports->where('status', 'approved')->count(),
            'total_workforce' => $reports->sum('workforce_count'),
            'average_progress' => $reports->avg('progress_percentage'),
            'sites_reported' => $reports->unique('site_id')->count(),
            'safety_incidents' => $reports->filter(fn($r) => !empty($r->safety_incidents))->count(),
        ];
    }

    public function generateWeeklyReport(int $projectId, Carbon $startDate, Carbon $endDate): array
    {
        $reports = DailyReport::where('project_id', $projectId)
            ->whereBetween('report_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->get();

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'total_workforce_days' => $reports->sum('workforce_count'),
                'average_daily_workforce' => $reports->avg('workforce_count'),
                'progress_delta' => $reports->max('progress_percentage') - $reports->min('progress_percentage'),
                'total_equipment_hours' => $reports->sum(function ($report) {
                    return collect($report->equipment_hours)->sum();
                }),
                'safety_incidents' => $reports->filter(fn($r) => !empty($r->safety_incidents))->count(),
                'material_deliveries' => $reports->filter(fn($r) => !empty($r->material_deliveries))->count(),
            ],
            'reports' => $reports->map(fn($r) => [
                'date' => $r->report_date->format('Y-m-d'),
                'site' => $r->site->site_name,
                'progress' => $r->progress_percentage,
                'workforce' => $r->workforce_count,
            ])
        ];
    }
}