<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskAssessment extends Model
{
    protected $fillable = [
        'risk_id',
        'assessment_date',
        'assessment_type',
        'assessed_by_id',
        'probability',
        'probability_score',
        'impact',
        'impact_score',
        'risk_score',
        'risk_level',
        'cost_impact',
        'schedule_impact',
        'justification',
        'recommendations',
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'probability_score' => 'integer',
        'impact_score' => 'integer',
        'risk_score' => 'integer',
        'cost_impact' => 'decimal:2',
        'schedule_impact' => 'integer',
    ];

    // Auto-calculate risk score and level
    protected static function booted()
    {
        static::saving(function ($assessment) {
            $assessment->risk_score = $assessment->probability_score * $assessment->impact_score;
            
            if ($assessment->risk_score >= 16) {
                $assessment->risk_level = 'critical';
            } elseif ($assessment->risk_score >= 10) {
                $assessment->risk_level = 'high';
            } elseif ($assessment->risk_score >= 5) {
                $assessment->risk_level = 'medium';
            } else {
                $assessment->risk_level = 'low';
            }
        });
    }

    // Relationships
    public function risk(): BelongsTo
    {
        return $this->belongsTo(Risk::class);
    }

    public function assessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by_id');
    }
}
