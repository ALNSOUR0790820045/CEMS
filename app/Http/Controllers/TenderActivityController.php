<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use App\Models\TenderActivity;
use App\Models\TenderWBS;
use App\Models\TenderActivityDependency;
use App\Services\CPMCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TenderActivityController extends Controller
{
    protected $cpmService;

    public function __construct(CPMCalculationService $cpmService)
    {
        $this->cpmService = $cpmService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $tenderId)
    {
        $tender = Tender::findOrFail($tenderId);
        
        $query = TenderActivity::where('tender_id', $tenderId)
            ->with(['wbs', 'predecessors', 'successors']);

        // Apply filters
        if ($request->filled('wbs_id')) {
            $query->where('tender_wbs_id', $request->wbs_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('is_critical')) {
            $query->where('is_critical', $request->is_critical);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('activity_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $activities = $query->orderBy('sort_order')->paginate(50);
        $wbsItems = TenderWBS::where('tender_id', $tenderId)->get();

        return view('tender-activities.index', compact('tender', 'activities', 'wbsItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($tenderId)
    {
        $tender = Tender::findOrFail($tenderId);
        $wbsItems = TenderWBS::where('tender_id', $tenderId)->get();
        $activities = TenderActivity::where('tender_id', $tenderId)->get();
        
        // Generate next activity code
        $lastActivity = TenderActivity::where('tender_id', $tenderId)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastActivity ? intval(substr($lastActivity->activity_code, -3)) + 1 : 1;
        $activityCode = 'TACT-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return view('tender-activities.create', compact('tender', 'wbsItems', 'activities', 'activityCode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $tenderId)
    {
        $validated = $request->validate([
            'activity_code' => 'required|unique:tender_activities,activity_code',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'tender_wbs_id' => 'nullable|exists:tender_wbs,id',
            'duration_days' => 'required|integer|min:1',
            'effort_hours' => 'nullable|numeric|min:0',
            'type' => 'required|in:task,milestone,summary',
            'priority' => 'required|in:low,medium,high,critical',
            'estimated_cost' => 'nullable|numeric|min:0',
            'predecessors' => 'nullable|array',
            'predecessors.*.id' => 'required_with:predecessors|exists:tender_activities,id',
            'predecessors.*.type' => 'required_with:predecessors|in:FS,SS,FF,SF',
            'predecessors.*.lag_days' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            $activity = TenderActivity::create([
                'tender_id' => $tenderId,
                'activity_code' => $validated['activity_code'],
                'name' => $validated['name'],
                'name_en' => $validated['name_en'] ?? null,
                'description' => $validated['description'] ?? null,
                'tender_wbs_id' => $validated['tender_wbs_id'] ?? null,
                'duration_days' => $validated['duration_days'],
                'effort_hours' => $validated['effort_hours'] ?? 0,
                'type' => $validated['type'],
                'priority' => $validated['priority'],
                'estimated_cost' => $validated['estimated_cost'] ?? 0,
                'sort_order' => TenderActivity::where('tender_id', $tenderId)->max('sort_order') + 1,
            ]);

            // Add predecessors
            if (!empty($validated['predecessors'])) {
                foreach ($validated['predecessors'] as $predecessor) {
                    TenderActivityDependency::create([
                        'predecessor_id' => $predecessor['id'],
                        'successor_id' => $activity->id,
                        'type' => $predecessor['type'],
                        'lag_days' => $predecessor['lag_days'] ?? 0,
                    ]);
                }
            }

            // Recalculate CPM
            $this->cpmService->calculateCPM($tenderId);

            DB::commit();

            return redirect()->route('tender-activities.index', $tenderId)
                ->with('success', 'Activity created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create activity: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($tenderId, $id)
    {
        $activity = TenderActivity::with(['tender', 'wbs', 'predecessors.predecessor', 'successors.successor'])
            ->findOrFail($id);

        return view('tender-activities.show', compact('activity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($tenderId, $id)
    {
        $tender = Tender::findOrFail($tenderId);
        $activity = TenderActivity::with(['predecessors'])->findOrFail($id);
        $wbsItems = TenderWBS::where('tender_id', $tenderId)->get();
        $activities = TenderActivity::where('tender_id', $tenderId)
            ->where('id', '!=', $id)
            ->get();

        return view('tender-activities.edit', compact('tender', 'activity', 'wbsItems', 'activities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $tenderId, $id)
    {
        $activity = TenderActivity::findOrFail($id);

        $validated = $request->validate([
            'activity_code' => 'required|unique:tender_activities,activity_code,' . $id,
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'tender_wbs_id' => 'nullable|exists:tender_wbs,id',
            'duration_days' => 'required|integer|min:1',
            'effort_hours' => 'nullable|numeric|min:0',
            'type' => 'required|in:task,milestone,summary',
            'priority' => 'required|in:low,medium,high,critical',
            'estimated_cost' => 'nullable|numeric|min:0',
            'predecessors' => 'nullable|array',
            'predecessors.*.id' => 'required_with:predecessors|exists:tender_activities,id',
            'predecessors.*.type' => 'required_with:predecessors|in:FS,SS,FF,SF',
            'predecessors.*.lag_days' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            $activity->update([
                'activity_code' => $validated['activity_code'],
                'name' => $validated['name'],
                'name_en' => $validated['name_en'] ?? null,
                'description' => $validated['description'] ?? null,
                'tender_wbs_id' => $validated['tender_wbs_id'] ?? null,
                'duration_days' => $validated['duration_days'],
                'effort_hours' => $validated['effort_hours'] ?? 0,
                'type' => $validated['type'],
                'priority' => $validated['priority'],
                'estimated_cost' => $validated['estimated_cost'] ?? 0,
            ]);

            // Update predecessors
            $activity->predecessors()->delete();
            if (!empty($validated['predecessors'])) {
                foreach ($validated['predecessors'] as $predecessor) {
                    TenderActivityDependency::create([
                        'predecessor_id' => $predecessor['id'],
                        'successor_id' => $activity->id,
                        'type' => $predecessor['type'],
                        'lag_days' => $predecessor['lag_days'] ?? 0,
                    ]);
                }
            }

            // Recalculate CPM
            $this->cpmService->calculateCPM($tenderId);

            DB::commit();

            return redirect()->route('tender-activities.index', $tenderId)
                ->with('success', 'Activity updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update activity: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($tenderId, $id)
    {
        try {
            $activity = TenderActivity::findOrFail($id);
            $activity->delete();

            // Recalculate CPM after deletion
            $this->cpmService->calculateCPM($tenderId);

            return redirect()->route('tender-activities.index', $tenderId)
                ->with('success', 'Activity deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete activity: ' . $e->getMessage());
        }
    }

    /**
     * Show Gantt chart
     */
    public function gantt($tenderId)
    {
        $tender = Tender::findOrFail($tenderId);
        $activities = TenderActivity::where('tender_id', $tenderId)
            ->with(['wbs', 'predecessors.predecessor', 'successors.successor'])
            ->orderBy('sort_order')
            ->get();

        $dependencies = TenderActivityDependency::whereIn('predecessor_id', $activities->pluck('id'))
            ->get();

        return view('tender-activities.gantt', compact('tender', 'activities', 'dependencies'));
    }

    /**
     * Show CPM analysis
     */
    public function cpmAnalysis($tenderId)
    {
        $tender = Tender::findOrFail($tenderId);
        
        // Calculate CPM
        $cpmResult = $this->cpmService->calculateCPM($tenderId);
        
        // Get critical path activities
        $criticalActivities = $this->cpmService->getCriticalPath($tenderId);
        
        // Get all activities
        $activities = TenderActivity::where('tender_id', $tenderId)
            ->with(['wbs', 'predecessors.predecessor', 'successors.successor'])
            ->orderBy('early_start')
            ->get();

        // Get network diagram data
        $networkData = $this->cpmService->getNetworkDiagram($tenderId);

        return view('tender-activities.cpm-analysis', compact(
            'tender', 
            'activities', 
            'criticalActivities', 
            'cpmResult',
            'networkData'
        ));
    }

    /**
     * Recalculate CPM
     */
    public function recalculateCPM($tenderId)
    {
        try {
            $result = $this->cpmService->calculateCPM($tenderId);

            return back()->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to calculate CPM: ' . $e->getMessage());
        }
    }
}
