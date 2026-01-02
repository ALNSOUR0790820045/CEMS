<?php

namespace App\Http\Controllers\Progress;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProgressTrackingService;
use App\Services\EVMCalculationService;
use Illuminate\Http\Request;

class ForecastingController extends Controller
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
     * Display forecasting analysis for a project
     */
    public function index(Project $project)
    {
        $latestSnapshot = $this->progressService->getLatestSnapshot($project);
        
        if (!$latestSnapshot) {
            return redirect()
                ->route('progress.dashboard', ['project_id' => $project->id])
                ->with('error', 'لا توجد بيانات كافية للتنبؤ');
        }
        
        $scenarios = $this->evmService->generateScenarios($project, $latestSnapshot);
        
        return view('progress.forecasting', compact('project', 'latestSnapshot', 'scenarios'));
    }

    /**
     * Calculate custom scenario
     */
    public function customScenario(Request $request, Project $project)
    {
        $validated = $request->validate([
            'spi_adjustment' => 'required|numeric|min:-50|max:50',
        ]);
        
        $latestSnapshot = $this->progressService->getLatestSnapshot($project);
        
        if (!$latestSnapshot) {
            return response()->json(['error' => 'No data available'], 404);
        }
        
        $currentSpi = $latestSnapshot->schedule_performance_index_spi;
        $adjustmentFactor = 1 + ($validated['spi_adjustment'] / 100);
        $newSpi = $currentSpi * $adjustmentFactor;
        
        $forecastedDate = $this->evmService->calculateForecastedCompletionDate($project, $newSpi);
        $forecastedEac = $latestSnapshot->budget_at_completion_bac / 
            ($latestSnapshot->cost_performance_index_cpi * $adjustmentFactor);
        
        return response()->json([
            'spi' => round($newSpi, 3),
            'completion_date' => $forecastedDate->format('Y-m-d'),
            'eac' => round($forecastedEac, 2),
            'delay_days' => $forecastedDate->diffInDays($project->planned_end_date, false),
        ]);
    }
}

