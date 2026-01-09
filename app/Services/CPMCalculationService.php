<?php

namespace App\Services;

use App\Models\Tender;
use App\Models\TenderActivity;
use App\Models\TenderActivityDependency;
use Illuminate\Support\Facades\DB;

class CPMCalculationService
{
    /**
     * Calculate CPM for a tender
     * 
     * @param int $tenderId
     * @return array
     */
    public function calculateCPM(int $tenderId): array
    {
        $activities = TenderActivity::where('tender_id', $tenderId)
            ->with(['predecessors.predecessor', 'successors.successor'])
            ->get();

        if ($activities->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No activities found for this tender'
            ];
        }

        // Initialize all activities
        foreach ($activities as $activity) {
            $activity->early_start = 0;
            $activity->early_finish = 0;
            $activity->late_start = 0;
            $activity->late_finish = 0;
            $activity->total_float = 0;
            $activity->free_float = 0;
            $activity->is_critical = false;
        }

        // Forward Pass - Calculate Early Start and Early Finish
        $this->forwardPass($activities);

        // Backward Pass - Calculate Late Start and Late Finish
        $this->backwardPass($activities);

        // Calculate Float and identify Critical Path
        $this->calculateFloat($activities);

        // Save all activities
        foreach ($activities as $activity) {
            $activity->save();
        }

        $criticalPath = $activities->where('is_critical', true)->pluck('activity_code')->toArray();

