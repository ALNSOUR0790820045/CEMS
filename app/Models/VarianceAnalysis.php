<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VarianceAnalysis extends Model
{
    protected $table = 'variance_analysis';

    protected $fillable = [
        'project_id',
        'analysis_date',
        'period_month',
        'period_year',
        'cost_code_id',
        'budgeted_amount',
        'actual_amount',
        'variance_amount',
        'variance_percentage',
        'variance_type',
        'variance_reason',
        'corrective_action',
        'responsible_person_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'analysis_date' => 'date',
        'period_month' => 'integer',
        'period_year' => 'integer',
        'budgeted_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'variance_amount' => 'decimal:2',
        'variance_percentage' => 'decimal:2',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function costCode(): BelongsTo
    {
        return $this->belongsTo(CostCode::class);
    }

    public function responsiblePerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_person_id');
    }

    // Scopes
    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByPeriod($query, $month, $year)
    {
        return $query->where('period_month', $month)
                    ->where('period_year', $year);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('variance_type', $type);
    }

    public function scopeUnfavorable($query)
    {
        return $query->where('variance_type', 'unfavorable');
    }

    public function scopeSignificant($query, $threshold = 10)
    {
        return $query->where('variance_percentage', '>', $threshold)
                    ->orWhere('variance_percentage', '<', -$threshold);
    }

    // Helper methods
    public function calculateVariance(): void
    {
        $this->variance_amount = $this->budgeted_amount - $this->actual_amount;
        
        if ($this->budgeted_amount > 0) {
            $this->variance_percentage = ($this->variance_amount / $this->budgeted_amount) * 100;
        } else {
            $this->variance_percentage = 0;
        }

        $this->variance_type = $this->variance_amount >= 0 ? 'favorable' : 'unfavorable';
        $this->save();
    }

    public static function analyzeProject($projectId, $month, $year)
    {
        // This would implement automatic variance analysis
        // Placeholder for actual implementation
    }
}
