<?php

namespace App\Http\Controllers;

use App\Models\CostPlusContract;
use App\Models\CostPlusTransaction;
use App\Models\CostPlusInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CostPlusReportController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_contracts' => CostPlusContract::count(),
            'active_contracts' => CostPlusContract::whereHas('contract', function($q) {
                $q->where('status', 'active');
            })->count(),
            'pending_transactions' => CostPlusTransaction::where('status', 'pending')->count(),
            'total_invoices' => CostPlusInvoice::count(),
            'pending_approvals' => CostPlusTransaction::where('status', 'documented')->count(),
        ];

        if (request()->wantsJson()) {
            return response()->json($stats);
        }

        return view('cost-plus.dashboard', compact('stats'));
    }

    public function gmpStatus()
    {
        $contracts = CostPlusContract::where('has_gmp', true)
            ->with(['project', 'invoices'])
            ->get();

        $gmpData = $contracts->map(function($contract) {
            $status = $contract->checkGMPStatus();
            return [
                'contract_id' => $contract->id,
                'project_name' => $contract->project->name,
                'gmp' => $contract->guaranteed_maximum_price,
                'total_costs' => $contract->invoices->sum('cumulative_costs'),
                'remaining' => $status['remaining'],
                'percentage_used' => $status['percentage_used'],
                'exceeded' => $status['exceeded'],
            ];
        });

        if (request()->wantsJson()) {
            return response()->json($gmpData);
        }

        return view('cost-plus.gmp-tracker', compact('gmpData'));
    }

    public function openBookReport(Request $request)
    {
        $contracts = CostPlusContract::with('project')->get();
        $contractId = $request->input('contract_id');
        
        if (!$contractId) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Please select a contract']);
            }
            return view('cost-plus.open-book', compact('contracts'));
        }
        
        $contract = CostPlusContract::with([
            'project',
            'transactions' => function($q) {
                $q->where('status', 'approved');
            },
            'invoices',
            'overheadAllocations'
        ])->findOrFail($contractId);

        // Group transactions by cost type
        $transactionsByCostType = $contract->transactions->groupBy('cost_type')
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('net_amount'),
                    'transactions' => $group,
                ];
            });

        $data = [
            'contract' => $contract,
            'transactions_by_type' => $transactionsByCostType,
            'total_costs' => $contract->transactions->sum('net_amount'),
            'total_invoiced' => $contract->invoices->sum('total_amount'),
            'documentation_rate' => $contract->transactions->count() > 0 
                ? ($contract->transactions->where('documentation_complete', true)->count() / $contract->transactions->count() * 100)
                : 0,
        ];

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('cost-plus.open-book', compact('data', 'contracts'));
    }

    public function reports()
    {
        $contracts = CostPlusContract::with('project')->get();

        if (request()->wantsJson()) {
            return response()->json($contracts);
        }

        return view('cost-plus.reports', compact('contracts'));
    }
}
