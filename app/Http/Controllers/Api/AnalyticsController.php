<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ARInvoice;
use App\Models\ApInvoice;
use App\Models\Transaction;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Get project summary analytics.
     */
    public function projectSummary(Request $request)
    {
        $companyId = $request->user()->company_id;

        $summary = [
            'total_projects' => Project::where('company_id', $companyId)->count(),
            'active_projects' => Project::where('company_id', $companyId)
                ->where('status', 'in_progress')->count(),
            'completed_projects' => Project::where('company_id', $companyId)
                ->where('status', 'completed')->count(),
            'total_budget' => Project::where('company_id', $companyId)
                ->sum('budget') ?? 0,
            'total_spent' => Project::where('company_id', $companyId)
                ->sum('actual_cost') ?? 0,
        ];

        return response()->json($summary);
    }

    /**
     * Get financial overview analytics.
     */
    public function financialOverview(Request $request)
    {
        $companyId = $request->user()->company_id;

        $overview = [
            'total_revenue' => ARInvoice::where('company_id', $companyId)
                ->where('status', 'paid')
                ->sum('total_amount') ?? 0,
            'total_expenses' => ApInvoice::where('company_id', $companyId)
                ->where('status', 'paid')
                ->sum('total_amount') ?? 0,
            'accounts_receivable' => ARInvoice::where('company_id', $companyId)
                ->whereIn('status', ['pending', 'partial'])
                ->sum('balance_due') ?? 0,
            'accounts_payable' => ApInvoice::where('company_id', $companyId)
                ->whereIn('status', ['pending', 'approved'])
                ->sum('balance_due') ?? 0,
        ];

        $overview['net_profit'] = $overview['total_revenue'] - $overview['total_expenses'];

        return response()->json($overview);
    }

    /**
     * Get revenue trend analytics.
     */
    public function revenueTrend(Request $request)
    {
        $companyId = $request->user()->company_id;
        $months = $request->get('months', 12);

        $trend = ARInvoice::where('company_id', $companyId)
            ->where('status', 'paid')
            ->where('invoice_date', '>=', now()->subMonths($months))
            ->select(
                DB::raw('DATE_FORMAT(invoice_date, "%Y-%m") as month'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'labels' => $trend->pluck('month'),
            'data' => $trend->pluck('revenue'),
        ]);
    }

    /**
     * Get expense breakdown analytics.
     */
    public function expenseBreakdown(Request $request)
    {
        $companyId = $request->user()->company_id;

        $breakdown = ApInvoice::where('company_id', $companyId)
            ->where('status', 'paid')
            ->join('vendors', 'ap_invoices.vendor_id', '=', 'vendors.id')
            ->select(
                'vendors.name as vendor_name',
                DB::raw('SUM(ap_invoices.total_amount) as total_expense')
            )
            ->groupBy('vendors.id', 'vendors.name')
            ->orderByDesc('total_expense')
            ->limit(10)
            ->get();

        return response()->json([
            'labels' => $breakdown->pluck('vendor_name'),
            'data' => $breakdown->pluck('total_expense'),
        ]);
    }

    /**
     * Get cash position analytics.
     */
    public function cashPosition(Request $request)
    {
        $companyId = $request->user()->company_id;

        $cashFlow = [
            'cash_inflow' => ARInvoice::where('company_id', $companyId)
                ->where('status', 'paid')
                ->whereMonth('payment_date', now()->month)
                ->sum('total_amount') ?? 0,
            'cash_outflow' => ApInvoice::where('company_id', $companyId)
                ->where('status', 'paid')
                ->whereMonth('payment_date', now()->month)
                ->sum('total_amount') ?? 0,
        ];

        $cashFlow['net_cash_flow'] = $cashFlow['cash_inflow'] - $cashFlow['cash_outflow'];

        // Get bank account balances if available
        $cashFlow['bank_balances'] = DB::table('bank_accounts')
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->sum('current_balance') ?? 0;

        return response()->json($cashFlow);
    }

    /**
     * Get project performance analytics.
     */
    public function projectPerformance(Request $request)
    {
        $companyId = $request->user()->company_id;

        $performance = Project::where('company_id', $companyId)
            ->whereIn('status', ['in_progress', 'completed'])
            ->select(
                'id',
                'name',
                'budget',
                'actual_cost',
                'progress_percentage',
                'status',
                DB::raw('(budget - actual_cost) as variance')
            )
            ->orderByDesc('budget')
            ->limit(10)
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'budget' => $project->budget,
                    'actual_cost' => $project->actual_cost,
                    'progress' => $project->progress_percentage,
                    'status' => $project->status,
                    'variance' => $project->variance,
                    'variance_percentage' => $project->budget > 0 
                        ? round(($project->variance / $project->budget) * 100, 2) 
                        : 0,
                ];
            });

        return response()->json($performance);
    }

    /**
     * Get HR metrics analytics.
     */
    public function hrMetrics(Request $request)
    {
        $companyId = $request->user()->company_id;

        $metrics = [
            'total_employees' => Employee::where('company_id', $companyId)->count(),
            'active_employees' => Employee::where('company_id', $companyId)
                ->where('status', 'active')->count(),
            'departments_count' => DB::table('departments')
                ->where('company_id', $companyId)
                ->where('is_active', true)
                ->count(),
        ];

        // Get employee distribution by department
        $distribution = Employee::where('company_id', $companyId)
            ->where('status', 'active')
            ->join('departments', 'employees.department_id', '=', 'departments.id')
            ->select(
                'departments.name as department_name',
                DB::raw('COUNT(*) as employee_count')
            )
            ->groupBy('departments.id', 'departments.name')
            ->get();

        $metrics['department_distribution'] = [
            'labels' => $distribution->pluck('department_name'),
            'data' => $distribution->pluck('employee_count'),
        ];

        return response()->json($metrics);
    }
}
