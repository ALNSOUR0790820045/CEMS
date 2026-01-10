<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectProgressSnapshot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProgressTrackingService
{
    protected $evmService;

    public function __construct(EVMCalculationService $evmService)
    {
        $this->evmService = $evmService;
    }

    /**
     * Create a new progress snapshot with EVM calculations
     */
    public function createProgressSnapshot(Project $project, array $data, int $userId): ProjectProgressSnapshot
    {
        $snapshotDate = Carbon::parse($data['snapshot_date']);
        
        // Calculate all EVM metrics
        $evmMetrics = $this->evmService->calculateEVMMetrics($project, $snapshotDate, $data);
        
        // Merge with user input
        $snapshotData = array_merge($evmMetrics, [
            'project_id' => $project->id,
            'snapshot_date' => $snapshotDate,
            'comments' => $data['comments'] ?? null,
            'reported_by' => $userId,
        ]);
        
        // Create or update snapshot
        $snapshot = ProjectProgressSnapshot::updateOrCreate(
            [
                'project_id' => $project->id,
                'snapshot_date' => $snapshotDate,
            ],
            $snapshotData
        );
        
        // Update project's overall progress
        $project->update([
            'overall_progress' => $evmMetrics['overall_progress_percent']
        ]);
        
        return $snapshot;
    }

    /**
     * Get latest snapshot for a project
     */
    public function getLatestSnapshot(Project $project): ?ProjectProgressSnapshot
    {
        return $project->progressSnapshots()
            ->latest('snapshot_date')
            ->first();
    }

    /**
     * Get snapshots for a date range
     */
    public function getSnapshotsForPeriod(Project $project, Carbon $startDate, Carbon $endDate)
    {
        return $project->progressSnapshots()
            ->whereBetween('snapshot_date', [$startDate, $endDate])
            ->orderBy('snapshot_date')
            ->get();
    }

    /**
     * Calculate activity-level progress
     */
    public function calculateActivityProgress(Project $project): array
    {
        return $project->activities()
            ->select('id', 'name', 'progress_percent', 'planned_budget', 'actual_cost', 'is_critical', 'status')
            ->get()
            ->map(function ($activity) {
                $ev = ($activity->progress_percent / 100) * $activity->planned_budget;
                $sv = $ev - $activity->planned_budget; // Simplified
                $cv = $ev - $activity->actual_cost;
                
                return [
                    'id' => $activity->id,
                    'name' => $activity->name,
                    'progress' => $activity->progress_percent,
                    'status' => $activity->status,
                    'is_critical' => $activity->is_critical,
                    'schedule_variance' => round($sv, 2),
                    'cost_variance' => round($cv, 2),
                    'is_delayed' => $sv < 0,
                    'is_over_budget' => $cv < 0,
                ];
            })
            ->toArray();
    }

    /**
     * Get delayed activities
     */
    public function getDelayedActivities(Project $project)
    {
        return $project->activities()
            ->where('planned_end_date', '<', now())
            ->where('status', '!=', 'completed')
            ->orderBy('planned_end_date')
            ->get();
    }

    /**
     * Get activities over budget
     */
    public function getOverBudgetActivities(Project $project)
    {
        return $project->activities()
            ->whereRaw('actual_cost > planned_budget')
            ->orderByRaw('(actual_cost - planned_budget) DESC')
            ->get();
    }

    /**
     * Calculate weekly/monthly progress rate
     */
    public function calculateProgressRate(Project $project, string $period = 'week'): float
    {
        $days = $period === 'week' ? 7 : 30;
        $startDate = now()->subDays($days);
        
        $snapshots = $project->progressSnapshots()
            ->whereBetween('snapshot_date', [$startDate, now()])
            ->orderBy('snapshot_date')
            ->get();
        
        if ($snapshots->count() < 2) {
            return 0;
        }
        
        $firstSnapshot = $snapshots->first();
        $lastSnapshot = $snapshots->last();
        
        return $lastSnapshot->overall_progress_percent - $firstSnapshot->overall_progress_percent;
    }

    /**
     * Get dashboard summary data
     */
    public function getDashboardData(Project $project): array
    {
        $latestSnapshot = $this->getLatestSnapshot($project);
        
        if (!$latestSnapshot) {
            return $this->getEmptyDashboardData($project);
        }
        
        $trendData = $this->evmService->getTrendData($project, 6);
        $healthStatus = $this->evmService->getProjectHealthStatus($latestSnapshot);
        $delayedActivities = $this->getDelayedActivities($project);
        $overBudgetActivities = $this->getOverBudgetActivities($project);
        
        return [
            'snapshot' => $latestSnapshot,
            'health_status' => $healthStatus,
            'trend_data' => $trendData,
            'alerts' => [
                'delayed_activities' => $delayedActivities->count(),
                'over_budget_activities' => $overBudgetActivities->count(),
                'critical_delayed' => $delayedActivities->where('is_critical', true)->count(),
            ],
            'quick_stats' => [
                'budget_spent_percent' => $latestSnapshot->budget_at_completion_bac > 0 
                    ? round(($latestSnapshot->actual_cost_ac / $latestSnapshot->budget_at_completion_bac) * 100, 1)
                    : 0,
                'schedule_complete_percent' => $latestSnapshot->overall_progress_percent,
                'days_to_completion' => now()->diffInDays($latestSnapshot->forecasted_completion_date, false),
            ],
            'delayed_activities' => $delayedActivities->take(10),
            'over_budget_activities' => $overBudgetActivities->take(10),
        ];
    }

    private function getEmptyDashboardData(Project $project): array
    {
        return [
            'snapshot' => null,
            'health_status' => null,
            'trend_data' => [
                'dates' => [],
                'pv' => [],
                'ev' => [],
                'ac' => [],
                'spi' => [],
                'cpi' => [],
            ],
            'alerts' => [
                'delayed_activities' => 0,
                'over_budget_activities' => 0,
                'critical_delayed' => 0,
            ],
            'quick_stats' => [
                'budget_spent_percent' => 0,
                'schedule_complete_percent' => 0,
                'days_to_completion' => 0,
            ],
            'delayed_activities' => collect(),
            'over_budget_activities' => collect(),
        ];
    }

    /**
     * Update project progress from activity completion
     */
    public function updateProgressFromActivities(Project $project): void
    {
        $activities = $project->activities;
        
        if ($activities->isEmpty()) {
            return;
        }
        
        // Calculate weighted average progress
        $totalWeight = $activities->sum('weight');
        
        if ($totalWeight > 0) {
            $weightedProgress = $activities->sum(function ($activity) {
                return $activity->progress_percent * $activity->weight;
            }) / $totalWeight;
            
            $project->update([
                'overall_progress' => round($weightedProgress, 2)
            ]);
        }
    }
}
