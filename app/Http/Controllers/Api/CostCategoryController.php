<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CostCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CostCategory::where('company_id', $request->user()->company_id);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $costCategories = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($costCategories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:cost_categories,code',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'type' => 'required|in:direct_material,direct_labor,subcontractor,equipment,overhead,other',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $costCategory = CostCategory::create(array_merge(
            $validator->validated(),
            ['company_id' => $request->user()->company_id]
        ));

        return response()->json($costCategory, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $costCategory = CostCategory::with(['costAllocations', 'budgetItems'])->findOrFail($id);

        return response()->json($costCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $costCategory = CostCategory::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:cost_categories,code,' . $id,
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'type' => 'required|in:direct_material,direct_labor,subcontractor,equipment,overhead,other',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $costCategory->update($validator->validated());

        return response()->json($costCategory);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $costCategory = CostCategory::findOrFail($id);
        $costCategory->delete();

        return response()->json(['message' => 'Cost category deleted successfully']);
    }
}
