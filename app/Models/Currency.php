<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'symbol',
        'exchange_rate',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function materialVendors()
    {
        return $this->hasMany(MaterialVendor::class);
    }
}
