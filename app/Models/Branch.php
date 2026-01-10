<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'name_en',
        'code',
        'phone',
        'address',
        'city_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
