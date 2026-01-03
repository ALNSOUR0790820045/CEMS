<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Budget::with(['company', 'project', 'costCenter', 'budgetItems']);

        if ($request->has('fiscal_year')) {
            $query->where('fiscal_year', $request->fiscal_year);
        }

        if ($request->has('budget_type')) {
            $query->where('budget_type', $request->budget_type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $budgets = $query->get();

        return response()->json([
            'success' => true,
            'data' => $budgets,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'budget_name' => 'required|string|max:255',
            'fiscal_year' => 'required|integer|min:2000|max:2100',
            'budget_type' => 'required|in:operating,capital,project',
            'project_id' => 'nullable|exists:projects,id',
            'cost_center_id' => 'nullable|exists:cost_centers,id',
            'status' => 'in:draft,approved,active,closed',
            'total_budget' => 'required|numeric|min:0',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $budget = Budget::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Budget created successfully',
            'data' => $budget->load(['company', 'project', 'costCenter']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $budget = Budget::with(['company', 'project', 'costCenter', 'budgetItems.glAccount'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $budget,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $budget = Budget::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'budget_name' => 'sometimes|required|string|max:255',
            'fiscal_year' => 'sometimes|required|integer|min:2000|max:2100',
            'budget_type' => 'sometimes|required|in:operating,capital,project',
            'project_id' => 'nullable|exists:projects,id',
            'cost_center_id' => 'nullable|exists:cost_centers,id',
            'status' => 'in:draft,approved,active,closed',
            'total_budget' => 'sometimes|required|numeric|min:0',
            'company_id' => 'sometimes|required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $budget->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Budget updated successfully',
            'data' => $budget->load(['company', 'project', 'costCenter']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $budget = Budget::findOrFail($id);
        $budget->delete();

        return response()->json([
            'success' => true,
            'message' => 'Budget deleted successfully',
        ]);
    }
}
