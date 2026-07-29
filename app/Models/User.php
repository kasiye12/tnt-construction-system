<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
        'uuid',
        'full_name',
        'phone_number',
        'email',
        'password',
        'telegram_id',
        'profile_photo',
        'employee_id',
        'department',
        'position',
        'site_id',
        'status',
        'last_seen_at',
        'settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_seen_at' => 'datetime',
        'settings' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_members')
            ->withPivot('role', 'permissions')
            ->withTimestamps();
    }

    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'manager_id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function supervisedSites()
    {
        return $this->hasMany(Site::class, 'supervisor_id');
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class, 'submitted_by');
    }

    public function approvedReports()
    {
        return $this->hasMany(DailyReport::class, 'approved_by');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function channels()
    {
        return $this->belongsToMany(Channel::class, 'channel_members')
            ->withPivot('role', 'last_read_at', 'is_muted')
            ->withTimestamps();
    }

    public function checkins()
    {
        return $this->hasMany(WorkerCheckin::class);
    }

    public function getFullNameAttribute()
    {
        return $this->attributes['full_name'] ?? 'Unknown';
    }

    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->diffInMinutes(now()) < 5;
    }

    public function updateLastSeen()
    {
        $this->update(['last_seen_at' => now()]);
    }
}
