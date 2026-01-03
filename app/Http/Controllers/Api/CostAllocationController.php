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
        $query = CostAllocation::with(['company', 'costCenter', 'glAccount', 'currency']);

        if ($request->has('cost_center_id')) {
            $query->where('cost_center_id', $request->cost_center_id);
        }

        if ($request->has('gl_account_id')) {
            $query->where('gl_account_id', $request->gl_account_id);
        }

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        $costAllocations = $query->orderBy('transaction_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $costAllocations,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_date' => 'required|date',
            'source_type' => 'required|string|max:255',
            'source_id' => 'required|integer',
            'cost_center_id' => 'required|exists:cost_centers,id',
            'gl_account_id' => 'required|exists:gl_accounts,id',
            'amount' => 'required|numeric',
            'currency_id' => 'required|exists:currencies,id',
            'description' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $costAllocation = CostAllocation::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cost allocation created successfully',
            'data' => $costAllocation->load(['company', 'costCenter', 'glAccount', 'currency']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $costAllocation = CostAllocation::with(['company', 'costCenter', 'glAccount', 'currency'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $costAllocation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $costAllocation = CostAllocation::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'transaction_date' => 'sometimes|required|date',
            'source_type' => 'sometimes|required|string|max:255',
            'source_id' => 'sometimes|required|integer',
            'cost_center_id' => 'sometimes|required|exists:cost_centers,id',
            'gl_account_id' => 'sometimes|required|exists:gl_accounts,id',
            'amount' => 'sometimes|required|numeric',
            'currency_id' => 'sometimes|required|exists:currencies,id',
            'description' => 'nullable|string',
            'company_id' => 'sometimes|required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $costAllocation->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cost allocation updated successfully',
            'data' => $costAllocation->load(['company', 'costCenter', 'glAccount', 'currency']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $costAllocation = CostAllocation::findOrFail($id);
        $costAllocation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cost allocation deleted successfully',
        ]);
    }
}
