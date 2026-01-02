<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProjectActivity extends Model
{
    protected $fillable = [
        'project_id',
        'wbs_id',
        'activity_code',
        'name',
        'name_en',
        'description',
        'planned_start_date',
        'planned_end_date',
        'planned_duration_days',
        'actual_start_date',
        'actual_end_date',
        'actual_duration_days',
        'planned_effort_hours',
        'actual_effort_hours',
        'progress_percent',
        'progress_method',
        'type',
        'is_critical',
        'total_float_days',
        'free_float_days',
        'responsible_id',
        'status',
        'budgeted_cost',
        'actual_cost',
        'priority',
        'notes',
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
        'planned_effort_hours' => 'decimal:2',
        'actual_effort_hours' => 'decimal:2',
        'progress_percent' => 'decimal:2',
        'is_critical' => 'boolean',
        'budgeted_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function wbs()
    {
        return $this->belongsTo(ProjectWbs::class, 'wbs_id');
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function predecessors()
    {
        return $this->belongsToMany(
            ProjectActivity::class,
            'activity_dependencies',
            'successor_id',
            'predecessor_id'
        )->withPivot('type', 'lag_days')->withTimestamps();
    }

    public function successors()
    {
        return $this->belongsToMany(
            ProjectActivity::class,
            'activity_dependencies',
            'predecessor_id',
            'successor_id'
        )->withPivot('type', 'lag_days')->withTimestamps();
    }

    public function milestones()
    {
        return $this->hasMany(ProjectMilestone::class, 'activity_id');
    }

    // Auto-calculate duration from dates
    public function calculatePlannedDuration()
    {
        if ($this->planned_start_date && $this->planned_end_date) {
            $start = Carbon::parse($this->planned_start_date);
            $end = Carbon::parse($this->planned_end_date);
            $this->planned_duration_days = $start->diffInDays($end) + 1;
        }
    }

    public function calculateActualDuration()
    {
        if ($this->actual_start_date && $this->actual_end_date) {
            $start = Carbon::parse($this->actual_start_date);
            $end = Carbon::parse($this->actual_end_date);
            $this->actual_duration_days = $start->diffInDays($end) + 1;
        }
    }

    // Auto-calculate progress based on method
    public function calculateProgress()
    {
        switch ($this->progress_method) {
            case 'duration':
                if ($this->planned_duration_days > 0 && $this->actual_duration_days) {
                    $this->progress_percent = min(100, ($this->actual_duration_days / $this->planned_duration_days) * 100);
                }
                break;
            case 'effort':
                if ($this->planned_effort_hours > 0) {
                    $this->progress_percent = min(100, ($this->actual_effort_hours / $this->planned_effort_hours) * 100);
                }
                break;
            // 'manual' and 'units' remain unchanged
        }
    }

    // Accessor for status badge color
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'not_started' => '#86868b',
            'in_progress' => '#0071e3',
            'completed' => '#34c759',
            'on_hold' => '#ff9500',
            'cancelled' => '#ff3b30',
            default => '#86868b',
        };
    }

    // Accessor for priority color
    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => '#34c759',
            'medium' => '#ff9500',
            'high' => '#ff6b1a',
            'critical' => '#ff3b30',
            default => '#86868b',
        };
    }
}