        return [
            'success' => true,
            'message' => 'CPM calculation completed successfully',
            'critical_path' => $criticalPath,
            'project_duration' => $activities->max('early_finish'),
        ];
    }

    /**
     * Forward Pass - Calculate Early Start and Early Finish
     * 
     * @param \Illuminate\Support\Collection $activities
     * @return void
     */
    protected function forwardPass($activities): void
    {
        $activitiesById = $activities->keyBy('id');
        $processed = [];
        $maxIterations = $activities->count() * 2; // Prevent infinite loops
        $iteration = 0;

        while (count($processed) < $activities->count() && $iteration < $maxIterations) {
            $iteration++;
            $progressMade = false;

            foreach ($activities as $activity) {
                if (in_array($activity->id, $processed)) {
                    continue;
                }

                // Check if all predecessors are processed
                $allPredecessorsProcessed = true;
                $maxPredecessorFinish = 0;

                foreach ($activity->predecessors as $dependency) {
                    $predecessor = $dependency->predecessor;

                    if (!in_array($predecessor->id, $processed)) {
                        $allPredecessorsProcessed = false;
                        break;
                    }

                    // Calculate based on dependency type
                    $finishValue = $this->calculateDependencyValue(
                        $predecessor,
                        $dependency->type,
                        $dependency->lag_days,
                        'forward'
                    );

                    $maxPredecessorFinish = max($maxPredecessorFinish, $finishValue);
                }

                if ($allPredecessorsProcessed) {
                    $activity->early_start = $maxPredecessorFinish;
                    $activity->early_finish = $activity->early_start + $activity->duration_days;
                    $processed[] = $activity->id;
                    $progressMade = true;
                }
            }

            if (!$progressMade) {
                break; // Prevent infinite loop if there are circular dependencies
            }
        }
    }

    /**
     * Backward Pass - Calculate Late Start and Late Finish
     * 
     * @param \Illuminate\Support\Collection $activities
     * @return void
     */
    protected function backwardPass($activities): void
    {
        $activitiesById = $activities->keyBy('id');
        $processed = [];
        $maxIterations = $activities->count() * 2;
        $iteration = 0;

        // Find project end time
        $projectEndTime = $activities->max('early_finish');

        // Initialize activities without successors
        foreach ($activities as $activity) {
            if ($activity->successors->isEmpty()) {
                $activity->late_finish = $projectEndTime;
                $activity->late_start = $activity->late_finish - $activity->duration_days;
                $processed[] = $activity->id;
            }
        }

        while (count($processed) < $activities->count() && $iteration < $maxIterations) {
            $iteration++;
            $progressMade = false;

            foreach ($activities as $activity) {
                if (in_array($activity->id, $processed)) {
                    continue;
                }

                // Check if all successors are processed
                $allSuccessorsProcessed = true;
                $minSuccessorStart = PHP_INT_MAX;

                foreach ($activity->successors as $dependency) {
                    $successor = $dependency->successor;

                    if (!in_array($successor->id, $processed)) {
                        $allSuccessorsProcessed = false;
                        break;
                    }

                    // Calculate based on dependency type
                    $startValue = $this->calculateDependencyValue(
                        $successor,
                        $dependency->type,
                        $dependency->lag_days,
                        'backward'
                    );

                    $minSuccessorStart = min($minSuccessorStart, $startValue);
                }

                if ($allSuccessorsProcessed) {
                    $activity->late_finish = $minSuccessorStart;
                    $activity->late_start = $activity->late_finish - $activity->duration_days;
                    $processed[] = $activity->id;
                    $progressMade = true;
                }
            }

            if (!$progressMade) {
                break;
            }
        }
    }

    /**
     * Calculate Float and identify Critical Path
     * 
     * @param \Illuminate\Support\Collection $activities
     * @return void
     */
    protected function calculateFloat($activities): void
    {
        foreach ($activities as $activity) {
            // Total Float = Late Start - Early Start (or Late Finish - Early Finish)
            $activity->total_float = $activity->late_start - $activity->early_start;

            // Calculate Free Float
            $minSuccessorES = PHP_INT_MAX;
            foreach ($activity->successors as $dependency) {
                $successor = $dependency->successor;
                $minSuccessorES = min($minSuccessorES, $successor->early_start);
            }

            if ($minSuccessorES != PHP_INT_MAX) {
                $activity->free_float = $minSuccessorES - $activity->early_finish;
            } else {
                $activity->free_float = $activity->total_float;
            }

            // Critical activities have zero or near-zero float
            $activity->is_critical = $activity->total_float == 0;
        }
    }

    /**
     * Calculate dependency value based on type
     * 
     * @param TenderActivity $activity
     * @param string $dependencyType
     * @param int $lagDays
     * @param string $direction (forward or backward)
     * @return int
     */
    protected function calculateDependencyValue(
        TenderActivity $activity,
        string $dependencyType,
        int $lagDays,
        string $direction
    ): int {
        if ($direction === 'forward') {
            return match ($dependencyType) {
                'FS' => $activity->early_finish + $lagDays,  // Finish-to-Start
                'SS' => $activity->early_start + $lagDays,   // Start-to-Start
                'FF' => $activity->early_finish + $lagDays,  // Finish-to-Finish (needs adjustment)
                'SF' => $activity->early_start + $lagDays,   // Start-to-Finish
                default => $activity->early_finish + $lagDays,
            };
        } else {
            return match ($dependencyType) {
                'FS' => $activity->late_start - $lagDays,    // Finish-to-Start
                'SS' => $activity->late_start - $lagDays,    // Start-to-Start
                'FF' => $activity->late_finish - $lagDays,   // Finish-to-Finish
                'SF' => $activity->late_finish - $lagDays,   // Start-to-Finish
                default => $activity->late_start - $lagDays,
            };
        }
    }

    /**
     * Get critical path activities
     * 
     * @param int $tenderId
     * @return \Illuminate\Support\Collection
     */
    public function getCriticalPath(int $tenderId)
    {
        return TenderActivity::where('tender_id', $tenderId)
            ->where('is_critical', true)
            ->orderBy('early_start')
            ->get();
    }

    /**
     * Get network diagram data for visualization
     * 
     * @param int $tenderId
     * @return array
     */
    public function getNetworkDiagram(int $tenderId): array
    {
        $activities = TenderActivity::where('tender_id', $tenderId)
            ->with(['predecessors.predecessor', 'successors.successor'])
            ->get();

        $nodes = [];
        $edges = [];

        foreach ($activities as $activity) {
            $nodes[] = [
                'id' => $activity->id,
                'label' => $activity->activity_code,
                'name' => $activity->name,
                'duration' => $activity->duration_days,
                'early_start' => $activity->early_start,
                'early_finish' => $activity->early_finish,
                'late_start' => $activity->late_start,
                'late_finish' => $activity->late_finish,
                'total_float' => $activity->total_float,
                'is_critical' => $activity->is_critical,
            ];

            foreach ($activity->successors as $dependency) {
                $edges[] = [
                    'from' => $activity->id,
                    'to' => $dependency->successor_id,
                    'type' => $dependency->type,
                    'lag' => $dependency->lag_days,
                ];
            }
        }

        return [
            'nodes' => $nodes,
            'edges' => $edges,
        ];
    }
}
