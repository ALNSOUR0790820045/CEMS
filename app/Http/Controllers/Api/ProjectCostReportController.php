<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CostReport;
use App\Models\ProjectBudget;
use App\Models\ActualCost;
use App\Models\CommittedCost;
use App\Models\VarianceAnalysis;
use App\Models\CostForecast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProjectCostReportController extends Controller
{
    /**
     * Display a listing of cost reports.
     */
    public function index(Request $request)
    {
        $query = CostReport::with(['project', 'preparedBy'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('report_type')) {
            $query->where('report_type', $request->report_type);
        }

        $reports = $query->latest('report_date')->paginate($request->per_page ?? 15);

        return response()->json($reports);
    }

    /**
     * Store a newly created cost report.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'report_date' => 'required|date',
            'report_type' => 'required|in:weekly,monthly,quarterly',
            'period_from' => 'required|date',
            'period_to' => 'required|date|after_or_equal:period_from',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $reportNumber = CostReport::generateReportNumber(date('Y', strtotime($request->report_date)));

        $report = CostReport::create(array_merge(
            $validator->validated(),
            [
                'report_number' => $reportNumber,
                'prepared_by_id' => $request->user()->id,
                'company_id' => $request->user()->company_id,
            ]
        ));

        return response()->json($report->load('project'), 201);
    }

    /**
     * Display the specified cost report.
     */
    public function show(string $id)
    {
        $report = CostReport::with(['project', 'preparedBy'])->findOrFail($id);
        return response()->json($report);
    }

    /**
     * Update the specified cost report.
     */
    public function update(Request $request, string $id)
    {
        $report = CostReport::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'report_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $report->update($request->all());

        return response()->json($report->load('project'));
    }

    /**
     * Remove the specified cost report.
     */
    public function destroy(string $id)
    {
        $report = CostReport::findOrFail($id);
        $report->delete();

        return response()->json(['message' => 'Cost report deleted successfully']);
    }

    /**
     * Get cost summary for a project
     */
    public function costSummary($projectId)
    {
        $activeBudget = ProjectBudget::where('project_id', $projectId)
            ->where('status', 'active')
            ->first();

        $committedCosts = CommittedCost::where('project_id', $projectId)
            ->sum('current_amount');

        $actualCosts = ActualCost::where('project_id', $projectId)
            ->sum('amount_local');

        $summary = [
            'original_budget' => $activeBudget->total_budget ?? 0,
            'revised_budget' => $activeBudget->total_budget ?? 0,
            'committed_costs' => $committedCosts,
            'actual_costs' => $actualCosts,
            'remaining_budget' => ($activeBudget->total_budget ?? 0) - $actualCosts,
            'forecast_at_completion' => $actualCosts + $committedCosts,
            'variance_to_budget' => ($activeBudget->total_budget ?? 0) - ($actualCosts + $committedCosts),
        ];

        return response()->json($summary);
    }

    /**
     * Get budget vs actual report
     */
    public function budgetVsActual($projectId)
    {
        $budget = ProjectBudget::where('project_id', $projectId)
            ->where('status', 'active')
            ->with(['items.costCode'])
            ->first();

        if (!$budget) {
            return response()->json(['error' => 'No active budget found'], 404);
        }

        $data = $budget->items->map(function ($item) use ($projectId) {
            $actualAmount = ActualCost::where('project_id', $projectId)
                ->where('cost_code_id', $item->cost_code_id)
                ->sum('amount_local');

            return [
                'cost_code' => $item->costCode->code,
                'cost_code_name' => $item->costCode->name,
                'budgeted_amount' => $item->budgeted_amount,
                'actual_amount' => $actualAmount,
                'variance' => $item->budgeted_amount - $actualAmount,
                'variance_percentage' => $item->budgeted_amount > 0 
                    ? (($item->budgeted_amount - $actualAmount) / $item->budgeted_amount) * 100 
                    : 0,
            ];
        });

        return response()->json($data);
    }

    /**
     * Get cost breakdown by cost code
     */
    public function costBreakdown($projectId)
    {
        $breakdown = ActualCost::where('project_id', $projectId)
            ->with('costCode')
            ->select('cost_code_id', DB::raw('SUM(amount_local) as total'))
            ->groupBy('cost_code_id')
            ->get()
            ->map(function ($item) {
                return [
                    'cost_code' => $item->costCode->code,
                    'cost_code_name' => $item->costCode->name,
                    'total_amount' => $item->total,
                ];
            });

        return response()->json($breakdown);
    }

    /**
     * Get commitment status report
     */
    public function commitmentStatus($projectId)
    {
        $commitments = CommittedCost::where('project_id', $projectId)
            ->with(['costCode', 'vendor'])
            ->get()
            ->map(function ($item) {
                return [
                    'commitment_number' => $item->commitment_number,
                    'vendor' => $item->vendor->name ?? 'N/A',
                    'cost_code' => $item->costCode->code,
                    'original_amount' => $item->original_amount,
                    'current_amount' => $item->current_amount,
                    'invoiced_amount' => $item->invoiced_amount,
                    'remaining_amount' => $item->remaining_amount,
                    'status' => $item->status,
                ];
            });

        return response()->json($commitments);
    }

    /**
     * Get cost trend analysis
     */
    public function costTrend(Request $request, $projectId)
    {
        $months = $request->get('months', 6);

        $trend = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;

            $startDate = sprintf('%04d-%02d-01', $year, $month);
            $endDate = date('Y-m-t', strtotime($startDate));

            $monthlyActual = ActualCost::where('project_id', $projectId)
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount_local');

            $trend[] = [
                'period' => $date->format('Y-m'),
                'actual_costs' => $monthlyActual,
            ];
        }

        return response()->json($trend);
    }

    /**
     * Get EVM (Earned Value Management) analysis
     */
    public function evmAnalysis(Request $request, $projectId)
    {
        $budget = ProjectBudget::where('project_id', $projectId)
            ->where('status', 'active')
            ->first();

        if (!$budget) {
            return response()->json(['error' => 'No active budget found'], 404);
        }

        $actualCosts = ActualCost::where('project_id', $projectId)->sum('amount_local');
        
        // Get project progress percentage
        $percentComplete = $request->get('percentage_complete', 50); // Would come from project progress tracking
        
        // Calculate Earned Value (EV) based on work completed
        $earnedValue = ($budget->total_budget * $percentComplete) / 100;
        
        // For accurate SPI, we need actual Planned Value (PV) for the current period
        // This is a simplified calculation assuming linear progress
        // In a real implementation, PV should come from the project schedule
        $plannedPercentage = $request->get('planned_percentage', $percentComplete);
        $plannedValue = ($budget->total_budget * $plannedPercentage) / 100;
        
        $cpi = $actualCosts > 0 ? $earnedValue / $actualCosts : 0;
        $spi = $plannedValue > 0 ? $earnedValue / $plannedValue : 0;
        $eac = $cpi > 0 ? $budget->total_budget / $cpi : 0;
        $vac = $budget->total_budget - $eac;
        $tcpi = ($budget->total_budget - $earnedValue) > 0 && ($budget->total_budget - $actualCosts) > 0
            ? ($budget->total_budget - $earnedValue) / ($budget->total_budget - $actualCosts)
            : 0;

        return response()->json([
            'bac' => $budget->total_budget,
            'planned_value' => $plannedValue,
            'earned_value' => $earnedValue,
            'actual_cost' => $actualCosts,
            'cost_variance' => $earnedValue - $actualCosts,
            'schedule_variance' => $earnedValue - $plannedValue,
            'cpi' => round($cpi, 4),
            'spi' => round($spi, 4),
            'eac' => round($eac, 2),
            'vac' => round($vac, 2),
            'tcpi' => round($tcpi, 4),
        ]);
    }

    /**
     * Get variance report
     */
    public function varianceReport($projectId)
    {
        $variances = VarianceAnalysis::where('project_id', $projectId)
            ->with(['costCode', 'responsiblePerson'])
            ->orderBy('variance_percentage', 'desc')
            ->get();

        return response()->json($variances);
    }

    /**
     * Get forecast report
     */
    public function forecastReport($projectId)
    {
        $forecasts = CostForecast::where('project_id', $projectId)
            ->with(['costCode', 'preparedBy'])
            ->latest('forecast_date')
            ->get();

        return response()->json($forecasts);
    }

    /**
     * Get cost to complete report
     */
    public function costToComplete($projectId)
    {
        $budget = ProjectBudget::where('project_id', $projectId)
            ->where('status', 'active')
            ->with(['items.costCode'])
            ->first();

        if (!$budget) {
            return response()->json(['error' => 'No active budget found'], 404);
        }

        $data = $budget->items->map(function ($item) use ($projectId) {
            $actualAmount = ActualCost::where('project_id', $projectId)
                ->where('cost_code_id', $item->cost_code_id)
                ->sum('amount_local');

            $committedAmount = CommittedCost::where('project_id', $projectId)
                ->where('cost_code_id', $item->cost_code_id)
                ->sum('remaining_amount');

            $costToComplete = max(0, $item->budgeted_amount - $actualAmount - $committedAmount);

            return [
                'cost_code' => $item->costCode->code,
                'cost_code_name' => $item->costCode->name,
                'budgeted_amount' => $item->budgeted_amount,
                'actual_amount' => $actualAmount,
                'committed_amount' => $committedAmount,
                'cost_to_complete' => $costToComplete,
                'estimate_at_completion' => $actualAmount + $committedAmount + $costToComplete,
            ];
        });

        return response()->json($data);
    }

    /**
     * Generate monthly cost report
     */
    public function generateMonthlyReport(Request $request, $projectId)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $month = $request->month;
        $year = $request->year;

        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));

        DB::beginTransaction();
        try {
            $budget = ProjectBudget::where('project_id', $projectId)
                ->where('status', 'active')
                ->first();

            $committedCosts = CommittedCost::where('project_id', $projectId)
                ->sum('current_amount');

            $actualCosts = ActualCost::where('project_id', $projectId)
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount_local');

            $totalActualCosts = ActualCost::where('project_id', $projectId)
                ->sum('amount_local');

            $percentComplete = $budget && $budget->total_budget > 0 
                ? ($totalActualCosts / $budget->total_budget) * 100 
                : 0;

            $earnedValue = $budget ? ($budget->total_budget * $percentComplete) / 100 : 0;
            $cpi = $totalActualCosts > 0 ? $earnedValue / $totalActualCosts : 0;
            $eac = $cpi > 0 && $budget ? $budget->total_budget / $cpi : 0;

            $reportNumber = CostReport::generateReportNumber($year);

            $report = CostReport::create([
                'report_number' => $reportNumber,
                'project_id' => $projectId,
                'report_date' => now(),
                'report_type' => 'monthly',
                'period_from' => $startDate,
                'period_to' => $endDate,
                'original_budget' => $budget->total_budget ?? 0,
                'revised_budget' => $budget->total_budget ?? 0,
                'committed_costs' => $committedCosts,
                'actual_costs' => $actualCosts,
                'forecast_at_completion' => $eac,
                'variance_to_budget' => ($budget->total_budget ?? 0) - $eac,
                'percentage_complete' => $percentComplete,
                'cost_performance_index' => $cpi,
                'schedule_performance_index' => 0, // Would need schedule data
                'earned_value' => $earnedValue,
                'prepared_by_id' => $request->user()->id,
                'company_id' => $request->user()->company_id,
            ]);

            DB::commit();

            return response()->json($report->load('project'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to generate report: ' . $e->getMessage()], 500);
        }
    }
}
