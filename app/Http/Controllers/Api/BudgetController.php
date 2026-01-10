<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\BudgetItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Budget::with(['costCenter', 'project', 'approvedBy'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('budget_type')) {
            $query->where('budget_type', $request->budget_type);
        }

        if ($request->has('fiscal_year')) {
            $query->where('fiscal_year', $request->fiscal_year);
        }

        $budgets = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($budgets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'budget_name' => 'required|string|max:255',
            'fiscal_year' => 'required|integer|min:2000|max:2100',
            'budget_type' => 'required|in:annual,project,department',
            'cost_center_id' => 'nullable|exists:cost_centers,id',
            'project_id' => 'nullable|exists:projects,id',
            'total_amount' => 'nullable|numeric|min:0',
            'items' => 'nullable|array',
            'items.*.cost_category_id' => 'required|exists:cost_categories,id',
            'items.*.gl_account_id' => 'nullable|exists:gl_accounts,id',
            'items.*.month' => 'nullable|integer|min:1|max:12',
            'items.*.budgeted_amount' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $budgetNumber = Budget::generateBudgetNumber($request->fiscal_year);
            
            $budget = Budget::create(array_merge(
                $validator->validated(),
                [
                    'budget_number' => $budgetNumber,
                    'company_id' => $request->user()->company_id,
                ]
            ));

            // Create budget items if provided
            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    BudgetItem::create(array_merge($item, ['budget_id' => $budget->id]));
                }
            }

            DB::commit();

            return response()->json($budget->load(['items', 'costCenter', 'project']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create budget: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $budget = Budget::with(['items.costCategory', 'items.glAccount', 'costCenter', 'project', 'approvedBy'])
            ->findOrFail($id);

        return response()->json($budget);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $budget = Budget::findOrFail($id);

        if ($budget->status === 'approved') {
            return response()->json(['error' => 'Cannot update approved budget'], 422);
        }

        $validator = Validator::make($request->all(), [
            'budget_name' => 'required|string|max:255',
            'fiscal_year' => 'required|integer|min:2000|max:2100',
            'budget_type' => 'required|in:annual,project,department',
            'cost_center_id' => 'nullable|exists:cost_centers,id',
            'project_id' => 'nullable|exists:projects,id',
            'total_amount' => 'nullable|numeric|min:0',
            'status' => 'in:draft,approved,active,closed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $budget->update($validator->validated());

        return response()->json($budget->load(['items', 'costCenter', 'project']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $budget = Budget::findOrFail($id);
        
        if ($budget->status === 'approved') {
            return response()->json(['error' => 'Cannot delete approved budget'], 422);
        }

        $budget->delete();

        return response()->json(['message' => 'Budget deleted successfully']);
    }

    /**
     * Approve a budget
     */
    public function approve(Request $request, string $id)
    {
        $budget = Budget::findOrFail($id);

        if ($budget->status !== 'draft') {
            return response()->json(['error' => 'Only draft budgets can be approved'], 422);
        }

        $budget->update([
            'status' => 'approved',
            'approved_by_id' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return response()->json($budget->load(['approvedBy']));
    }
}
