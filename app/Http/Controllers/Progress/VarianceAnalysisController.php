<?php

namespace App\Http\Controllers\Progress;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProgressTrackingService;
use Illuminate\Http\Request;

class VarianceAnalysisController extends Controller
{
    protected $progressService;

    public function __construct(ProgressTrackingService $progressService)
    {
        $this->progressService = $progressService;
    }

    /**
     * Display variance analysis for a project
     */
    public function index(Project $project)
    {
        $latestSnapshot = $this->progressService->getLatestSnapshot($project);
        $activityProgress = $this->progressService->calculateActivityProgress($project);
        $delayedActivities = $this->progressService->getDelayedActivities($project);
        $overBudgetActivities = $this->progressService->getOverBudgetActivities($project);
        
        // Top 10 delayed activities
        $topDelayed = collect($activityProgress)
            ->filter(fn($a) => $a['is_delayed'])
            ->sortBy('schedule_variance')
            ->take(10);
        
        // Top 10 cost overruns
        $topOverBudget = collect($activityProgress)
            ->filter(fn($a) => $a['is_over_budget'])
            ->sortBy('cost_variance')
            ->take(10);
        
        return view('progress.variance-analysis', compact(
            'project',
            'latestSnapshot',
            'activityProgress',
            'topDelayed',
            'topOverBudget',
            'delayedActivities',
            'overBudgetActivities'
        ));
    }

    /**
     * Get variance data for specific activity
     */
    public function activityDetail(Project $project, $activityId)
    {
        $activity = $project->activities()->findOrFail($activityId);
        
        $sv = $activity->getScheduleVariance();
        $cv = $activity->getCostVariance();
        
        return response()->json([
            'activity' => $activity,
            'schedule_variance' => $sv,
            'cost_variance' => $cv,
        ]);
    }
}

