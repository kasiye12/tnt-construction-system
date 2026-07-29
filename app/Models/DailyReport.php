<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DailyReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'site_id',
        'project_id',
        'submitted_by',
        'report_date',
        'workforce_count',
        'subcontractor_count',
        'absent_count',
        'progress_percentage',
        'equipment_hours',
        'weather_conditions',
        'summary_text',
        'challenges_encountered',
        'safety_incidents',
        'material_deliveries',
        'quality_inspections',
        'visitors',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'location_data',
        'is_offline_submission',
        'custom_fields',
    ];

    protected $casts = [
        'report_date' => 'date',
        'equipment_hours' => 'array',
        'weather_conditions' => 'array',
        'location_data' => 'array',
        'custom_fields' => 'array',
        'is_offline_submission' => 'boolean',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function attachments()
    {
        return $this->hasMany(ReportAttachment::class, 'report_id');
    }
}
