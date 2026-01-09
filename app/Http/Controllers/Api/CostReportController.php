<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\CostAllocation;
use App\Models\CostCenter;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CostReportController extends Controller
{
    /**
     * Cost analysis report
     */
    public function costAnalysis(Request $request)
    {
        $companyId = $request->user()->company_id;
        
        $query = CostAllocation::with(['costCenter', 'costCategory', 'project'])
            ->where('company_id', $companyId)
            ->where('status', 'posted');

        if ($request->has('from_date')) {
            $query->where('allocation_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('allocation_date', '<=', $request->to_date);
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $allocations = $query->get();

        $summary = [
            'total_cost' => $allocations->sum('amount'),
            'by_category' => $allocations->groupBy('cost_category_id')->map(function ($items) {
                return [
                    'category' => $items->first()->costCategory->name,
                    'total' => $items->sum('amount'),
                    'count' => $items->count(),
                ];
            }),
            'by_cost_center' => $allocations->groupBy('cost_center_id')->map(function ($items) {
                return [
                    'cost_center' => $items->first()->costCenter->name,
                    'total' => $items->sum('amount'),
                    'count' => $items->count(),
                ];
            }),
        ];

        return response()->json([
            'summary' => $summary,
            'allocations' => $allocations,
        ]);
    }

    /**
     * Budget variance report
     */
    public function budgetVariance(Request $request)
    {
        $companyId = $request->user()->company_id;
        
        $budgetId = $request->input('budget_id');
        
        if (!$budgetId) {
            return response()->json(['error' => 'budget_id is required'], 422);
        }

        $budget = Budget::with(['items.costCategory', 'items.glAccount'])
            ->where('company_id', $companyId)
            ->findOrFail($budgetId);

        $items = $budget->items->map(function ($item) use ($budget) {
            // Calculate actual amount from allocations
            $actualAmount = CostAllocation::where('cost_category_id', $item->cost_category_id)
                ->where('status', 'posted')
                ->when($budget->project_id, function ($q) use ($budget) {
                    return $q->where('project_id', $budget->project_id);
                })
                ->when($budget->cost_center_id, function ($q) use ($budget) {
                    return $q->where('cost_center_id', $budget->cost_center_id);
                })
                ->when($item->month, function ($q) use ($item, $budget) {
                    return $q->whereYear('allocation_date', $budget->fiscal_year)
                        ->whereMonth('allocation_date', $item->month);
                })
                ->sum('amount');

            $item->actual_amount = $actualAmount;
            $item->variance = $item->budgeted_amount - $actualAmount;
            $item->variance_percentage = $item->budgeted_amount > 0 
                ? ($item->variance / $item->budgeted_amount) * 100 
                : 0;
            
            return $item;
        });

        return response()->json([
            'budget' => $budget,
            'items' => $items,
            'summary' => [
                'total_budgeted' => $items->sum('budgeted_amount'),
                'total_actual' => $items->sum('actual_amount'),
                'total_variance' => $items->sum('variance'),
            ],
        ]);
    }

    /**
     * Cost center report
     */
    public function costCenterReport(Request $request)
    {
        $companyId = $request->user()->company_id;
        
        $costCenterId = $request->input('cost_center_id');
        
        if (!$costCenterId) {
            return response()->json(['error' => 'cost_center_id is required'], 422);
        }

        $costCenter = CostCenter::with(['children'])
            ->where('company_id', $companyId)
            ->findOrFail($costCenterId);

        $query = CostAllocation::with(['costCategory', 'project'])
            ->where('cost_center_id', $costCenterId)
            ->where('status', 'posted');

        if ($request->has('from_date')) {
            $query->where('allocation_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('allocation_date', '<=', $request->to_date);
        }

        $allocations = $query->get();

        return response()->json([
            'cost_center' => $costCenter,
            'total_cost' => $allocations->sum('amount'),
            'by_category' => $allocations->groupBy('cost_category_id')->map(function ($items) {
                return [
                    'category' => $items->first()->costCategory->name,
                    'total' => $items->sum('amount'),
                    'count' => $items->count(),
                ];
            }),
            'allocations' => $allocations,
        ]);
    }

    /**
     * Project cost summary report
     */
    public function projectCostSummary(Request $request)
    {
        $companyId = $request->user()->company_id;
        
        $projectId = $request->input('project_id');
        
        if (!$projectId) {
            return response()->json(['error' => 'project_id is required'], 422);
        }

        $project = Project::where('company_id', $companyId)->findOrFail($projectId);

        $query = CostAllocation::with(['costCenter', 'costCategory'])
            ->where('project_id', $projectId)
            ->where('status', 'posted');

        if ($request->has('from_date')) {
            $query->where('allocation_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('allocation_date', '<=', $request->to_date);
        }

        $allocations = $query->get();

        // Get budget for this project
        $budget = Budget::where('project_id', $projectId)
            ->where('status', 'approved')
            ->first();

        $totalBudget = $budget ? $budget->total_amount : 0;
        $totalCost = $allocations->sum('amount');

        return response()->json([
            'project' => $project,
            'total_budget' => $totalBudget,
            'total_cost' => $totalCost,
            'variance' => $totalBudget - $totalCost,
            'variance_percentage' => $totalBudget > 0 ? (($totalBudget - $totalCost) / $totalBudget) * 100 : 0,
            'by_category' => $allocations->groupBy('cost_category_id')->map(function ($items) {
                return [
                    'category' => $items->first()->costCategory->name,
                    'total' => $items->sum('amount'),
                    'count' => $items->count(),
                ];
            }),
            'by_cost_center' => $allocations->groupBy('cost_center_id')->map(function ($items) {
                return [
                    'cost_center' => $items->first()->costCenter->name,
                    'total' => $items->sum('amount'),
                    'count' => $items->count(),
                ];
            }),
            'allocations' => $allocations,
        ]);
    }
}
