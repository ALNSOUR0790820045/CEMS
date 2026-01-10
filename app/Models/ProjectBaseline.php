<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectBaseline extends Model
{
    protected $fillable = [
        'project_id',
        'baseline_name',
        'baseline_date',
        'is_current',
        'schedule_snapshot',
        'cost_snapshot',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'baseline_date' => 'date',
        'is_current' => 'boolean',
        'schedule_snapshot' => 'array',
        'cost_snapshot' => 'array',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Methods
    public function setAsCurrent()
    {
        // First, unset all other baselines as current for this project
        self::where('project_id', $this->project_id)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);
        
        // Then set this one as current
        $this->is_current = true;
        $this->save();
    }

    public static function createFromProject(Project $project, $baselineName, $reason = null, $userId)
    {
        // Get all activities with their current state
        $activities = $project->activities()->get()->map(function ($activity) {
            return [
                'id' => $activity->id,
                'name' => $activity->name,
                'code' => $activity->code,
                'planned_start_date' => $activity->planned_start_date,
                'planned_end_date' => $activity->planned_end_date,
                'planned_duration_days' => $activity->planned_duration_days,
                'planned_budget' => $activity->planned_budget,
                'weight' => $activity->weight,
                'is_critical' => $activity->is_critical,
            ];
        })->toArray();

        // Get cost information
        $costData = [
            'total_budget' => $project->total_budget,
            'contingency_budget' => $project->contingency_budget,
            'activities_budget' => collect($activities)->sum('planned_budget'),
        ];

        return self::create([
            'project_id' => $project->id,
            'baseline_name' => $baselineName,
            'baseline_date' => now(),
            'is_current' => false,
            'schedule_snapshot' => $activities,
            'cost_snapshot' => $costData,
            'reason' => $reason,
            'created_by' => $userId,
        ]);
    }
}
