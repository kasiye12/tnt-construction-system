<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'equipment_code',
        'type',
        'model',
        'manufacturer',
        'serial_number',
        'purchase_date',
        'purchase_cost',
        'status',
        'current_site_id',
        'hourly_rate',
        'daily_rate',
        'total_hours_used',
        'last_maintenance_date',
        'next_maintenance_date',
        'notes',
        'specifications',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'specifications' => 'array',
        'purchase_cost' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'daily_rate' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function currentSite()
    {
        return $this->belongsTo(Site::class, 'current_site_id');
    }

    public function usageLogs()
    {
        return $this->hasMany(EquipmentUsageLog::class);
    }
}
