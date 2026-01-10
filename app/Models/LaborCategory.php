<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaborCategory extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'skill_level',
        'hourly_rate',
        'daily_rate',
        'overtime_multiplier',
        'is_active',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'overtime_multiplier' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function laborers()
    {
        return $this->hasMany(Laborer::class, 'category_id');
    }
}
