<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectProgressSnapshot;
use Carbon\Carbon;

class EVMCalculationService
{
    /**
     * Calculate all EVM metrics for a project at a specific date
     */
    public function calculateEVMMetrics(Project $project, Carbon $snapshotDate, array $inputData = []): array
    {
        $bac = $project->total_budget;
        
        // Get or calculate basic values
        $actualProgress = $inputData['overall_progress_percent'] ?? $project->overall_progress;
        $plannedProgress = $this->calculatePlannedProgress($project, $snapshotDate);
        $physicalProgress = $inputData['physical_progress_percent'] ?? $actualProgress;
        $actualCost = $inputData['actual_cost_ac'] ?? $this->calculateActualCost($project, $snapshotDate);
        
        // Calculate PV, EV, AC
        $pv = ($plannedProgress / 100) * $bac; // Planned Value
        $ev = ($actualProgress / 100) * $bac; // Earned Value
        $ac = $actualCost; // Actual Cost
        
        // Calculate Variances
        $sv = $ev - $pv; // Schedule Variance
        $cv = $ev - $ac; // Cost Variance
        
        // Calculate Performance Indexes
        $spi = $pv > 0 ? $ev / $pv : 1.0; // Schedule Performance Index
        $cpi = $ac > 0 ? $ev / $ac : 1.0; // Cost Performance Index
        
        // Calculate Estimates
        $eac = $cpi > 0 ? $bac / $cpi : $bac; // Estimate at Completion
        $etc = $eac - $ac; // Estimate to Complete
        $vac = $bac - $eac; // Variance at Completion
        
        // Calculate TCPI (To Complete Performance Index)
        $tcpi = ($bac - $ev) > 0 && ($bac - $ac) > 0 
            ? ($bac - $ev) / ($bac - $ac) 
            : 1.0;
        
        // Calculate forecasted completion date
        $forecastedDate = $this->calculateForecastedCompletionDate($project, $spi);
        
        return [
            'overall_progress_percent' => round($actualProgress, 2),
            'planned_progress_percent' => round($plannedProgress, 2),
            'physical_progress_percent' => round($physicalProgress, 2),
            'planned_value_pv' => round($pv, 2),
            'earned_value_ev' => round($ev, 2),
            'actual_cost_ac' => round($ac, 2),
            'budget_at_completion_bac' => round($bac, 2),
            'estimate_at_completion_eac' => round($eac, 2),
            'estimate_to_complete_etc' => round($etc, 2),
            'variance_at_completion_vac' => round($vac, 2),
            'schedule_variance_sv' => round($sv, 2),
            'cost_variance_cv' => round($cv, 2),
            'schedule_performance_index_spi' => round($spi, 3),
            'cost_performance_index_cpi' => round($cpi, 3),
            'to_complete_performance_index_tcpi' => round($tcpi, 3),
            'planned_completion_date' => $project->planned_end_date,
            'forecasted_completion_date' => $forecastedDate,
        ];
    }

    /**
     * Calculate planned progress based on project schedule
     */
    public function calculatePlannedProgress(Project $project, Carbon $asOfDate): float
    {
        $startDate = Carbon::parse($project->start_date);
        $endDate = Carbon::parse($project->planned_end_date);
        
        // If before start, 0%
        if ($asOfDate->lt($startDate)) {
            return 0;
        }
        
        // If after end, 100%
        if ($asOfDate->gte($endDate)) {
            return 100;
        }
        
        // Calculate based on time elapsed
        $totalDays = $startDate->diffInDays($endDate);
        $elapsedDays = $startDate->diffInDays($asOfDate);
        
        return $totalDays > 0 ? ($elapsedDays / $totalDays) * 100 : 0;
    }

    /**
     * Calculate actual cost from timesheets and other sources
     */
    public function calculateActualCost(Project $project, Carbon $asOfDate): float
    {
        return $project->timesheets()
            ->where('work_date', '<=', $asOfDate)
            ->where('status', 'approved')
            ->sum('cost');
    }

