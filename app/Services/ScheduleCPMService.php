<?php

namespace App\Services;

use App\Models\ProjectSchedule;
use App\Models\ScheduleActivity;
use Carbon\Carbon;

class ScheduleCPMService
{
    public function calculate(ProjectSchedule $schedule)
    {
        $activities = $schedule->activities()->with(['predecessors', 'successors'])->get();
        
        if ($activities->isEmpty()) {
            return;
        }

        // Reset CPM fields
        foreach ($activities as $activity) {
            $activity->early_start = null;
            $activity->early_finish = null;
            $activity->late_start = null;
            $activity->late_finish = null;
            $activity->total_float = 0;
            $activity->free_float = 0;
            $activity->is_critical = false;
        }

        // Forward Pass
        $this->forwardPass($activities, $schedule);

        // Backward Pass
        $this->backwardPass($activities, $schedule);

        // Calculate Float and Critical Path
        $this->calculateFloat($activities);

        // Save all activities
        foreach ($activities as $activity) {
            $activity->save();
        }

        return $activities;
    }

    protected function forwardPass($activities, $schedule)
    {
        // Start with activities that have no predecessors
        $processed = [];
        $queue = $activities->filter(function ($activity) {
            return $activity->predecessors->isEmpty();
        });

        foreach ($queue as $activity) {
            if ($activity->planned_start) {
                $activity->early_start = $activity->planned_start;
            } else {
                $activity->early_start = $schedule->start_date;
            }
            
            $activity->early_finish = $this->calculateFinishDate(
                $activity->early_start,
                $activity->planned_duration
            );
            
            $processed[] = $activity->id;
        }

        // Process remaining activities
        $maxIterations = $activities->count() * 2;
        $iteration = 0;

        while (count($processed) < $activities->count() && $iteration < $maxIterations) {
            $iteration++;
            
            foreach ($activities as $activity) {
                if (in_array($activity->id, $processed)) {
                    continue;
                }

                // Check if all predecessors are processed
                $allPredecessorsProcessed = true;
                $maxEarlyFinish = null;

                foreach ($activity->predecessors as $dependency) {
                    $predecessor = $dependency->predecessor;
                    
                    if (!in_array($predecessor->id, $processed)) {
                        $allPredecessorsProcessed = false;
                        break;
                    }

                    $predFinish = $this->calculateDependencyDate(
                        $predecessor,
                        $dependency
                    );

                    if ($maxEarlyFinish === null || $predFinish > $maxEarlyFinish) {
                        $maxEarlyFinish = $predFinish;
                    }
                }

                if ($allPredecessorsProcessed) {
                    $activity->early_start = $maxEarlyFinish ?? $schedule->start_date;
                    $activity->early_finish = $this->calculateFinishDate(
                        $activity->early_start,
                        $activity->planned_duration
                    );
                    $processed[] = $activity->id;
                }
            }
        }
    }

    protected function backwardPass($activities, $schedule)
    {
        // Start with activities that have no successors
        $processed = [];
        $queue = $activities->filter(function ($activity) {
            return $activity->successors->isEmpty();
        });

        foreach ($queue as $activity) {
            if ($activity->planned_finish) {
                $activity->late_finish = $activity->planned_finish;
            } else {
                $activity->late_finish = $activity->early_finish ?? $schedule->end_date;
            }
            
            $activity->late_start = $this->calculateStartDate(
                $activity->late_finish,
                $activity->planned_duration
            );
            
            $processed[] = $activity->id;
        }

        // Process remaining activities
        $maxIterations = $activities->count() * 2;
        $iteration = 0;

        while (count($processed) < $activities->count() && $iteration < $maxIterations) {
            $iteration++;
            
            foreach ($activities->reverse() as $activity) {
                if (in_array($activity->id, $processed)) {
                    continue;
                }

                // Check if all successors are processed
                $allSuccessorsProcessed = true;
                $minLateStart = null;

                foreach ($activity->successors as $dependency) {
                    $successor = $dependency->successor;
                    
                    if (!in_array($successor->id, $processed)) {
                        $allSuccessorsProcessed = false;
                        break;
                    }

                    $succStart = $this->calculateBackwardDependencyDate(
                        $successor,
                        $dependency
                    );

                    if ($minLateStart === null || $succStart < $minLateStart) {
                        $minLateStart = $succStart;
                    }
                }

                if ($allSuccessorsProcessed) {
                    $activity->late_finish = $minLateStart ?? $activity->early_finish;
                    $activity->late_start = $this->calculateStartDate(
                        $activity->late_finish,
                        $activity->planned_duration
                    );
                    $processed[] = $activity->id;
                }
            }
        }
    }

