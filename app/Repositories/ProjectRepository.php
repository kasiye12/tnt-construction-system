<?php

namespace App\Repositories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectRepository extends BaseRepository
{
    protected function model(): string
    {
        return Project::class;
    }

    public function getActiveProjects(): Collection
    {
        return $this->model
            ->with(['manager', 'sites'])
            ->where('status', 'active')
            ->latest()
            ->get();
    }

    public function getProjectsByManager(int $managerId): Collection
    {
        return $this->model
            ->with(['sites', 'dailyReports'])
            ->where('manager_id', $managerId)
            ->latest()
            ->get();
    }

    public function getProjectStats(): array
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->where('status', 'active')->count(),
            'completed' => $this->model->where('status', 'completed')->count(),
            'on_hold' => $this->model->where('status', 'on_hold')->count(),
            'total_budget' => $this->model->sum('budget'),
            'total_actual_cost' => $this->model->sum('actual_cost'),
        ];
    }

    public function filterProjects(array $filters): LengthAwarePaginator
    {
        $query = $this->model->with(['manager', 'sites']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['manager_id'])) {
            $query->where('manager_id', $filters['manager_id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('code', 'like', "%{$filters['search']}%")
                  ->orWhere('location', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('start_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('start_date', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }
}
