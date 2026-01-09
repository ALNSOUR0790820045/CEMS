<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetCategory::with(['parent', 'children', 'glAssetAccount', 'glDepreciationAccount']);

        // Apply filters
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('parent_id')) {
            if ($request->parent_id === 'null') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Add company filter
        $query->where('company_id', auth()->user()->company_id);

        $categories = $query->orderBy('code')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:asset_categories,id',
            'default_useful_life' => 'nullable|integer|min:1',
            'default_depreciation_method' => 'required|in:straight_line,declining_balance,units_of_production',
            'default_depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'gl_asset_account_id' => 'nullable|exists:gl_accounts,id',
            'gl_depreciation_account_id' => 'nullable|exists:gl_accounts,id',
            'is_active' => 'boolean',
        ]);

        try {
            $validated['company_id'] = auth()->user()->company_id;
            $validated['is_active'] = $validated['is_active'] ?? true;

            $category = AssetCategory::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Asset category created successfully',
                'data' => $category->load(['parent', 'glAssetAccount', 'glDepreciationAccount'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create asset category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $category = AssetCategory::with([
            'parent',
            'children',
            'glAssetAccount',
            'glDepreciationAccount',
            'fixedAssets' => function($query) {
                $query->where('status', 'active')->limit(10);
            }
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    public function update(Request $request, $id)
    {
        $category = AssetCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:asset_categories,id',
            'default_useful_life' => 'nullable|integer|min:1',
            'default_depreciation_method' => 'sometimes|required|in:straight_line,declining_balance,units_of_production',
            'default_depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'gl_asset_account_id' => 'nullable|exists:gl_accounts,id',
            'gl_depreciation_account_id' => 'nullable|exists:gl_accounts,id',
            'is_active' => 'boolean',
        ]);

        try {
            // Prevent setting parent to itself or a child
            if (isset($validated['parent_id']) && $validated['parent_id'] == $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category cannot be its own parent'
                ], 422);
            }

            $category->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Asset category updated successfully',
                'data' => $category->fresh(['parent', 'glAssetAccount', 'glDepreciationAccount'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update asset category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $category = AssetCategory::findOrFail($id);

        // Check if category has assets
        if ($category->fixedAssets()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with associated assets'
            ], 422);
        }

        // Check if category has children
        if ($category->children()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with subcategories'
            ], 422);
        }

        try {
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Asset category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete asset category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
