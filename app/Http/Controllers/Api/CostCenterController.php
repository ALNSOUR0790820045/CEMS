<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CostCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CostCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CostCenter::with(['parent', 'children', 'company']);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $costCenters = $query->get();

        return response()->json([
            'success' => true,
            'data' => $costCenters,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:cost_centers,code',
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:cost_centers,id',
            'type' => 'required|in:project,department,activity,asset',
            'is_active' => 'boolean',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $costCenter = CostCenter::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cost center created successfully',
            'data' => $costCenter->load(['parent', 'company']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $costCenter = CostCenter::with(['parent', 'children', 'company', 'costAllocations'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $costCenter,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $costCenter = CostCenter::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'code' => 'sometimes|required|string|unique:cost_centers,code,' . $id,
            'name' => 'sometimes|required|string|max:255',
            'parent_id' => 'nullable|exists:cost_centers,id',
            'type' => 'sometimes|required|in:project,department,activity,asset',
            'is_active' => 'boolean',
            'company_id' => 'sometimes|required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $costCenter->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cost center updated successfully',
            'data' => $costCenter->load(['parent', 'company']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $costCenter = CostCenter::findOrFail($id);
        $costCenter->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cost center deleted successfully',
        ]);
    }
}
