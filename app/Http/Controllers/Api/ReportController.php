<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\CostAllocation;
use App\Models\CostCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Cost Analysis Report
     */
    public function costAnalysis(Request $request)
    {
        $query = CostAllocation::with(['costCenter', 'glAccount', 'currency'])
            ->select('cost_center_id', 'gl_account_id', 'currency_id')
            ->selectRaw('SUM(amount) as total_amount')
            ->selectRaw('COUNT(*) as transaction_count')
            ->groupBy('cost_center_id', 'gl_account_id', 'currency_id');

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('cost_center_id')) {
            $query->where('cost_center_id', $request->cost_center_id);
        }

        if ($request->has('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        $data = $query->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Budget Variance Report
     */
    public function budgetVariance(Request $request)
    {
        $query = Budget::with(['budgetItems.glAccount', 'costCenter', 'project']);

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('fiscal_year')) {
            $query->where('fiscal_year', $request->fiscal_year);
        }

        if ($request->has('budget_id')) {
            $query->where('id', $request->budget_id);
        }

        $budgets = $query->get();

        $report = $budgets->map(function ($budget) {
            $totalBudgeted = $budget->budgetItems->sum('budgeted_amount');
            $totalActual = $budget->budgetItems->sum('actual_amount');
            $totalVariance = $totalActual - $totalBudgeted;
            $variancePercentage = $totalBudgeted > 0 ? ($totalVariance / $totalBudgeted) * 100 : 0;

            return [
                'budget_id' => $budget->id,
                'budget_name' => $budget->budget_name,
                'fiscal_year' => $budget->fiscal_year,
                'budget_type' => $budget->budget_type,
                'status' => $budget->status,
                'cost_center' => $budget->costCenter ? $budget->costCenter->name : null,
                'project' => $budget->project ? $budget->project->name : null,
                'total_budgeted' => $totalBudgeted,
                'total_actual' => $totalActual,
                'total_variance' => $totalVariance,
                'variance_percentage' => round($variancePercentage, 2),
                'items' => $budget->budgetItems->map(function ($item) {
                    return [
                        'gl_account' => $item->glAccount->name,
                        'month' => $item->month,
                        'budgeted_amount' => $item->budgeted_amount,
                        'actual_amount' => $item->actual_amount,
                        'variance' => $item->variance,
                        'variance_percentage' => $item->budgeted_amount > 0 
                            ? round(($item->variance / $item->budgeted_amount) * 100, 2) 
                            : 0,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    /**
     * Cost Center Report
     */
    public function costCenterReport(Request $request)
    {
        $query = CostCenter::with(['costAllocations' => function ($q) use ($request) {
            if ($request->has('date_from')) {
                $q->where('transaction_date', '>=', $request->date_from);
            }
            if ($request->has('date_to')) {
                $q->where('transaction_date', '<=', $request->date_to);
            }
        }]);

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $costCenters = $query->get();

        $report = $costCenters->map(function ($costCenter) {
            $totalCost = $costCenter->costAllocations->sum('amount');
            $transactionCount = $costCenter->costAllocations->count();

            return [
                'cost_center_id' => $costCenter->id,
                'code' => $costCenter->code,
                'name' => $costCenter->name,
                'type' => $costCenter->type,
                'is_active' => $costCenter->is_active,
                'total_cost' => $totalCost,
                'transaction_count' => $transactionCount,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }
}