    /**
     * Calculate forecasted completion date based on SPI
     */
    public function calculateForecastedCompletionDate(Project $project, float $spi): Carbon
    {
        $startDate = Carbon::parse($project->start_date);
        $plannedEndDate = Carbon::parse($project->planned_end_date);
        
        if ($spi <= 0) {
            return $plannedEndDate->copy()->addMonths(6); // Default to 6 months delay
        }
        
        $totalPlannedDays = $startDate->diffInDays($plannedEndDate);
        $forecastedDays = $totalPlannedDays / $spi;
        
        return $startDate->copy()->addDays($forecastedDays);
    }

    /**
     * Get trend data for charts (last N snapshots)
     */
    public function getTrendData(Project $project, int $months = 6): array
    {
        $snapshots = $project->progressSnapshots()
            ->where('snapshot_date', '>=', now()->subMonths($months))
            ->orderBy('snapshot_date')
            ->get();
        
        return [
            'dates' => $snapshots->pluck('snapshot_date')->map(fn($d) => $d->format('Y-m-d'))->toArray(),
            'pv' => $snapshots->pluck('planned_value_pv')->toArray(),
            'ev' => $snapshots->pluck('earned_value_ev')->toArray(),
            'ac' => $snapshots->pluck('actual_cost_ac')->toArray(),
            'spi' => $snapshots->pluck('schedule_performance_index_spi')->toArray(),
            'cpi' => $snapshots->pluck('cost_performance_index_cpi')->toArray(),
            'sv' => $snapshots->pluck('schedule_variance_sv')->toArray(),
            'cv' => $snapshots->pluck('cost_variance_cv')->toArray(),
        ];
    }

    /**
     * Calculate project health status
     */
    public function getProjectHealthStatus(ProjectProgressSnapshot $snapshot): array
    {
        $spi = $snapshot->schedule_performance_index_spi;
        $cpi = $snapshot->cost_performance_index_cpi;
        
        return [
            'overall' => $this->getHealthColor($spi, $cpi),
            'schedule' => $this->getPerformanceColor($spi),
            'cost' => $this->getPerformanceColor($cpi),
            'spi_status' => $this->getStatusText($spi),
            'cpi_status' => $this->getStatusText($cpi),
        ];
    }

    private function getHealthColor(float $spi, float $cpi): string
    {
        if ($spi >= 0.95 && $cpi >= 0.95) {
            return 'green';
        } elseif ($spi >= 0.85 && $cpi >= 0.85) {
            return 'yellow';
        }
        return 'red';
    }

    private function getPerformanceColor(float $index): string
    {
        if ($index >= 0.95) return 'green';
        if ($index >= 0.85) return 'yellow';
        return 'red';
    }

    private function getStatusText(float $index): string
    {
        if ($index >= 0.95) return 'Good';
        if ($index >= 0.85) return 'Warning';
        return 'Critical';
    }

    /**
     * Generate forecasting scenarios
     */
    public function generateScenarios(Project $project, ProjectProgressSnapshot $currentSnapshot): array
    {
        $currentSpi = $currentSnapshot->schedule_performance_index_spi;
        
        return [
            'optimistic' => [
                'spi' => $currentSpi * 1.15,
                'completion_date' => $this->calculateForecastedCompletionDate($project, $currentSpi * 1.15),
                'description' => 'Performance improves by 15%',
            ],
            'most_likely' => [
                'spi' => $currentSpi,
                'completion_date' => $currentSnapshot->forecasted_completion_date,
                'description' => 'Current performance continues',
            ],
            'pessimistic' => [
                'spi' => $currentSpi * 0.85,
                'completion_date' => $this->calculateForecastedCompletionDate($project, $currentSpi * 0.85),
                'description' => 'Performance degrades by 15%',
            ],
        ];
    }
}
