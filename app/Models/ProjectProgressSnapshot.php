<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectProgressSnapshot extends Model
{
    protected $fillable = [
        'project_id',
        'snapshot_date',
        'overall_progress_percent',
        'planned_progress_percent',
        'physical_progress_percent',
        'planned_value_pv',
        'earned_value_ev',
        'actual_cost_ac',
        'budget_at_completion_bac',
        'estimate_at_completion_eac',
        'estimate_to_complete_etc',
        'variance_at_completion_vac',
        'schedule_variance_sv',
        'cost_variance_cv',
        'schedule_performance_index_spi',
        'cost_performance_index_cpi',
        'to_complete_performance_index_tcpi',
        'planned_completion_date',
        'forecasted_completion_date',
        'comments',
        'reported_by',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'overall_progress_percent' => 'decimal:2',
        'planned_progress_percent' => 'decimal:2',
        'physical_progress_percent' => 'decimal:2',
        'planned_value_pv' => 'decimal:2',
        'earned_value_ev' => 'decimal:2',
        'actual_cost_ac' => 'decimal:2',
        'budget_at_completion_bac' => 'decimal:2',
        'estimate_at_completion_eac' => 'decimal:2',
        'estimate_to_complete_etc' => 'decimal:2',
        'variance_at_completion_vac' => 'decimal:2',
        'schedule_variance_sv' => 'decimal:2',
        'cost_variance_cv' => 'decimal:2',
        'schedule_performance_index_spi' => 'decimal:3',
        'cost_performance_index_cpi' => 'decimal:3',
        'to_complete_performance_index_tcpi' => 'decimal:3',
        'planned_completion_date' => 'date',
        'forecasted_completion_date' => 'date',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    // Accessors
    public function getHealthStatusAttribute()
    {
        $spi = $this->schedule_performance_index_spi;
        $cpi = $this->cost_performance_index_cpi;
        
        if ($spi >= 0.95 && $cpi >= 0.95) {
            return 'good';
        } elseif ($spi >= 0.85 && $cpi >= 0.85) {
            return 'warning';
        } else {
            return 'critical';
        }
    }

    public function getSpiColorAttribute()
    {
        if ($this->schedule_performance_index_spi >= 0.95) return 'green';
        if ($this->schedule_performance_index_spi >= 0.85) return 'yellow';
        return 'red';
    }

    public function getCpiColorAttribute()
    {
        if ($this->cost_performance_index_cpi >= 0.95) return 'green';
        if ($this->cost_performance_index_cpi >= 0.85) return 'yellow';
        return 'red';
    }
}
