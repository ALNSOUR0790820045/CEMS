<?php

namespace App\Http\Controllers;

use App\Models\PunchCategory;
use Illuminate\Http\Request;

class PunchCategoryController extends Controller
{
    public function index()
    {
        $categories = PunchCategory::with('parent')
            ->where('is_active', true)
            ->latest()
            ->paginate(20);

        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:punch_categories,code',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:punch_categories,id',
            'discipline' => 'nullable|string',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $category = PunchCategory::create($validated);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    public function show($id)
    {
        $category = PunchCategory::with(['parent', 'children'])->findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = PunchCategory::findOrFail($id);

        $validated = $request->validate([
            'code' => 'sometimes|string|unique:punch_categories,code,'.$id,
            'name' => 'sometimes|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:punch_categories,id',
            'discipline' => 'nullable|string',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'sometimes|boolean',
        ]);

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category->fresh(['parent', 'children'])
        ]);
    }

    public function destroy($id)
    {
        $category = PunchCategory::findOrFail($id);
        
        if ($category->children()->count() > 0) {
            return response()->json(['error' => 'Cannot delete category with sub-categories'], 400);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }

    public function tree()
    {
        $categories = PunchCategory::with('children.children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->get();

        return response()->json($categories);
    }
}
