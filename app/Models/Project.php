<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'code',
        'location',
        'location_coordinates',
        'manager_id',
        'status',
        'priority',
        'start_date',
        'end_date',
        'budget',
        'actual_cost',
        'description',
        'client_name',
        'client_contact',
        'settings',
        'metadata',
    ];

    protected $casts = [
        'location_coordinates' => 'array',
        'settings' => 'array',
        'metadata' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role', 'permissions')
            ->withTimestamps();
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }
}
