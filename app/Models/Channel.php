<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Channel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'project_id', 'site_id', 'name', 'type',
        'description', 'created_by', 'is_archived', 'is_private',
        'avatar', 'settings', 'last_message_at'
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'is_private' => 'boolean',
        'settings' => 'array',
        'last_message_at' => 'datetime',
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'channel_members')
            ->withPivot('role', 'last_read_at', 'is_muted')
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }
}
