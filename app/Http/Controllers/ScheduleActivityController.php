<?php

namespace App\Http\Controllers;

use App\Models\ScheduleActivity;
use App\Models\ProjectSchedule;
use Illuminate\Http\Request;

class ScheduleActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = ScheduleActivity::with(['schedule', 'wbs', 'responsible']);

        if ($request->has('project_schedule_id')) {
            $query->where('project_schedule_id', $request->project_schedule_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('is_critical')) {
            $query->where('is_critical', $request->boolean('is_critical'));
        }

        $activities = $query->paginate($request->get('per_page', 50));

        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_schedule_id' => 'required|exists:project_schedules,id',
            'activity_code' => [
                'required',
                'string',
                \Illuminate\Validation\Rule::unique('schedule_activities')->where(function ($query) use ($request) {
                    return $query->where('project_schedule_id', $request->project_schedule_id);
                })
            ],
            'wbs_id' => 'nullable|exists:project_wbs,id',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'activity_type' => 'required|in:task,milestone,summary,hammock',
            'parent_id' => 'nullable|exists:schedule_activities,id',
            'planned_start' => 'nullable|date',
            'planned_finish' => 'nullable|date',
            'planned_duration' => 'required|integer|min:0',
            'constraint_type' => 'nullable|in:asap,alap,snet,snlt,fnet,fnlt,mso,mfo',
            'constraint_date' => 'nullable|date',
            'responsible_id' => 'nullable|exists:users,id',
            'budgeted_cost' => 'nullable|numeric|min:0',
        ]);

        $activity = ScheduleActivity::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Activity created successfully',
            'data' => $activity->load(['schedule', 'responsible'])
        ], 201);
    }

    public function show($id)
    {
        $activity = ScheduleActivity::with([
            'schedule',
            'wbs',
            'parent',
            'children',
            'responsible',
            'predecessors.predecessor',
            'successors.successor',
            'resources'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $activity
        ]);
    }

    public function update(Request $request, $id)
    {
        $activity = ScheduleActivity::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'activity_type' => 'sometimes|in:task,milestone,summary,hammock',
            'planned_start' => 'nullable|date',
            'planned_finish' => 'nullable|date',
            'planned_duration' => 'sometimes|integer|min:0',
            'actual_start' => 'nullable|date',
            'actual_finish' => 'nullable|date',
            'percent_complete' => 'nullable|numeric|min:0|max:100',
            'constraint_type' => 'nullable|in:asap,alap,snet,snlt,fnet,fnlt,mso,mfo',
            'constraint_date' => 'nullable|date',
            'responsible_id' => 'nullable|exists:users,id',
            'budgeted_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'status' => 'sometimes|in:not_started,in_progress,completed,on_hold',
            'notes' => 'nullable|string',
        ]);

        $activity->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Activity updated successfully',
            'data' => $activity->fresh(['schedule', 'responsible'])
        ]);
    }

    public function destroy($id)
    {
        $activity = ScheduleActivity::findOrFail($id);
        $activity->delete();

        return response()->json([
            'success' => true,
            'message' => 'Activity deleted successfully'
        ]);
    }

    public function bySchedule($scheduleId)
    {
        $activities = ScheduleActivity::where('project_schedule_id', $scheduleId)
            ->with(['wbs', 'responsible', 'predecessors', 'successors'])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }

    public function updateProgress(Request $request, $id)
    {
        $activity = ScheduleActivity::findOrFail($id);

        $validated = $request->validate([
            'percent_complete' => 'required|numeric|min:0|max:100',
            'actual_start' => 'nullable|date',
            'actual_finish' => 'nullable|date',
            'actual_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Calculate earned value
        if (isset($validated['percent_complete'])) {
            $validated['earned_value'] = ($activity->budgeted_cost * $validated['percent_complete']) / 100;
        }

        // Auto-update status based on progress
        if ($validated['percent_complete'] == 0) {
            $validated['status'] = 'not_started';
        } elseif ($validated['percent_complete'] == 100) {
            $validated['status'] = 'completed';
        } else {
            $validated['status'] = 'in_progress';
        }

        $activity->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Activity progress updated successfully',
            'data' => $activity
        ]);
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'activities' => 'required|array',
            'activities.*.id' => 'required|exists:schedule_activities,id',
            'activities.*.percent_complete' => 'nullable|numeric|min:0|max:100',
            'activities.*.actual_start' => 'nullable|date',
            'activities.*.actual_finish' => 'nullable|date',
            'activities.*.status' => 'nullable|in:not_started,in_progress,completed,on_hold',
        ]);

        $updated = [];
        foreach ($validated['activities'] as $activityData) {
            $activity = ScheduleActivity::find($activityData['id']);
            if ($activity) {
                $activity->update($activityData);
                $updated[] = $activity;
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($updated) . ' activities updated successfully',
            'data' => $updated
        ]);
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'project_schedule_id' => 'required|exists:project_schedules,id',
            'activities' => 'required|array',
            'activities.*.activity_code' => 'required|string',
            'activities.*.name' => 'required|string',
            'activities.*.planned_duration' => 'required|integer|min:0',
        ]);

        $imported = [];
        foreach ($validated['activities'] as $activityData) {
            $activityData['project_schedule_id'] = $validated['project_schedule_id'];
            
            $activity = ScheduleActivity::create($activityData);
            $imported[] = $activity;
        }

        return response()->json([
            'success' => true,
            'message' => count($imported) . ' activities imported successfully',
            'data' => $imported
        ], 201);
    }
}
