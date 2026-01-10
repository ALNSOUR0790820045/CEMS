<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'code',
        'phone_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'country_id');
    }

    public function nationalityEmployees(): HasMany
    {
        return $this->hasMany(Employee::class, 'nationality_id');
    }
}
