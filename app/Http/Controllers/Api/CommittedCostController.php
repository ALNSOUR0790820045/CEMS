<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommittedCost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommittedCostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CommittedCost::with([
            'project',
            'costCode',
            'budgetItem',
            'vendor',
            'currency'
        ])->where('company_id', $request->user()->company_id);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('commitment_type')) {
            $query->where('commitment_type', $request->commitment_type);
        }

        $committedCosts = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($committedCosts);
    }

    /**
     * Get committed costs by project
     */
    public function byProject($projectId)
    {
        $committedCosts = CommittedCost::with([
            'costCode',
            'budgetItem',
            'vendor',
            'currency'
        ])
            ->where('project_id', $projectId)
            ->latest()
            ->get();

        return response()->json($committedCosts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'cost_code_id' => 'required|exists:cost_codes,id',
            'budget_item_id' => 'nullable|exists:project_budget_items,id',
            'commitment_type' => 'required|in:purchase_order,subcontract,service_order',
            'commitment_id' => 'required|integer',
            'commitment_number' => 'required|string|max:100',
            'vendor_id' => 'required|exists:vendors,id',
            'description' => 'required|string',
            'original_amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $committedCost = CommittedCost::create(array_merge(
                $validator->validated(),
                [
                    'current_amount' => $request->original_amount,
                    'remaining_amount' => $request->original_amount,
                    'status' => 'open',
                    'company_id' => $request->user()->company_id,
                ]
            ));

            // Update budget item committed amount if provided
            if ($request->budget_item_id) {
                $budgetItem = $committedCost->budgetItem;
                $budgetItem->updateCommittedAmount($request->original_amount);
            }

            DB::commit();

            return response()->json($committedCost->load(['project', 'costCode', 'vendor']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create committed cost: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $committedCost = CommittedCost::with([
            'project',
            'costCode',
            'budgetItem',
            'vendor',
            'currency'
        ])->findOrFail($id);

        return response()->json($committedCost);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $committedCost = CommittedCost::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'description' => 'nullable|string',
            'approved_changes' => 'nullable|numeric',
            'invoiced_amount' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:open,partially_invoiced,closed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            if ($request->has('approved_changes')) {
                $committedCost->applyChange($request->approved_changes);
            }

            if ($request->has('invoiced_amount')) {
                $newInvoicedAmount = $request->invoiced_amount - $committedCost->invoiced_amount;
                $committedCost->addInvoicedAmount($newInvoicedAmount);
            }

            $committedCost->update($request->except(['approved_changes', 'invoiced_amount']));

            DB::commit();

            return response()->json($committedCost->load(['project', 'costCode']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update committed cost: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $committedCost = CommittedCost::findOrFail($id);

        if ($committedCost->invoiced_amount > 0) {
            return response()->json(['error' => 'Cannot delete committed cost with invoices'], 422);
        }

        DB::beginTransaction();
        try {
            // Update budget item
            if ($committedCost->budget_item_id) {
                $budgetItem = $committedCost->budgetItem;
                $budgetItem->updateCommittedAmount(-$committedCost->current_amount);
            }

            $committedCost->delete();

            DB::commit();

            return response()->json(['message' => 'Committed cost deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to delete committed cost: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Sync committed costs from purchase orders
     */
    public function syncFromPurchaseOrders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // This would implement automatic sync from purchase orders
        // Placeholder for actual implementation
        
        return response()->json(['message' => 'Sync from purchase orders to be implemented']);
    }

    /**
     * Sync committed costs from subcontracts
     */
    public function syncFromSubcontracts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // This would implement automatic sync from subcontracts
        // Placeholder for actual implementation
        
        return response()->json(['message' => 'Sync from subcontracts to be implemented']);
    }
}
