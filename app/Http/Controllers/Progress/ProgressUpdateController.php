<?php

namespace App\Http\Controllers\Progress;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProgressTrackingService;
use App\Services\EVMCalculationService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProgressUpdateController extends Controller
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
     * Show the form for creating a new progress update
     */
    public function create(Project $project)
    {
        $latestSnapshot = $this->progressService->getLatestSnapshot($project);
        
        return view('progress.update', compact('project', 'latestSnapshot'));
    }

    /**
     * Store a newly created progress update
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'snapshot_date' => 'required|date',
            'overall_progress_percent' => 'required|numeric|min:0|max:100',
            'physical_progress_percent' => 'nullable|numeric|min:0|max:100',
            'actual_cost_ac' => 'required|numeric|min:0',
            'comments' => 'nullable|string',
        ]);
        
        $snapshot = $this->progressService->createProgressSnapshot(
            $project,
            $validated,
            auth()->id()
        );
        
        return redirect()
            ->route('progress.dashboard', ['project_id' => $project->id])
            ->with('success', 'تم تحديث التقدم بنجاح');
    }

    /**
     * Preview EVM calculations before saving
     */
    public function preview(Request $request, Project $project)
    {
        $validated = $request->validate([
            'snapshot_date' => 'required|date',
            'overall_progress_percent' => 'required|numeric|min:0|max:100',
            'physical_progress_percent' => 'nullable|numeric|min:0|max:100',
            'actual_cost_ac' => 'required|numeric|min:0',
        ]);
        
        $snapshotDate = Carbon::parse($validated['snapshot_date']);
        $evmMetrics = $this->evmService->calculateEVMMetrics($project, $snapshotDate, $validated);
        
        return response()->json($evmMetrics);
    }

    /**
     * Show progress history
     */
    public function history(Project $project)
    {
        $snapshots = $project->progressSnapshots()
            ->with('reporter')
            ->orderBy('snapshot_date', 'desc')
            ->paginate(20);
        
        return view('progress.history', compact('project', 'snapshots'));
    }
}
