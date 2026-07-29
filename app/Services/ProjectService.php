<?php

namespace App\Services;

use App\Repositories\ProjectRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProjectService
{
    protected ProjectRepository $projectRepository;
    protected NotificationService $notificationService;

    public function __construct(
        ProjectRepository $projectRepository,
        NotificationService $notificationService
    ) {
        $this->projectRepository = $projectRepository;
        $this->notificationService = $notificationService;
    }

    public function createProject(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['uuid'] = Str::uuid();
            $project = $this->projectRepository->create($data);

            // Notify manager
            if ($project->manager) {
                $this->notificationService->send(
                    $project->manager_id,
                    'project_assigned',
                    'New Project Assigned',
                    "You have been assigned as manager for project: {$project->name}"
                );
            }

            Log::info("Project created: {$project->code} - {$project->name}");
            return $project;
        });
    }

    public function updateProject(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $project = $this->projectRepository->findOrFail($id);
            
            // Check for manager change
            if (isset($data['manager_id']) && $data['manager_id'] != $project->manager_id) {
                $this->notificationService->send(
                    $data['manager_id'],
                    'project_assigned',
                    'Project Assignment',
                    "You have been assigned as manager for project: {$project->name}"
                );
            }

            $this->projectRepository->update($id, $data);
            
            Log::info("Project updated: {$project->code}");
            return $project->fresh();
        });
    }

    public function deleteProject(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $project = $this->projectRepository->findOrFail($id);
            
            // Archive project data before deletion
            Log::warning("Project deleted: {$project->code} - {$project->name}");
            
            return $this->projectRepository->delete($id);
        });
    }

    public function getProjectAnalytics(int $projectId): array
    {
        $project = $this->projectRepository->findOrFail($projectId, ['*'], [
            'sites', 'dailyReports', 'members'
        ]);

        return [
            'total_sites' => $project->sites->count(),
            'active_sites' => $project->sites->where('status', 'active')->count(),
            'total_reports' => $project->dailyReports->count(),
            'avg_progress' => round($project->sites->avg('progress_percentage'), 2),
            'total_workforce' => $project->dailyReports->sum('workforce_count'),
            'budget_utilization' => $project->budget > 0 
                ? round(($project->actual_cost / $project->budget) * 100, 2) 
                : 0,
            'member_count' => $project->members->count(),
            'reports_this_month' => $project->dailyReports()
                ->whereMonth('report_date', now()->month)
                ->count(),
        ];
    }
}
