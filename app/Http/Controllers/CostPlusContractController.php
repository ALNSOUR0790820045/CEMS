<?php

namespace App\Http\Controllers;

use App\Models\CostPlusContract;
use App\Models\Contract;
use App\Models\Project;
use Illuminate\Http\Request;

class CostPlusContractController extends Controller
{
    public function index()
    {
        $contracts = CostPlusContract::with(['contract', 'project'])->latest()->get();
        
        if (request()->wantsJson()) {
            return response()->json($contracts);
        }
        
        return view('cost-plus.contracts.index', compact('contracts'));
    }

    public function create()
    {
        $contracts = Contract::where('contract_type', 'cost_plus')->get();
        $projects = Project::where('status', 'active')->get();
        
        return view('cost-plus.contracts.create', compact('contracts', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'project_id' => 'required|exists:projects,id',
            'fee_type' => 'required|in:percentage,fixed_fee,incentive,hybrid',
            'fee_percentage' => 'nullable|numeric|min:0|max:100',
            'fixed_fee_amount' => 'nullable|numeric|min:0',
            'has_gmp' => 'boolean',
            'guaranteed_maximum_price' => 'nullable|numeric|min:0',
            'gmp_savings_share' => 'nullable|numeric|min:0|max:100',
            'overhead_reimbursable' => 'boolean',
            'overhead_percentage' => 'nullable|numeric|min:0|max:100',
            'overhead_method' => 'required|in:percentage,actual,allocated',
            'reimbursable_costs' => 'array',
            'non_reimbursable_costs' => 'array',
            'currency' => 'string|size:3',
            'notes' => 'nullable|string',
        ]);

        $costPlusContract = CostPlusContract::create($validated);

        if ($request->wantsJson()) {
            return response()->json($costPlusContract, 201);
        }

        return redirect()->route('cost-plus.contracts.show', $costPlusContract)
            ->with('success', 'Cost Plus Contract created successfully');
    }

    public function show($id)
    {
        $contract = CostPlusContract::with([
            'contract',
            'project',
            'transactions',
            'invoices',
            'overheadAllocations'
        ])->findOrFail($id);

        if (request()->wantsJson()) {
            return response()->json($contract);
        }

        return view('cost-plus.contracts.show', compact('contract'));
    }

    public function update(Request $request, $id)
    {
        $contract = CostPlusContract::findOrFail($id);

        $validated = $request->validate([
            'fee_type' => 'sometimes|in:percentage,fixed_fee,incentive,hybrid',
            'fee_percentage' => 'nullable|numeric|min:0|max:100',
            'fixed_fee_amount' => 'nullable|numeric|min:0',
            'has_gmp' => 'boolean',
            'guaranteed_maximum_price' => 'nullable|numeric|min:0',
            'gmp_savings_share' => 'nullable|numeric|min:0|max:100',
            'overhead_reimbursable' => 'boolean',
            'overhead_percentage' => 'nullable|numeric|min:0|max:100',
            'overhead_method' => 'sometimes|in:percentage,actual,allocated',
            'reimbursable_costs' => 'array',
            'non_reimbursable_costs' => 'array',
            'notes' => 'nullable|string',
        ]);

        $contract->update($validated);

        if ($request->wantsJson()) {
            return response()->json($contract);
        }

        return redirect()->route('cost-plus.contracts.show', $contract)
            ->with('success', 'Cost Plus Contract updated successfully');
    }

    public function destroy($id)
    {
        $contract = CostPlusContract::findOrFail($id);
        $contract->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Contract deleted successfully']);
        }

        return redirect()->route('cost-plus.contracts.index')
            ->with('success', 'Contract deleted successfully');
    }
}
