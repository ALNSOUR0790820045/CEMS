<?php

namespace App\Http\Controllers;

use App\Models\ProjectSchedule;
use App\Models\ScheduleActivity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleReportController extends Controller
{
    public function summary($projectId)
    {
        $schedules = ProjectSchedule::where('project_id', $projectId)
            ->with('activities')
            ->get();

        $summary = [
            'total_schedules' => $schedules->count(),
            'total_activities' => 0,
            'critical_activities' => 0,
            'completed_activities' => 0,
            'in_progress_activities' => 0,
            'not_started_activities' => 0,
            'total_budget' => 0,
            'total_actual_cost' => 0,
            'total_earned_value' => 0,
        ];

        foreach ($schedules as $schedule) {
            $summary['total_activities'] += $schedule->activities->count();
            $summary['critical_activities'] += $schedule->activities->where('is_critical', true)->count();
            $summary['completed_activities'] += $schedule->activities->where('status', 'completed')->count();
            $summary['in_progress_activities'] += $schedule->activities->where('status', 'in_progress')->count();
            $summary['not_started_activities'] += $schedule->activities->where('status', 'not_started')->count();
            $summary['total_budget'] += $schedule->activities->sum('budgeted_cost');
            $summary['total_actual_cost'] += $schedule->activities->sum('actual_cost');
            $summary['total_earned_value'] += $schedule->activities->sum('earned_value');
        }

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    public function criticalActivities($projectId)
    {
        $schedules = ProjectSchedule::where('project_id', $projectId)->pluck('id');

        $criticalActivities = ScheduleActivity::whereIn('project_schedule_id', $schedules)
            ->where('is_critical', true)
            ->with(['schedule', 'responsible'])
            ->orderBy('early_start')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $criticalActivities
        ]);
    }

    public function variance($projectId)
    {
        $schedules = ProjectSchedule::where('project_id', $projectId)->pluck('id');

        $activities = ScheduleActivity::whereIn('project_schedule_id', $schedules)
            ->whereNotNull('actual_start')
            ->with('schedule')
            ->get();

        $variance = $activities->map(function ($activity) {
            return [
                'activity_id' => $activity->id,
                'activity_name' => $activity->name,
                'schedule_name' => $activity->schedule->name,
                'planned_start' => $activity->planned_start,
                'actual_start' => $activity->actual_start,
                'start_variance' => $activity->actual_start && $activity->planned_start
                    ? Carbon::parse($activity->actual_start)->diffInDays(Carbon::parse($activity->planned_start), false)
                    : 0,
                'planned_finish' => $activity->planned_finish,
                'actual_finish' => $activity->actual_finish,
                'finish_variance' => $activity->actual_finish && $activity->planned_finish
                    ? Carbon::parse($activity->actual_finish)->diffInDays(Carbon::parse($activity->planned_finish), false)
                    : 0,
                'cost_variance' => $activity->actual_cost - $activity->budgeted_cost,
                'schedule_variance' => $activity->earned_value - $activity->budgeted_cost,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $variance
        ]);
    }

    public function lookAhead(Request $request, $projectId)
    {
        $weeks = $request->get('weeks', 2);
        $fromDate = Carbon::parse($request->get('from_date', now()));
        $toDate = $fromDate->copy()->addWeeks($weeks);

        $schedules = ProjectSchedule::where('project_id', $projectId)->pluck('id');

        $activities = ScheduleActivity::whereIn('project_schedule_id', $schedules)
            ->where(function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('planned_start', [$fromDate, $toDate])
                    ->orWhereBetween('planned_finish', [$fromDate, $toDate]);
            })
            ->with(['schedule', 'responsible'])
            ->orderBy('planned_start')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'from_date' => $fromDate->toDateString(),
                'to_date' => $toDate->toDateString(),
                'activities' => $activities,
            ]
        ]);
    }

    public function milestoneStatus($projectId)
    {
        $schedules = ProjectSchedule::where('project_id', $projectId)->pluck('id');

        $milestones = ScheduleActivity::whereIn('project_schedule_id', $schedules)
            ->where('activity_type', 'milestone')
            ->with(['schedule', 'responsible'])
            ->orderBy('planned_finish')
            ->get();

        $milestoneStatus = $milestones->map(function ($milestone) {
            $status = 'on_track';
            
            if ($milestone->actual_finish) {
                if ($milestone->actual_finish <= $milestone->planned_finish) {
                    $status = 'completed_on_time';
                } else {
                    $status = 'completed_late';
                }
            } elseif ($milestone->planned_finish < now()) {
                $status = 'overdue';
            }

            return [
                'milestone_id' => $milestone->id,
                'milestone_name' => $milestone->name,
                'schedule_name' => $milestone->schedule->name,
                'planned_finish' => $milestone->planned_finish,
                'actual_finish' => $milestone->actual_finish,
                'status' => $status,
                'responsible' => $milestone->responsible,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $milestoneStatus
        ]);
    }

    public function resourceHistogram($projectId)
    {
        $schedules = ProjectSchedule::where('project_id', $projectId)->pluck('id');

        $activities = ScheduleActivity::whereIn('project_schedule_id', $schedules)
            ->with('resources')
            ->get();

        // Group resources by type and date
        $histogram = [];

        foreach ($activities as $activity) {
            foreach ($activity->resources as $resource) {
                $key = $resource->resource_type;
                
                if (!isset($histogram[$key])) {
                    $histogram[$key] = [
                        'resource_type' => $key,
                        'planned_units' => 0,
                        'actual_units' => 0,
                        'planned_cost' => 0,
                        'actual_cost' => 0,
                    ];
                }

                $histogram[$key]['planned_units'] += $resource->planned_units;
                $histogram[$key]['actual_units'] += $resource->actual_units;
                $histogram[$key]['planned_cost'] += $resource->planned_cost;
                $histogram[$key]['actual_cost'] += $resource->actual_cost;
            }
        }

        return response()->json([
            'success' => true,
            'data' => array_values($histogram)
        ]);
    }

    public function sCurve($projectId)
    {
        $schedules = ProjectSchedule::where('project_id', $projectId)->pluck('id');

        $activities = ScheduleActivity::whereIn('project_schedule_id', $schedules)
            ->orderBy('planned_start')
            ->get();

        $sCurve = [];
        $cumulativePlanned = 0;
        $cumulativeEarned = 0;
        $cumulativeActual = 0;

        foreach ($activities as $activity) {
            $cumulativePlanned += $activity->budgeted_cost;
            $cumulativeEarned += $activity->earned_value;
            $cumulativeActual += $activity->actual_cost;

            $sCurve[] = [
                'date' => $activity->planned_finish,
                'cumulative_planned' => $cumulativePlanned,
                'cumulative_earned' => $cumulativeEarned,
                'cumulative_actual' => $cumulativeActual,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $sCurve
        ]);
    }
}
