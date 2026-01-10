<?php

namespace App\Http\Controllers;

use App\Models\ActivityDependency;
use App\Models\ScheduleActivity;
use Illuminate\Http\Request;

class DependencyController extends Controller
{
    public function predecessors($activityId)
    {
        $activity = ScheduleActivity::findOrFail($activityId);
        
        $predecessors = $activity->predecessors()
            ->with('predecessor')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $predecessors
        ]);
    }

    public function successors($activityId)
    {
        $activity = ScheduleActivity::findOrFail($activityId);
        
        $successors = $activity->successors()
            ->with('successor')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $successors
        ]);
    }

    public function addDependency(Request $request, $activityId)
    {
        $activity = ScheduleActivity::findOrFail($activityId);

        $validated = $request->validate([
            'predecessor_id' => 'required|exists:schedule_activities,id',
            'dependency_type' => 'required|in:FS,FF,SS,SF',
            'lag_days' => 'nullable|integer',
            'lag_type' => 'nullable|in:days,percentage',
        ]);

        $validated['successor_id'] = $activityId;
        $validated['project_schedule_id'] = $activity->project_schedule_id;

        // Check for circular dependencies
        if ($this->wouldCreateCircularDependency($validated['predecessor_id'], $activityId)) {
            return response()->json([
                'success' => false,
                'message' => 'This dependency would create a circular reference'
            ], 422);
        }

        $dependency = ActivityDependency::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dependency added successfully',
            'data' => $dependency->load(['predecessor', 'successor'])
        ], 201);
    }

    public function removeDependency($id)
    {
        $dependency = ActivityDependency::findOrFail($id);
        $dependency->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dependency removed successfully'
        ]);
    }

    protected function wouldCreateCircularDependency($predecessorId, $successorId, $visited = [])
    {
        if ($predecessorId == $successorId) {
            return true;
        }

        if (in_array($successorId, $visited)) {
            return false;
        }

        $visited[] = $successorId;

        $dependencies = ActivityDependency::where('predecessor_id', $successorId)->get();

        foreach ($dependencies as $dep) {
            if ($this->wouldCreateCircularDependency($predecessorId, $dep->successor_id, $visited)) {
                return true;
            }
        }

        return false;
    }
}
