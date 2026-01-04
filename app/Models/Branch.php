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
        'country',
        'address',
        'phone',
        'email',
        'manager_id',
        'is_active',
        'is_headquarters',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_headquarters' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Get the company that owns the branch.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
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
}
