<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'code' => $this->code,
            'location' => $this->location,
            'status' => $this->status,
            'priority' => $this->priority,
            'budget' => $this->budget,
            'actual_cost' => $this->actual_cost,
            'budget_utilization' => $this->budget > 0 
                ? round(($this->actual_cost / $this->budget) * 100, 2) 
                : 0,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'duration_days' => $this->start_date?->diffInDays($this->end_date),
            'manager' => [
                'id' => $this->manager->id ?? null,
                'name' => $this->manager->full_name ?? null,
                'email' => $this->manager->email ?? null,
            ],
            'client' => [
                'name' => $this->client_name,
                'contact' => $this->client_contact,
            ],
            'stats' => [
                'total_sites' => $this->sites_count ?? $this->sites->count(),
                'total_reports' => $this->daily_reports_count ?? 0,
                'progress' => round($this->sites->avg('progress_percentage') ?? 0, 2),
            ],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function with(Request $request): array
    {
        return [
            'success' => true,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
