<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Risk extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'risk_number',
        'risk_register_id',
        'project_id',
        'title',
        'description',
        'category',
        'source',
        'trigger_events',
        'affected_objectives',
        'identification_date',
        'identified_by_id',
        'probability',
        'probability_score',
        'impact',
        'impact_score',
        'risk_score',
        'risk_level',
        'cost_impact_min',
        'cost_impact_max',
        'cost_impact_expected',
        'schedule_impact_days',
        'response_strategy',
        'response_plan',
        'contingency_plan',
        'residual_probability',
        'residual_impact',
        'residual_score',
        'owner_id',
        'status',
        'due_date',
        'closed_date',
        'closure_reason',
        'lessons_learned',
        'company_id',
    ];

    protected $casts = [
        'affected_objectives' => 'array',
        'identification_date' => 'date',
        'probability_score' => 'integer',
        'impact_score' => 'integer',
        'risk_score' => 'integer',
        'cost_impact_min' => 'decimal:2',
        'cost_impact_max' => 'decimal:2',
        'cost_impact_expected' => 'decimal:2',
        'schedule_impact_days' => 'integer',
        'residual_probability' => 'integer',
        'residual_impact' => 'integer',
        'residual_score' => 'integer',
        'due_date' => 'date',
        'closed_date' => 'date',
    ];

    // Auto-calculate risk score and level
    protected static function booted()
    {
        static::creating(function ($risk) {
            if (empty($risk->risk_number)) {
                $year = date('Y');
                $count = static::whereYear('created_at', $year)->count() + 1;
                $risk->risk_number = sprintf('RSK-%s-%04d', $year, $count);
            }
        });

        static::saving(function ($risk) {
            // Calculate risk score
            $risk->risk_score = $risk->probability_score * $risk->impact_score;
            
            // Calculate risk level based on score
            if ($risk->risk_score >= 16) {
                $risk->risk_level = 'critical';
            } elseif ($risk->risk_score >= 10) {
                $risk->risk_level = 'high';
            } elseif ($risk->risk_score >= 5) {
                $risk->risk_level = 'medium';
            } else {
                $risk->risk_level = 'low';
            }

            // Calculate residual score if both values present
            if ($risk->residual_probability && $risk->residual_impact) {
                $risk->residual_score = $risk->residual_probability * $risk->residual_impact;
            }
        });
    }

    // Relationships
    public function riskRegister(): BelongsTo
    {
        return $this->belongsTo(RiskRegister::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function identifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'identified_by_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(RiskAssessment::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(RiskResponse::class);
    }

    public function monitoring(): HasMany
    {
        return $this->hasMany(RiskMonitoring::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(RiskIncident::class);
    }

    // Helper methods
    public function isCritical(): bool
    {
        return $this->risk_level === 'critical';
    }

    public function isHigh(): bool
    {
        return $this->risk_level === 'high';
    }

    public function close(string $reason, ?string $lessonsLearned = null): void
    {
        $this->update([
            'status' => 'closed',
            'closed_date' => now(),
            'closure_reason' => $reason,
            'lessons_learned' => $lessonsLearned,
        ]);
    }

    public function escalate(): void
    {
        // Logic for escalating high/critical risks
        // This could trigger notifications to management
    }
}
