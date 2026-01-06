<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tender extends Model
{
    protected $fillable = [
        'company_id',
        'tender_code',
        'name',
        'name_en',
        'description',
        'client_name',
        'estimated_value',
        'submission_date',
        'opening_date',
        'project_start_date',
        'project_duration_days',
        'status',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'submission_date' => 'date',
        'opening_date' => 'date',
        'project_start_date' => 'date',
        'estimated_value' => 'decimal:2',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function wbsItems(): HasMany
    {
        return $this->hasMany(TenderWBS::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TenderActivity::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(TenderMilestone::class);
    }
}
