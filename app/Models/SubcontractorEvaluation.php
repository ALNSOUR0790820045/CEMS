<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubcontractorEvaluation extends Model
{
    protected $fillable = [
        'subcontractor_id',
        'project_id',
        'evaluation_date',
        'evaluation_period_from',
        'evaluation_period_to',
        'quality_score',
        'time_performance_score',
        'safety_score',
        'cooperation_score',
        'strengths',
        'weaknesses',
        'recommendations',
        'evaluated_by_id',
        'company_id',
    ];

    protected $casts = [
        'evaluation_date' => 'date',
        'evaluation_period_from' => 'date',
        'evaluation_period_to' => 'date',
        'quality_score' => 'integer',
        'time_performance_score' => 'integer',
        'safety_score' => 'integer',
        'cooperation_score' => 'integer',
    ];

    protected $appends = [
        'overall_score',
    ];

    // Relationships
    public function subcontractor(): BelongsTo
    {
        return $this->belongsTo(Subcontractor::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function evaluatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Computed Attributes
    public function getOverallScoreAttribute(): float
    {
        return round(
            ($this->quality_score + 
             $this->time_performance_score + 
             $this->safety_score + 
             $this->cooperation_score) / 4, 
            2
        );
    }
}
