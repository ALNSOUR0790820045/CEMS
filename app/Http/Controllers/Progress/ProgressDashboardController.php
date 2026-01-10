<?php

namespace App\Http\Controllers\Progress;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProgressTrackingService;
use App\Services\EVMCalculationService;
use Illuminate\Http\Request;

class ProgressDashboardController extends Controller
{
    protected $progressService;
    protected $evmService;

    public function __construct(
        ProgressTrackingService $progressService,
        EVMCalculationService $evmService
    ) {
        $this->progressService = $progressService;
        $this->evmService = $evmService;
    }

    /**
     * Display the main EVM dashboard for a project
     */
    public function index(Request $request)
    {
        $projects = Project::with(['company', 'projectManager'])
            ->active()
            ->get();
        
        $selectedProject = null;
        $dashboardData = null;
        
        if ($request->has('project_id')) {
            $selectedProject = Project::with(['activities', 'progressSnapshots'])
                ->findOrFail($request->project_id);
            
            $dashboardData = $this->progressService->getDashboardData($selectedProject);
        }
        
        return view('progress.dashboard', compact('projects', 'selectedProject', 'dashboardData'));
    }

    /**
     * Get chart data for AJAX requests
     */
    public function chartData(Project $project, Request $request)
    {
        $months = $request->input('months', 6);
        $trendData = $this->evmService->getTrendData($project, $months);
        
        return response()->json($trendData);
    }

    /**
     * Get latest snapshot data
     */
    public function latestSnapshot(Project $project)
    {
        $snapshot = $this->progressService->getLatestSnapshot($project);
        
        if (!$snapshot) {
            return response()->json(['error' => 'No snapshots found'], 404);
        }
        
        $healthStatus = $this->evmService->getProjectHealthStatus($snapshot);
        
        return response()->json([
            'snapshot' => $snapshot,
            'health_status' => $healthStatus,
        ]);
    }
}
