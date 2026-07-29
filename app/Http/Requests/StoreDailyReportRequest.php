<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDailyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'site_id' => ['required', 'exists:sites,id'],
            'report_date' => ['required', 'date', 'before_or_equal:today'],
            'workforce_count' => ['nullable', 'integer', 'min:0'],
            'subcontractor_count' => ['nullable', 'integer', 'min:0'],
            'absent_count' => ['nullable', 'integer', 'min:0'],
            'progress_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'summary_text' => ['nullable', 'string', 'max:5000'],
            'challenges_encountered' => ['nullable', 'string', 'max:2000'],
            'safety_incidents' => ['nullable', 'string', 'max:2000'],
            'material_deliveries' => ['nullable', 'string', 'max:2000'],
            'equipment_hours' => ['nullable', 'array'],
            'weather_conditions' => ['nullable', 'array'],
            'status' => ['required', 'in:draft,submitted'],
            'photos.*' => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'report_date.before_or_equal' => 'Report date cannot be in the future.',
            'progress_percentage.max' => 'Progress cannot exceed 100%.',
            'photos.*.max' => 'Each photo must be less than 5MB.',
        ];
    }
}
