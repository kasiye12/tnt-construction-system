<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SafetyIncident extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'incident_number', 'project_id', 'site_id',
        'reported_by', 'incident_datetime', 'severity', 'type',
        'location', 'description', 'immediate_actions',
        'root_cause', 'corrective_actions', 'affected_persons',
        'injuries_sustained', 'medical_treatment_required',
        'work_stoppage', 'estimated_damage_cost', 'status',
        'investigated_by', 'resolved_at', 'attachments',
        'witnesses', 'preventive_measures',
    ];

    protected $casts = [
        'incident_datetime' => 'datetime',
        'resolved_at' => 'datetime',
        'affected_persons' => 'array',
        'injuries_sustained' => 'array',
        'attachments' => 'array',
        'witnesses' => 'array',
        'medical_treatment_required' => 'boolean',
        'work_stoppage' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function investigatedBy()
    {
        return $this->belongsTo(User::class, 'investigated_by');
    }
}
