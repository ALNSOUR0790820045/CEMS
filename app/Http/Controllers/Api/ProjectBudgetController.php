<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProjectBudget;
use App\Models\ProjectBudgetItem;
use App\Models\BoqItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProjectBudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProjectBudget::with(['project', 'contract', 'currency', 'preparedBy', 'approvedBy'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('budget_type')) {
            $query->where('budget_type', $request->budget_type);
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $budgets = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($budgets);
    }

    /**
     * Get budgets by project
     */
    public function byProject($projectId)
    {
        $budgets = ProjectBudget::with(['contract', 'currency', 'preparedBy', 'approvedBy'])
            ->where('project_id', $projectId)
            ->latest()
            ->get();

        return response()->json($budgets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'budget_type' => 'required|in:original,revised,forecast',
            'budget_date' => 'required|date',
            'contract_value' => 'nullable|numeric|min:0',
            'direct_costs' => 'nullable|numeric|min:0',
            'indirect_costs' => 'nullable|numeric|min:0',
            'contingency_percentage' => 'nullable|numeric|min:0|max:100',
            'profit_margin_percentage' => 'nullable|numeric|min:0|max:100',
            'currency_id' => 'required|exists:currencies,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $budgetNumber = ProjectBudget::generateBudgetNumber(date('Y', strtotime($request->budget_date)));
            
            // Calculate contingency and profit amounts
            $directCosts = $request->direct_costs ?? 0;
            $indirectCosts = $request->indirect_costs ?? 0;
            $contingencyPercentage = $request->contingency_percentage ?? 0;
            $profitMarginPercentage = $request->profit_margin_percentage ?? 0;
            
            $baseCosts = $directCosts + $indirectCosts;
            $contingencyAmount = ($baseCosts * $contingencyPercentage) / 100;
            $totalBudget = $baseCosts + $contingencyAmount;
            $expectedProfit = ($totalBudget * $profitMarginPercentage) / 100;
            
            $budget = ProjectBudget::create(array_merge(
                $validator->validated(),
                [
                    'budget_number' => $budgetNumber,
                    'contingency_amount' => $contingencyAmount,
                    'total_budget' => $totalBudget,
                    'expected_profit' => $expectedProfit,
                    'version' => 1,
                    'status' => 'draft',
                    'prepared_by_id' => $request->user()->id,
                    'company_id' => $request->user()->company_id,
                ]
            ));

            DB::commit();

            return response()->json($budget->load(['project', 'contract', 'currency']), 201);
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
        $budget = ProjectBudget::with([
            'project',
            'contract',
            'currency',
            'preparedBy',
            'approvedBy',
            'items.costCode',
            'items.unit',
            'items.boqItem',
            'items.wbs'
        ])->findOrFail($id);

        return response()->json($budget);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $budget = ProjectBudget::findOrFail($id);

        if ($budget->status === 'approved' || $budget->status === 'active') {
            return response()->json(['error' => 'Cannot update approved or active budget'], 422);
        }

        $validator = Validator::make($request->all(), [
            'budget_date' => 'nullable|date',
            'contract_value' => 'nullable|numeric|min:0',
            'direct_costs' => 'nullable|numeric|min:0',
            'indirect_costs' => 'nullable|numeric|min:0',
            'contingency_percentage' => 'nullable|numeric|min:0|max:100',
            'profit_margin_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Recalculate if relevant fields changed
        if ($request->has(['direct_costs', 'indirect_costs', 'contingency_percentage'])) {
            $directCosts = $request->direct_costs ?? $budget->direct_costs;
            $indirectCosts = $request->indirect_costs ?? $budget->indirect_costs;
            $contingencyPercentage = $request->contingency_percentage ?? $budget->contingency_percentage;
            
            $baseCosts = $directCosts + $indirectCosts;
            $contingencyAmount = ($baseCosts * $contingencyPercentage) / 100;
            $totalBudget = $baseCosts + $contingencyAmount;
            
            $request->merge([
                'contingency_amount' => $contingencyAmount,
                'total_budget' => $totalBudget,
            ]);
        }

        $budget->update($request->all());

        return response()->json($budget->load(['project', 'contract', 'currency']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $budget = ProjectBudget::findOrFail($id);
        
        if ($budget->status === 'approved' || $budget->status === 'active') {
            return response()->json(['error' => 'Cannot delete approved or active budget'], 422);
        }

        $budget->delete();

        return response()->json(['message' => 'Budget deleted successfully']);
    }

    /**
     * Approve a budget
     */
    public function approve(Request $request, string $id)
    {
        $budget = ProjectBudget::findOrFail($id);

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

    /**
     * Create a revised budget
     */
    public function revise(Request $request, string $id)
    {
        $originalBudget = ProjectBudget::findOrFail($id);

        DB::beginTransaction();
        try {
            // Create new version
            $newBudget = $originalBudget->replicate();
            $newBudget->budget_number = ProjectBudget::generateBudgetNumber(date('Y'));
            $newBudget->budget_type = 'revised';
            $newBudget->version = $originalBudget->version + 1;
            $newBudget->status = 'draft';
            $newBudget->approved_by_id = null;
            $newBudget->approved_at = null;
            $newBudget->save();

            // Copy items if requested
            if ($request->copy_items) {
                foreach ($originalBudget->items as $item) {
                    $newItem = $item->replicate();
                    $newItem->project_budget_id = $newBudget->id;
                    $newItem->save();
                }
            }

            DB::commit();

            return response()->json($newBudget->load(['items']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create revised budget: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get budget items
     */
    public function getItems(string $id)
    {
        $budget = ProjectBudget::findOrFail($id);
        $items = $budget->items()
            ->with(['costCode', 'unit', 'boqItem', 'wbs'])
            ->get();

        return response()->json($items);
    }

    /**
     * Update budget items
     */
    public function updateItems(Request $request, string $id)
    {
        $budget = ProjectBudget::findOrFail($id);

        if ($budget->status === 'approved' || $budget->status === 'active') {
            return response()->json(['error' => 'Cannot update items of approved or active budget'], 422);
        }

        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.cost_code_id' => 'required|exists:cost_codes,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'nullable|numeric|min:0',
            'items.*.unit_rate' => 'nullable|numeric|min:0',
            'items.*.budgeted_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Delete existing items
            $budget->items()->delete();

            // Create new items
            foreach ($request->items as $itemData) {
                ProjectBudgetItem::create(array_merge($itemData, [
                    'project_budget_id' => $budget->id,
                ]));
            }

            // Update budget total from items
            $budget->updateFromItems();

            DB::commit();

            return response()->json($budget->load(['items.costCode']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update items: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Import budget items from BOQ
     */
    public function importFromBoq(Request $request, string $id)
    {
        $budget = ProjectBudget::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'boq_items' => 'required|array',
            'boq_items.*' => 'exists:boq_items,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $boqItems = BoqItem::whereIn('id', $request->boq_items)->get();

            foreach ($boqItems as $boqItem) {
                ProjectBudgetItem::create([
                    'project_budget_id' => $budget->id,
                    'cost_code_id' => $request->default_cost_code_id ?? 1, // Would need proper mapping
                    'boq_item_id' => $boqItem->id,
                    'description' => $boqItem->description ?? $boqItem->item_description,
                    'quantity' => $boqItem->quantity ?? 0,
                    'unit_id' => $boqItem->unit_id,
                    'unit_rate' => $boqItem->unit_rate ?? 0,
                    'budgeted_amount' => $boqItem->total_amount ?? ($boqItem->quantity * $boqItem->unit_rate),
                ]);
            }

            $budget->updateFromItems();

            DB::commit();

            return response()->json($budget->load(['items']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to import from BOQ: ' . $e->getMessage()], 500);
        }
    }
}
