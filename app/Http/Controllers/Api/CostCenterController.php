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
        $query = CostCenter::with(['company', 'parent', 'children'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        $costCenters = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($costCenters);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:cost_centers,code',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:cost_centers,id',
            'type' => 'required|in:project,department,overhead,administrative',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $costCenter = CostCenter::create(array_merge(
            $validator->validated(),
            ['company_id' => $request->user()->company_id]
        ));

        return response()->json($costCenter->load(['company', 'parent']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $costCenter = CostCenter::with(['company', 'parent', 'children', 'costAllocations', 'budgets'])
            ->findOrFail($id);

        return response()->json($costCenter);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $costCenter = CostCenter::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:cost_centers,code,' . $id,
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:cost_centers,id',
            'type' => 'required|in:project,department,overhead,administrative',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $costCenter->update($validator->validated());

        return response()->json($costCenter->load(['company', 'parent']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $costCenter = CostCenter::findOrFail($id);
        $costCenter->delete();

        return response()->json(['message' => 'Cost center deleted successfully']);
    }
}
