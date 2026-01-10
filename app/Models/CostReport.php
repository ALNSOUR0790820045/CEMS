<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostReport extends Model
{
    protected $fillable = [
        'report_number',
        'project_id',
        'report_date',
        'report_type',
        'period_from',
        'period_to',
        'original_budget',
        'revised_budget',
        'committed_costs',
        'actual_costs',
        'forecast_at_completion',
        'variance_to_budget',
        'percentage_complete',
        'cost_performance_index',
        'schedule_performance_index',
        'earned_value',
        'prepared_by_id',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'report_date' => 'date',
        'period_from' => 'date',
        'period_to' => 'date',
        'original_budget' => 'decimal:2',
        'revised_budget' => 'decimal:2',
        'committed_costs' => 'decimal:2',
        'actual_costs' => 'decimal:2',
        'forecast_at_completion' => 'decimal:2',
        'variance_to_budget' => 'decimal:2',
        'percentage_complete' => 'decimal:2',
        'cost_performance_index' => 'decimal:4',
        'schedule_performance_index' => 'decimal:4',
        'earned_value' => 'decimal:2',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
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

    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('report_date', [$from, $to]);
    }

    // Helper methods
    public static function generateReportNumber($year = null)
    {
        $year = $year ?? date('Y');
        
        // Use lockForUpdate to prevent race conditions
        $lastReport = static::whereYear('report_date', $year)
            ->lockForUpdate()
            ->orderBy('id', 'desc')
            ->first();

        if ($lastReport && preg_match('/CR-(\d{4})-(\d{3})/', $lastReport->report_number, $matches)) {
            $sequence = intval($matches[2]) + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('CR-%s-%03d', $year, $sequence);
    }

    public function calculateEVM(): void
    {
        // Calculate Cost Performance Index (CPI)
        if ($this->actual_costs > 0) {
            $this->cost_performance_index = $this->earned_value / $this->actual_costs;
        }

        // Calculate variance
        $this->variance_to_budget = $this->revised_budget - $this->actual_costs;

        // Calculate forecast at completion (EAC)
        if ($this->cost_performance_index > 0) {
            $this->forecast_at_completion = $this->revised_budget / $this->cost_performance_index;
        }

        $this->save();
    }
}
