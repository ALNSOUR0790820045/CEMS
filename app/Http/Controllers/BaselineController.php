<?php

namespace App\Http\Controllers;

use App\Models\ProjectSchedule;
use App\Models\ScheduleBaseline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BaselineController extends Controller
{
    public function bySchedule($scheduleId)
    {
        $baselines = ScheduleBaseline::where('project_schedule_id', $scheduleId)
            ->with(['createdBy', 'activities'])
            ->orderBy('baseline_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $baselines
        ]);
    }

    public function create(Request $request, $scheduleId)
    {
        $schedule = ProjectSchedule::findOrFail($scheduleId);

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

    public function compare(Request $request, $scheduleId)
    {
        $schedule = ProjectSchedule::with('activities')->findOrFail($scheduleId);

        $baselineId = $request->get('baseline_id');
        $baseline = ScheduleBaseline::with('activities.activity')->findOrFail($baselineId);

        $comparison = [];

        foreach ($schedule->activities as $activity) {
            $baselineActivity = $baseline->activities
                ->where('schedule_activity_id', $activity->id)
                ->first();

            if ($baselineActivity) {
                $comparison[] = [
                    'activity_id' => $activity->id,
                    'activity_name' => $activity->name,
                    'baseline_start' => $baselineActivity->planned_start,
                    'baseline_finish' => $baselineActivity->planned_finish,
                    'baseline_duration' => $baselineActivity->planned_duration,
                    'baseline_cost' => $baselineActivity->budgeted_cost,
                    'current_start' => $activity->planned_start,
                    'current_finish' => $activity->planned_finish,
                    'current_duration' => $activity->planned_duration,
                    'current_cost' => $activity->budgeted_cost,
                    'start_variance' => $activity->planned_start && $baselineActivity->planned_start
                        ? \Carbon\Carbon::parse($activity->planned_start)
                            ->diffInDays(\Carbon\Carbon::parse($baselineActivity->planned_start), false)
                        : 0,
                    'finish_variance' => $activity->planned_finish && $baselineActivity->planned_finish
                        ? \Carbon\Carbon::parse($activity->planned_finish)
                            ->diffInDays(\Carbon\Carbon::parse($baselineActivity->planned_finish), false)
                        : 0,
                    'duration_variance' => $activity->planned_duration - $baselineActivity->planned_duration,
                    'cost_variance' => $activity->budgeted_cost - $baselineActivity->budgeted_cost,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'schedule' => $schedule,
                'baseline' => $baseline,
                'comparison' => $comparison,
            ]
        ]);
    }
}
