<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_en',
        'client_code',
        'email',
        'phone',
        'commercial_registration',
        'tax_number',
        'address',
        'city_id',
        'country_id',
        'client_type',
        'contact_person',
        'type',
        'notes',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function tenders(): HasMany
    {
        return $this->hasMany(Tender::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('client_type', $type);
    }
}