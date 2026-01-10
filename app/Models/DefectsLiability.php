<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DefectsLiability extends Model
{
    protected $table = 'defects_liability';

    protected $fillable = [
        'project_id',
        'contract_id',
        'retention_id',
        'taking_over_date',
        'dlp_start_date',
        'dlp_end_date',
        'dlp_months',
        'final_certificate_date',
        'status',
        'extension_months',
        'extension_reason',
        'defects_reported',
        'defects_rectified',
        'performance_bond_released',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'taking_over_date' => 'date',
        'dlp_start_date' => 'date',
        'dlp_end_date' => 'date',
        'final_certificate_date' => 'date',
        'performance_bond_released' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function retention(): BelongsTo
    {
        return $this->belongsTo(Retention::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(DefectNotification::class);
    }
}
