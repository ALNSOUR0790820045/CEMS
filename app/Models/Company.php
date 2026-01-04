<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

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

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function certifications()
    {
        return $this->hasMany(Certification::class);
    }

    public function complianceRequirements()
    {
        return $this->hasMany(ComplianceRequirement::class);
    }

    public function complianceTrackings()
    {
        return $this->hasMany(ComplianceTracking::class);
    }
}
