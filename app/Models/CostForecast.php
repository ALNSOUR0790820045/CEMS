<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostForecast extends Model
{
    protected $fillable = [
        'project_id',
        'forecast_date',
        'forecast_type',
        'period_month',
        'period_year',
        'budget_item_id',
        'cost_code_id',
        'forecast_amount',
        'basis',
        'assumptions',
        'prepared_by_id',
    ];

    protected $casts = [
        'forecast_date' => 'date',
        'period_month' => 'integer',
        'period_year' => 'integer',
        'forecast_amount' => 'decimal:2',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function budgetItem(): BelongsTo
    {
        return $this->belongsTo(ProjectBudgetItem::class);
    }

    public function costCode(): BelongsTo
    {
        return $this->belongsTo(CostCode::class);
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by_id');
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
        return $query->where('forecast_type', $type);
    }

    // Helper methods
    public static function generateForProject($projectId, $type = 'monthly')
    {
        // Implementation would calculate forecast based on trends
        // This is a placeholder for the actual logic
    }
}
