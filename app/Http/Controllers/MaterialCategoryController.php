<?php

namespace App\Http\Controllers;

use App\Models\MaterialCategory;
use Illuminate\Http\Request;

class MaterialCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MaterialCategory::with(['parent', 'children']);

        // Get only root categories
        if ($request->boolean('root_only')) {
            $query->whereNull('parent_id');
        }

        // Get tree structure
        if ($request->boolean('tree')) {
            $categories = MaterialCategory::whereNull('parent_id')
                ->with('children.children')
                ->get();
            return response()->json($categories);
        }

        $categories = $query->get();

        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:material_categories,id',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $category = MaterialCategory::create($validated);

        return response()->json($category->load('parent'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = MaterialCategory::with(['parent', 'children', 'materials'])
            ->findOrFail($id);

        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $category = MaterialCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:material_categories,id',
        ]);

        // Prevent circular reference
        if (isset($validated['parent_id']) && $validated['parent_id'] == $id) {
            return response()->json(['error' => 'Category cannot be its own parent'], 422);
        }

        $category->update($validated);

        return response()->json($category->load('parent'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = MaterialCategory::findOrFail($id);
        
        // Check if category has children
        if ($category->children()->count() > 0) {
            return response()->json(['error' => 'Cannot delete category with subcategories'], 422);
        }
        
        // Check if category has materials
        if ($category->materials()->count() > 0) {
            return response()->json(['error' => 'Cannot delete category with materials'], 422);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
