<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MaterialCategory extends Model
{
    protected $fillable = ['uuid', 'name', 'code', 'description', 'parent_id'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'category_id');
    }

    public function parent()
    {
        return $this->belongsTo(MaterialCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MaterialCategory::class, 'parent_id');
    }
}
