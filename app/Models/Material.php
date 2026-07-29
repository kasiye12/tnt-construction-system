<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Material extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'material_code',
        'category_id',
        'unit',
        'unit_price',
        'current_stock',
        'minimum_stock',
        'maximum_stock',
        'reorder_point',
        'supplier_name',
        'supplier_contact',
        'storage_location',
        'status',
        'description',
        'specifications',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'maximum_stock' => 'decimal:2',
        'reorder_point' => 'decimal:2',
        'specifications' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function category()
    {
        return $this->belongsTo(MaterialCategory::class, 'category_id');
    }

    public function transactions()
    {
        return $this->hasMany(MaterialTransaction::class);
    }
}
