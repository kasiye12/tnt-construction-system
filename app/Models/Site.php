<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Site extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'project_id',
        'site_name',
        'site_code',
        'location_coordinates',
        'supervisor_id',
        'status',
        'type',
        'address',
        'landmark',
        'area_sqm',
        'progress_percentage',
        'start_date',
        'expected_end_date',
        'actual_end_date',
        'max_workers',
        'facilities',
        'settings',
        'notes',
    ];

    protected $casts = [
        'location_coordinates' => 'array',
        'facilities' => 'array',
        'settings' => 'array',
        'start_date' => 'date',
        'expected_end_date' => 'date',
        'actual_end_date' => 'date',
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

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }

    public function workers()
    {
        return $this->belongsToMany(User::class, 'site_workers')
            ->withPivot('role', 'shift', 'assigned_date', 'end_date', 'status')
            ->withTimestamps();
    }
}
