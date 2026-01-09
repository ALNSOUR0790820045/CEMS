<?php

namespace App\Http\Controllers;

use App\Models\ProjectSchedule;
use App\Models\Project;
use App\Services\ScheduleCPMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectScheduleController extends Controller
{
    protected $cpmService;

    public function __construct(ScheduleCPMService $cpmService)
    {
        $this->cpmService = $cpmService;
    }

    public function index(Request $request)
    {
        $query = ProjectSchedule::with(['project', 'preparedBy', 'approvedBy']);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $schedules = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $schedules
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'schedule_type' => 'required|in:baseline,current,revised,what_if',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'working_days_per_week' => 'nullable|integer|min:1|max:7',
            'hours_per_day' => 'nullable|numeric|min:1|max:24',
            'calendar_id' => 'nullable|exists:schedule_calendars,id',
        ]);

        $validated['prepared_by_id'] = Auth::id();
        $validated['company_id'] = Auth::user()->company_id;
        $validated['duration_days'] = \Carbon\Carbon::parse($validated['start_date'])
            ->diffInDays(\Carbon\Carbon::parse($validated['end_date']));

        $schedule = ProjectSchedule::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Project schedule created successfully',
            'data' => $schedule->load(['project', 'preparedBy'])
        ], 201);
    }

    public function show($id)
    {
        $schedule = ProjectSchedule::with([
            'project',
            'preparedBy',
            'approvedBy',
            'activities',
            'baselines'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $schedule
        ]);
    }

    public function update(Request $request, $id)
    {
        $schedule = ProjectSchedule::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'schedule_type' => 'sometimes|in:baseline,current,revised,what_if',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'working_days_per_week' => 'nullable|integer|min:1|max:7',
            'hours_per_day' => 'nullable|numeric|min:1|max:24',
            'calendar_id' => 'nullable|exists:schedule_calendars,id',
            'status' => 'sometimes|in:draft,baseline,approved,superseded',
        ]);

        if (isset($validated['start_date']) && isset($validated['end_date'])) {
            $validated['duration_days'] = \Carbon\Carbon::parse($validated['start_date'])
                ->diffInDays(\Carbon\Carbon::parse($validated['end_date']));
        }

        $schedule->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Project schedule updated successfully',
            'data' => $schedule->load(['project', 'preparedBy', 'approvedBy'])
        ]);
    }

    public function destroy($id)
    {
        $schedule = ProjectSchedule::findOrFail($id);
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Project schedule deleted successfully'
        ]);
    }

    public function byProject($projectId)
    {
        $schedules = ProjectSchedule::where('project_id', $projectId)
            ->with(['preparedBy', 'approvedBy'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $schedules
        ]);
    }

    public function approve(Request $request, $id)
    {
        $schedule = ProjectSchedule::findOrFail($id);

        $schedule->update([
            'status' => 'approved',
            'approved_by_id' => Auth::id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schedule approved successfully',
            'data' => $schedule->load(['approvedBy'])
        ]);
    }

    public function calculate($id)
    {
        $schedule = ProjectSchedule::findOrFail($id);
        
        $this->cpmService->calculate($schedule);

        return response()->json([
            'success' => true,
            'message' => 'CPM calculation completed successfully',
            'data' => $schedule->fresh(['activities'])
        ]);
    }

    public function setBaseline(Request $request, $id)
    {
        $schedule = ProjectSchedule::findOrFail($id);

        $validated = $request->validate([
            'baseline_name' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $baselineNumber = 'BL-' . str_pad($schedule->baselines()->count() + 1, 3, '0', STR_PAD_LEFT);

        $baseline = $schedule->baselines()->create([
            'baseline_number' => $baselineNumber,
            'baseline_name' => $validated['baseline_name'],
            'baseline_date' => now(),
            'created_by_id' => Auth::id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Copy activity data to baseline
        foreach ($schedule->activities as $activity) {
            $baseline->activities()->create([
                'schedule_activity_id' => $activity->id,
                'planned_start' => $activity->planned_start,
                'planned_finish' => $activity->planned_finish,
                'planned_duration' => $activity->planned_duration,
                'budgeted_cost' => $activity->budgeted_cost,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Baseline created successfully',
            'data' => $baseline->load(['activities'])
        ], 201);
    }

    public function ganttData($id)
    {
        $schedule = ProjectSchedule::findOrFail($id);
        
        $activities = $schedule->activities()
            ->with(['predecessors.predecessor', 'successors.successor', 'responsible'])
            ->get();

        $ganttData = $activities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'code' => $activity->activity_code,
                'name' => $activity->name,
                'start' => $activity->early_start ?? $activity->planned_start,
                'end' => $activity->early_finish ?? $activity->planned_finish,
                'duration' => $activity->planned_duration,
                'progress' => $activity->percent_complete,
                'type' => $activity->activity_type,
                'parent' => $activity->parent_id,
                'is_critical' => $activity->is_critical,
                'dependencies' => $activity->predecessors->map(function ($dep) {
                    return [
                        'from' => $dep->predecessor_id,
                        'to' => $dep->successor_id,
                        'type' => $dep->dependency_type,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $ganttData
        ]);
    }

    public function criticalPath($id)
    {
        $schedule = ProjectSchedule::findOrFail($id);
        
        $criticalActivities = $this->cpmService->getCriticalPath($schedule);

        return response()->json([
            'success' => true,
            'data' => $criticalActivities
        ]);
    }
}
