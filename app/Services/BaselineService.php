<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectBaseline;

class BaselineService
{
    /**
     * Create a new baseline from current project state
     */
    public function createBaseline(Project $project, string $baselineName, ?string $reason, int $userId): ProjectBaseline
    {
        return ProjectBaseline::createFromProject($project, $baselineName, $reason, $userId);
    }

    /**
     * Set a baseline as current
     */
    public function setCurrentBaseline(ProjectBaseline $baseline): void
    {
        $baseline->setAsCurrent();
    }

    /**
     * Compare current state with baseline
     */
    public function compareWithBaseline(Project $project, ProjectBaseline $baseline): array
    {
        $activities = $project->activities;
        $baselineActivities = collect($baseline->schedule_snapshot);
        
        $comparisons = [];
        
        foreach ($activities as $activity) {
            $baselineActivity = $baselineActivities->firstWhere('id', $activity->id);
            
            if (!$baselineActivity) {
                continue;
            }
            
            $comparisons[] = [
                'activity_id' => $activity->id,
                'activity_name' => $activity->name,
                'baseline_start' => $baselineActivity['planned_start_date'] ?? null,
                'current_start' => $activity->planned_start_date,
                'baseline_end' => $baselineActivity['planned_end_date'] ?? null,
                'current_end' => $activity->planned_end_date,
                'baseline_budget' => $baselineActivity['planned_budget'] ?? 0,
                'current_budget' => $activity->planned_budget,
                'budget_variance' => $activity->planned_budget - ($baselineActivity['planned_budget'] ?? 0),
                'days_variance' => $this->calculateDaysVariance(
                    $baselineActivity['planned_end_date'] ?? null,
                    $activity->planned_end_date
                ),
            ];
        }
        
        // Calculate summary
        $totalBaselineBudget = $baselineActivities->sum('planned_budget');
        $totalCurrentBudget = $activities->sum('planned_budget');
        
        return [
            'comparisons' => $comparisons,
            'summary' => [
                'baseline_budget' => $totalBaselineBudget,
                'current_budget' => $totalCurrentBudget,
                'budget_variance' => $totalCurrentBudget - $totalBaselineBudget,
                'budget_variance_percent' => $totalBaselineBudget > 0 
                    ? round((($totalCurrentBudget - $totalBaselineBudget) / $totalBaselineBudget) * 100, 2)
                    : 0,
            ],
        ];
    }

    /**
     * Compare two baselines
     */
    public function compareBaselines(ProjectBaseline $baseline1, ProjectBaseline $baseline2): array
    {
        $activities1 = collect($baseline1->schedule_snapshot);
        $activities2 = collect($baseline2->schedule_snapshot);
        
        $comparisons = [];
        
        foreach ($activities1 as $activity1) {
            $activity2 = $activities2->firstWhere('id', $activity1['id']);
            
            if (!$activity2) {
                continue;
            }
            
            $comparisons[] = [
                'activity_id' => $activity1['id'],
                'activity_name' => $activity1['name'],
                'baseline1_budget' => $activity1['planned_budget'] ?? 0,
                'baseline2_budget' => $activity2['planned_budget'] ?? 0,
                'budget_variance' => ($activity2['planned_budget'] ?? 0) - ($activity1['planned_budget'] ?? 0),
            ];
        }
        
        return [
            'baseline1' => [
                'name' => $baseline1->baseline_name,
                'date' => $baseline1->baseline_date,
                'total_budget' => $baseline1->cost_snapshot['total_budget'] ?? 0,
            ],
            'baseline2' => [
                'name' => $baseline2->baseline_name,
                'date' => $baseline2->baseline_date,
                'total_budget' => $baseline2->cost_snapshot['total_budget'] ?? 0,
            ],
            'comparisons' => $comparisons,
        ];
    }

    /**
     * Get baseline history
     */
    public function getBaselineHistory(Project $project)
    {
        return $project->baselines()
            ->with('creator')
            ->orderBy('baseline_date', 'desc')
            ->get();
    }

    private function calculateDaysVariance($baselineDate, $currentDate): int
    {
        if (!$baselineDate || !$currentDate) {
            return 0;
        }
        
        $baseline = \Carbon\Carbon::parse($baselineDate);
        $current = \Carbon\Carbon::parse($currentDate);
        
        return $current->diffInDays($baseline, false);
    }
}
