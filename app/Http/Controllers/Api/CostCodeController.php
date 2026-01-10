<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CostCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CostCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CostCode::with(['parent', 'company'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->has('cost_type')) {
            $query->where('cost_type', $request->cost_type);
        }

        if ($request->has('cost_category')) {
            $query->where('cost_category', $request->cost_category);
        }

        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        $costCodes = $query->orderBy('code')->paginate($request->per_page ?? 15);

        return response()->json($costCodes);
    }

    /**
     * Get cost codes as tree structure
     */
    public function tree(Request $request)
    {
        $rootCodes = CostCode::with('children.children')
            ->where('company_id', $request->user()->company_id)
            ->whereNull('parent_id')
            ->orderBy('code')
            ->get();

        return response()->json($rootCodes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:cost_codes,code',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:cost_codes,id',
            'cost_type' => 'required|in:direct,indirect',
            'cost_category' => 'nullable|in:material,labor,equipment,subcontractor,overhead',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Calculate level
        $level = 1;
        if ($request->parent_id) {
            $parent = CostCode::find($request->parent_id);
            $level = $parent->level + 1;
        }

        $costCode = CostCode::create(array_merge(
            $validator->validated(),
            [
                'level' => $level,
                'company_id' => $request->user()->company_id,
            ]
        ));

        return response()->json($costCode->load('parent'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $costCode = CostCode::with(['parent', 'children'])->findOrFail($id);

        return response()->json($costCode);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $costCode = CostCode::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:cost_codes,code,' . $id,
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:cost_codes,id',
            'cost_type' => 'required|in:direct,indirect',
            'cost_category' => 'nullable|in:material,labor,equipment,subcontractor,overhead',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Recalculate level if parent changed
        if ($request->has('parent_id') && $request->parent_id != $costCode->parent_id) {
            $level = 1;
            if ($request->parent_id) {
                $parent = CostCode::find($request->parent_id);
                $level = $parent->level + 1;
            }
            $request->merge(['level' => $level]);
        }

        $costCode->update($request->all());

        return response()->json($costCode->load('parent'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $costCode = CostCode::findOrFail($id);

        // Check if has children
        if ($costCode->children()->count() > 0) {
            return response()->json(['error' => 'Cannot delete cost code with children'], 422);
        }

        // Check if has budget items
        if ($costCode->projectBudgetItems()->count() > 0) {
            return response()->json(['error' => 'Cannot delete cost code with budget items'], 422);
        }

        $costCode->delete();

        return response()->json(['message' => 'Cost code deleted successfully']);
    }
}
