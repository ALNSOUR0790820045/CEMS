<?php

namespace App\Http\Controllers;

use App\Models\RiskCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RiskCategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = RiskCategory::with('parent');

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $categories = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:risk_categories',
            'name' => 'required|string',
            'name_en' => 'nullable|string',
            'parent_id' => 'nullable|exists:risk_categories,id',
            'description' => 'nullable|string',
            'default_probability' => 'nullable|integer|min:1|max:5',
            'default_impact' => 'nullable|integer|min:1|max:5',
            'company_id' => 'required|exists:companies,id',
        ]);

        $category = RiskCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Risk category created successfully',
            'data' => $category,
        ], 201);
    }

    public function show(RiskCategory $riskCategory): JsonResponse
    {
        $riskCategory->load(['parent', 'children']);

        return response()->json([
            'success' => true,
            'data' => $riskCategory,
        ]);
    }

    public function update(Request $request, RiskCategory $riskCategory): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'name_en' => 'nullable|string',
            'description' => 'nullable|string',
            'default_probability' => 'nullable|integer|min:1|max:5',
            'default_impact' => 'nullable|integer|min:1|max:5',
            'is_active' => 'sometimes|boolean',
        ]);

        $riskCategory->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Risk category updated successfully',
            'data' => $riskCategory,
        ]);
    }

    public function destroy(RiskCategory $riskCategory): JsonResponse
    {
        $riskCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Risk category deleted successfully',
        ]);
    }

    public function tree(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id');
        $categories = RiskCategory::where('company_id', $companyId)
            ->whereNull('parent_id')
            ->with('children')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}
