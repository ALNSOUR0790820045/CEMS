<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'name',
        'code',
        'contract_start_date',
        'original_completion_date',
        'current_completion_date',
        'company_id',
    ];

    protected $casts = [
        'contract_start_date' => 'date',
        'original_completion_date' => 'date',
        'current_completion_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ProjectActivity::class);
    }

    public function eotClaims(): HasMany
    {
        return $this->hasMany(EotClaim::class);
    }

    public function timeBarClaims(): HasMany
    {
        return $this->hasMany(TimeBarClaim::class);
    }
}
