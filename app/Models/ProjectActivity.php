<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectActivity extends Model
{
    protected $fillable = [
        'project_id',
        'parent_id',
        'name',
        'code',
        'description',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
        'planned_duration_days',
        'actual_duration_days',
        'planned_budget',
        'actual_cost',
        'progress_percent',
        'weight',
        'status',
        'is_critical',
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
        'planned_duration_days' => 'integer',
        'actual_duration_days' => 'integer',
        'planned_budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'progress_percent' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_critical' => 'boolean',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProjectActivity::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ProjectActivity::class, 'parent_id');
    }

    public function timesheets(): HasMany
    {
        return $this->hasMany(ProjectTimesheet::class, 'activity_id');
    }

    // Scopes
    public function scopeCritical($query)
    {
        return $query->where('is_critical', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDelayed($query)
    {
        return $query->where('planned_end_date', '<', now())
            ->where('status', '!=', 'completed');
    }

    // Methods
    public function isDelayed()
    {
        return $this->planned_end_date < now() && $this->status !== 'completed';
    }

    public function getScheduleVariance()
    {
        $ev = ($this->progress_percent / 100) * $this->planned_budget;
        $pv = $this->calculatePlannedValue();
        return $ev - $pv;
    }

    public function getCostVariance()
    {
        $ev = ($this->progress_percent / 100) * $this->planned_budget;
        return $ev - $this->actual_cost;
    }

    private function calculatePlannedValue()
    {
        $today = now();
        if ($today < $this->planned_start_date) {
            return 0;
        }
        if ($today >= $this->planned_end_date) {
            return $this->planned_budget;
        }
        
        $totalDays = $this->planned_start_date->diffInDays($this->planned_end_date);
        $elapsedDays = $this->planned_start_date->diffInDays($today);
        
        return ($elapsedDays / $totalDays) * $this->planned_budget;
    }
}
