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
        'primary_currency_id',
        'secondary_currencies',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'secondary_currencies' => 'array',
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

    public function primaryCurrency()
    {
        return $this->belongsTo(Currency::class, 'primary_currency_id');
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function checks()
    {
        return $this->hasMany(Check::class);
    }

    public function promissoryNotes()
    {
        return $this->hasMany(PromissoryNote::class);
    }

    public function guarantees()
    {
        return $this->hasMany(Guarantee::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
