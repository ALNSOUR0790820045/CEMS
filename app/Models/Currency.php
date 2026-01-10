<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\Relations\HasMany;
>>>>>>> origin/main

class Currency extends Model
{
    protected $fillable = [
<<<<<<< HEAD
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
=======
        'name',
        'name_en',
        'code',
        'symbol',
        'exchange_rate',
        'is_base',
        'is_active',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'is_base' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBase($query)
    {
        return $query->where('is_base', true);
    }

    // Method to format exchange rate
    public function getFormattedExchangeRate()
    {
        return number_format($this->exchange_rate, 6);
    }

    // Relations
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
>>>>>>> origin/main
