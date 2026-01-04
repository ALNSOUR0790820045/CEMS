<?php

namespace App\Http\Controllers;

use App\Models\CostPlusOverheadAllocation;
use App\Models\CostPlusContract;
use App\Models\Project;
use Illuminate\Http\Request;

class CostPlusOverheadController extends Controller
{
    public function index()
    {
        $allocations = CostPlusOverheadAllocation::with([
            'costPlusContract',
            'project',
            'allocator'
        ])->latest()->get();

        if (request()->wantsJson()) {
            return response()->json($allocations);
        }

        return view('cost-plus.overhead.index', compact('allocations'));
    }

    public function allocate(Request $request)
    {
        $validated = $request->validate([
            'cost_plus_contract_id' => 'required|exists:cost_plus_contracts,id',
            'project_id' => 'required|exists:projects,id',
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'overhead_type' => 'required|in:admin_salaries,office_rent,utilities,insurance,depreciation,other',
            'description' => 'required|string',
            'total_overhead' => 'required|numeric|min:0',
            'allocation_percentage' => 'required|numeric|min:0|max:100',
            'allocated_amount' => 'required|numeric|min:0',
            'allocation_basis' => 'nullable|string',
            'is_reimbursable' => 'boolean',
        ]);

        $validated['allocated_by'] = auth()->id();

        $allocation = CostPlusOverheadAllocation::create($validated);

        if ($request->wantsJson()) {
            return response()->json($allocation, 201);
        }

        return back()->with('success', 'Overhead allocation created successfully');
    }
}