    protected function calculateFloat($activities)
    {
        foreach ($activities as $activity) {
            if ($activity->early_start && $activity->late_start) {
                $activity->total_float = Carbon::parse($activity->late_start)
                    ->diffInDays(Carbon::parse($activity->early_start));
            }

            // Calculate free float
            $minSuccessorES = null;
            foreach ($activity->successors as $dependency) {
                $successor = $dependency->successor;
                if ($successor->early_start) {
                    if ($minSuccessorES === null || $successor->early_start < $minSuccessorES) {
                        $minSuccessorES = $successor->early_start;
                    }
                }
            }

            if ($minSuccessorES && $activity->early_finish) {
                $activity->free_float = max(0, Carbon::parse($minSuccessorES)
                    ->diffInDays(Carbon::parse($activity->early_finish)) - 1);
            }

            // Mark critical activities
            $activity->is_critical = ($activity->total_float == 0);
        }
    }

    protected function calculateDependencyDate($predecessor, $dependency)
    {
        $lagDays = $dependency->lag_days ?? 0;
        
        switch ($dependency->dependency_type) {
            case 'FS': // Finish-to-Start
                return Carbon::parse($predecessor->early_finish)->addDays($lagDays);
                
            case 'SS': // Start-to-Start
                return Carbon::parse($predecessor->early_start)->addDays($lagDays);
                
            case 'FF': // Finish-to-Finish
                return Carbon::parse($predecessor->early_finish)->addDays($lagDays);
                
            case 'SF': // Start-to-Finish
                return Carbon::parse($predecessor->early_start)->addDays($lagDays);
                
            default:
                return Carbon::parse($predecessor->early_finish)->addDays($lagDays);
        }
    }

    protected function calculateBackwardDependencyDate($successor, $dependency)
    {
        $lagDays = $dependency->lag_days ?? 0;
        
        switch ($dependency->dependency_type) {
            case 'FS': // Finish-to-Start
                return Carbon::parse($successor->late_start)->subDays($lagDays);
                
            case 'SS': // Start-to-Start
                return Carbon::parse($successor->late_start)->subDays($lagDays);
                
            case 'FF': // Finish-to-Finish
                return Carbon::parse($successor->late_finish)->subDays($lagDays);
                
            case 'SF': // Start-to-Finish
                return Carbon::parse($successor->late_finish)->subDays($lagDays);
                
            default:
                return Carbon::parse($successor->late_start)->subDays($lagDays);
        }
    }

    protected function calculateFinishDate($startDate, $duration)
    {
        if (!$startDate || $duration <= 0) {
            return $startDate;
        }
        
        return Carbon::parse($startDate)->addDays($duration - 1);
    }

    protected function calculateStartDate($finishDate, $duration)
    {
        if (!$finishDate || $duration <= 0) {
            return $finishDate;
        }
        
        return Carbon::parse($finishDate)->subDays($duration - 1);
    }

    public function getCriticalPath(ProjectSchedule $schedule)
    {
        return $schedule->activities()
            ->where('is_critical', true)
            ->orderBy('early_start')
            ->get();
    }
}
