<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CostAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CostAllocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CostAllocation::with(['costCenter', 'costCategory', 'project', 'glAccount', 'currency', 'postedBy'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('cost_center_id')) {
            $query->where('cost_center_id', $request->cost_center_id);
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('from_date')) {
            $query->where('allocation_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('allocation_date', '<=', $request->to_date);
        }

        $allocations = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($allocations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'allocation_date' => 'required|date',
            'cost_center_id' => 'required|exists:cost_centers,id',
            'cost_category_id' => 'required|exists:cost_categories,id',
            'project_id' => 'nullable|exists:projects,id',
            'gl_account_id' => 'required|exists:gl_accounts,id',
            'amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'reference_type' => 'in:invoice,payroll,journal,manual',
            'reference_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $allocationNumber = CostAllocation::generateAllocationNumber();

        $allocation = CostAllocation::create(array_merge(
            $validator->validated(),
            [
                'allocation_number' => $allocationNumber,
                'company_id' => $request->user()->company_id,
                'status' => 'draft',
            ]
        ));

        return response()->json($allocation->load(['costCenter', 'costCategory', 'project', 'glAccount', 'currency']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $allocation = CostAllocation::with(['costCenter', 'costCategory', 'project', 'glAccount', 'currency', 'postedBy'])
            ->findOrFail($id);

        return response()->json($allocation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $allocation = CostAllocation::findOrFail($id);

        if ($allocation->status === 'posted') {
            return response()->json(['error' => 'Cannot update posted allocation'], 422);
        }

        $validator = Validator::make($request->all(), [
            'allocation_date' => 'required|date',
            'cost_center_id' => 'required|exists:cost_centers,id',
            'cost_category_id' => 'required|exists:cost_categories,id',
            'project_id' => 'nullable|exists:projects,id',
            'gl_account_id' => 'required|exists:gl_accounts,id',
            'amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'reference_type' => 'in:invoice,payroll,journal,manual',
            'reference_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $allocation->update($validator->validated());

        return response()->json($allocation->load(['costCenter', 'costCategory', 'project', 'glAccount', 'currency']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $allocation = CostAllocation::findOrFail($id);

        if ($allocation->status === 'posted') {
            return response()->json(['error' => 'Cannot delete posted allocation'], 422);
        }

        $allocation->delete();

        return response()->json(['message' => 'Cost allocation deleted successfully']);
    }

    /**
     * Post an allocation
     */
    public function post(Request $request, string $id)
    {
        $allocation = CostAllocation::findOrFail($id);

        if ($allocation->status !== 'draft') {
            return response()->json(['error' => 'Only draft allocations can be posted'], 422);
        }

        $allocation->update([
            'status' => 'posted',
            'posted_by_id' => $request->user()->id,
            'posted_at' => now(),
        ]);

        return response()->json($allocation->load(['postedBy']));
    }

    /**
     * Reverse an allocation
     */
    public function reverse(Request $request, string $id)
    {
        $allocation = CostAllocation::findOrFail($id);

        if ($allocation->status !== 'posted') {
            return response()->json(['error' => 'Only posted allocations can be reversed'], 422);
        }

        $allocation->update(['status' => 'reversed']);

        return response()->json($allocation);
    }
}
