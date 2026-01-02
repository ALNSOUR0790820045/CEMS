<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_en',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'commercial_registration',
        'tax_number',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Accessors
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : asset('images/default-company.png');
    }

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }
}