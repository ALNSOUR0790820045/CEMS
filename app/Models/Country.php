<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'phone_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
