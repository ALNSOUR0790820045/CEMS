<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'name_en',
        'region',
        'city',
        'city_id',
        'country',
        'address',
        'phone',
        'email',
        'manager_id',
        'is_active',
        'is_main',
        'is_headquarters',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_main' => 'boolean',
        'is_headquarters' => 'boolean',
        'settings' => 'array',
    ];

    // Relationships
    
    /**
     * Get the company that owns the branch.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the city that the branch belongs to.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the manager of the branch.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the users assigned to the branch.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the departments in the branch.
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    // Scopes
    
    /**
     * Scope a query to only include active branches. 
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include main branches. 
     */
    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }
}