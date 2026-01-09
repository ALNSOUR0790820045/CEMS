<?php

namespace App\Http\Controllers\Progress;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectBaseline;
use App\Services\BaselineService;
use Illuminate\Http\Request;

class BaselineController extends Controller
{
    protected $baselineService;

    public function __construct(BaselineService $baselineService)
    {
        $this->baselineService = $baselineService;
    }

    /**
     * Display baselines for a project
     */
    public function index(Project $project)
    {
        $baselines = $this->baselineService->getBaselineHistory($project);
        $currentBaseline = $project->getCurrentBaseline();
        
        return view('progress.baseline', compact('project', 'baselines', 'currentBaseline'));
    }

    /**
     * Store a new baseline
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'baseline_name' => 'required|string|max:255',
            'reason' => 'nullable|string',
        ]);
        
        $baseline = $this->baselineService->createBaseline(
            $project,
            $validated['baseline_name'],
            $validated['reason'] ?? null,
            auth()->id()
        );
        
        return redirect()
            ->route('progress.baseline.index', $project)
            ->with('success', 'تم إنشاء الخط الأساسي بنجاح');
    }

    /**
     * Set a baseline as current
     */
    public function setCurrent(Project $project, ProjectBaseline $baseline)
    {
        $this->baselineService->setCurrentBaseline($baseline);
        
        return back()->with('success', 'تم تعيين الخط الأساسي الحالي');
    }

    /**
     * Compare current state with baseline
     */
    public function compare(Project $project, ProjectBaseline $baseline)
    {
        $comparison = $this->baselineService->compareWithBaseline($project, $baseline);
        
        return view('progress.baseline-compare', compact('project', 'baseline', 'comparison'));
    }

    /**
     * Compare two baselines
     */
    public function compareBaselines(Request $request, Project $project)
    {
        $validated = $request->validate([
            'baseline1_id' => 'required|exists:project_baselines,id',
            'baseline2_id' => 'required|exists:project_baselines,id',
        ]);
        
        $baseline1 = ProjectBaseline::findOrFail($validated['baseline1_id']);
        $baseline2 = ProjectBaseline::findOrFail($validated['baseline2_id']);
        
        $comparison = $this->baselineService->compareBaselines($baseline1, $baseline2);
        
        return view('progress.baseline-compare-two', compact('project', 'comparison'));
    }
}

