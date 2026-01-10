<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectReportController extends Controller
{
    /**
     * Get Project Profitability Report
     */
    public function profitability(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'project_id' => 'nullable|exists:projects,id',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth());
        $projectId = $request->input('project_id');

        $query = Project::with(['transactions.account'])
            ->where('is_billable', true);

        if ($projectId) {
            $query->where('id', $projectId);
        }

        $projects = $query->get()->map(function ($project) use ($dateFrom, $dateTo) {
            $revenue = Transaction::where('project_id', $project->id)
                ->whereHas('account', fn ($q) => $q->where('type', 'revenue'))
                ->whereHas('journalEntry', fn ($q) => $q->where('status', 'posted')
                    ->whereBetween('entry_date', [$dateFrom, $dateTo]))
                ->selectRaw('SUM(credit) - SUM(debit) as total')
                ->value('total') ?? 0;

            $costs = Transaction::where('project_id', $project->id)
                ->whereHas('account', fn ($q) => $q->where('type', 'expense'))
                ->whereHas('journalEntry', fn ($q) => $q->where('status', 'posted')
                    ->whereBetween('entry_date', [$dateFrom, $dateTo]))
                ->selectRaw('SUM(debit) - SUM(credit) as total')
                ->value('total') ?? 0;

            $profit = $revenue - $costs;
            $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

            return [
                'project_code' => $project->code,
                'project_name' => $project->name,
                'revenue' => number_format($revenue, 2),
                'costs' => number_format($costs, 2),
                'profit' => number_format($profit, 2),
                'margin' => number_format($margin, 2).'%',
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'projects' => $projects,
            ],
        ]);
    }

    /**
     * Get Project Cost Analysis Report
     */
    public function costAnalysis(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth());
        $projectId = $request->input('project_id');

        $query = Project::with('transactions.account');

        if ($projectId) {
            $query->where('id', $projectId);
        }

        $projects = $query->get()->map(function ($project) use ($dateFrom, $dateTo) {
            $costs = Transaction::where('project_id', $project->id)
                ->whereHas('account', fn ($q) => $q->where('type', 'expense'))
                ->whereHas('journalEntry', fn ($q) => $q->where('status', 'posted')
                    ->whereBetween('entry_date', [$dateFrom, $dateTo]))
                ->with('account')
                ->get()
                ->groupBy('account.category')
                ->map(fn ($group) => $group->sum(fn ($t) => $t->debit - $t->credit));

            return [
                'project_code' => $project->code,
                'project_name' => $project->name,
                'cost_breakdown' => $costs,
                'total_cost' => number_format($costs->sum(), 2),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'projects' => $projects,
            ],
        ]);
    }

    /**
     * Get Budget vs Actual Report
     */
    public function budgetVsActual(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth());
        $projectId = $request->input('project_id');

        $query = Project::query();

        if ($projectId) {
            $query->where('id', $projectId);
        }

        $projects = $query->get()->map(function ($project) use ($dateFrom, $dateTo) {
            $actualCost = Transaction::where('project_id', $project->id)
                ->whereHas('account', fn ($q) => $q->where('type', 'expense'))
                ->whereHas('journalEntry', fn ($q) => $q->where('status', 'posted')
                    ->whereBetween('entry_date', [$dateFrom, $dateTo]))
                ->selectRaw('SUM(debit) - SUM(credit) as total')
                ->value('total') ?? 0;

            $variance = $project->budget - $actualCost;
            $variancePercent = $project->budget > 0
                ? ($variance / $project->budget) * 100
                : 0;

            return [
                'project_code' => $project->code,
                'project_name' => $project->name,
                'budget' => number_format($project->budget, 2),
                'actual' => number_format($actualCost, 2),
                'variance' => number_format($variance, 2),
                'variance_percent' => number_format($variancePercent, 2).'%',
                'status' => $variance >= 0 ? 'under_budget' : 'over_budget',
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'projects' => $projects,
            ],
        ]);
    }

    /**
     * Get Project Cash Flow Report
     */
    public function cashFlow(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth());

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'cash_inflows' => 0,
                'cash_outflows' => 0,
                'net_cash_flow' => 0,
            ],
        ]);
    }

    /**
     * Get Cost Performance Index (CPI) Report
     */
    public function costPerformanceIndex(Request $request): JsonResponse
    {
        $request->validate([
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $projectId = $request->input('project_id');

        $query = Project::query();

        if ($projectId) {
            $query->where('id', $projectId);
        }

        $projects = $query->get()->map(function ($project) {
            // Calculate earned value based on IPC cumulative work or use 0 if none
            $progressPercentage = 0;
            
            // Try to calculate from IPCs if available
            if ($project->budget > 0) {
                $latestIpc = $project->ipcs()->where('status', 'approved_for_payment')->latest()->first();
                if ($latestIpc && $latestIpc->current_cumulative > 0) {
                    $progressPercentage = ($latestIpc->current_cumulative / $project->budget) * 100;
                }
            }
            
            $earnedValue = $project->budget * ($progressPercentage / 100);
            // Actual cost would typically come from cost tracking, use earned value as fallback
            $actualCost = $earnedValue ?: 0;

            $cpi = $actualCost > 0 ? $earnedValue / $actualCost : 0;

            return [
                'project_code' => $project->code,
                'project_name' => $project->name,
                'budget' => number_format($project->budget, 2),
                'earned_value' => number_format($earnedValue, 2),
                'actual_cost' => number_format($actualCost, 2),
                'cpi' => number_format($cpi, 2),
                'performance' => $cpi >= 1 ? 'good' : 'poor',
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'projects' => $projects,
            ],
        ]);
    }
}
