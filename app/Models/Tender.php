<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tender extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'code',
        'title',
        'description',
        'submission_date',
        'estimated_value',
        'status',
    ];

    protected $casts = [
        'submission_date' => 'date',
        'estimated_value' => 'decimal:2',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function risks(): HasMany
    {
        return $this->hasMany(TenderRisk::class);
    }

    public function contingencyReserve(): HasOne
    {
        return $this->hasOne(TenderContingencyReserve::class);
    }

    // Helper methods
    public function calculateTotalRiskExposure(): float
    {
        return $this->risks()
            ->whereNotNull('cost_impact_expected')
            ->get()
            ->sum(function ($risk) {
                return ($risk->probability_score / 5) * $risk->cost_impact_expected;
            });
    }

    public function getCriticalRisksCount(): int
    {
        return $this->risks()->where('risk_level', 'critical')->count();
    }

    public function getHighRisksCount(): int
    {
        return $this->risks()->where('risk_level', 'high')->count();
    }

    public function getMediumRisksCount(): int
    {
        return $this->risks()->where('risk_level', 'medium')->count();
    }

    public function getLowRisksCount(): int
    {
        return $this->risks()->where('risk_level', 'low')->count();
    }
}
