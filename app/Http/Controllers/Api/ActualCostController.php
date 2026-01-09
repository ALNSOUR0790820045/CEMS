<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActualCost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ActualCostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ActualCost::with([
            'project',
            'costCode',
            'budgetItem',
            'vendor',
            'currency',
            'unit',
            'postedBy'
        ])->where('company_id', $request->user()->company_id);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('cost_code_id')) {
            $query->where('cost_code_id', $request->cost_code_id);
        }

        if ($request->has('reference_type')) {
            $query->where('reference_type', $request->reference_type);
        }

        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('transaction_date', [$request->date_from, $request->date_to]);
        }

        $actualCosts = $query->latest('transaction_date')->paginate($request->per_page ?? 15);

        return response()->json($actualCosts);
    }

    /**
     * Get actual costs by project
     */
    public function byProject($projectId)
    {
        $actualCosts = ActualCost::with([
            'costCode',
            'budgetItem',
            'vendor',
            'currency',
            'unit',
            'postedBy'
        ])
            ->where('project_id', $projectId)
            ->latest('transaction_date')
            ->get();

        return response()->json($actualCosts);
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
            'transaction_date' => 'required|date',
            'reference_type' => 'required|in:invoice,payroll,petty_cash,journal',
            'reference_id' => 'required|integer',
            'reference_number' => 'required|string|max:100',
            'vendor_id' => 'nullable|exists:vendors,id',
            'description' => 'required|string',
            'quantity' => 'nullable|numeric|min:0',
            'unit_id' => 'nullable|exists:units,id',
            'unit_rate' => 'nullable|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $exchangeRate = $request->exchange_rate ?? 1;
            $amountLocal = $request->amount * $exchangeRate;

            $actualCost = ActualCost::create(array_merge(
                $validator->validated(),
                [
                    'exchange_rate' => $exchangeRate,
                    'amount_local' => $amountLocal,
                    'posted_by_id' => $request->user()->id,
                    'posted_at' => now(),
                    'company_id' => $request->user()->company_id,
                ]
            ));

            // Update budget item actual amount if provided
            if ($request->budget_item_id) {
                $budgetItem = $actualCost->budgetItem;
                $budgetItem->updateActualAmount($amountLocal);
            }

            DB::commit();

            return response()->json($actualCost->load(['project', 'costCode', 'vendor']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create actual cost: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $actualCost = ActualCost::with([
            'project',
            'costCode',
            'budgetItem',
            'vendor',
            'currency',
            'unit',
            'postedBy'
        ])->findOrFail($id);

        return response()->json($actualCost);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $actualCost = ActualCost::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'transaction_date' => 'nullable|date',
            'description' => 'nullable|string',
            'quantity' => 'nullable|numeric|min:0',
            'unit_rate' => 'nullable|numeric|min:0',
            'amount' => 'nullable|numeric|min:0',
            'exchange_rate' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $oldAmount = $actualCost->amount_local;

            if ($request->has('amount') || $request->has('exchange_rate')) {
                $amount = $request->amount ?? $actualCost->amount;
                $exchangeRate = $request->exchange_rate ?? $actualCost->exchange_rate;
                $amountLocal = $amount * $exchangeRate;
                $request->merge(['amount_local' => $amountLocal]);
            }

            $actualCost->update($request->all());

            // Update budget item if amount changed
            if ($actualCost->budget_item_id && $request->has('amount')) {
                $budgetItem = $actualCost->budgetItem;
                $difference = $actualCost->amount_local - $oldAmount;
                $budgetItem->updateActualAmount($difference);
            }

            DB::commit();

            return response()->json($actualCost->load(['project', 'costCode']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update actual cost: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $actualCost = ActualCost::findOrFail($id);

        DB::beginTransaction();
        try {
            // Update budget item
            if ($actualCost->budget_item_id) {
                $budgetItem = $actualCost->budgetItem;
                $budgetItem->updateActualAmount(-$actualCost->amount_local);
            }

            $actualCost->delete();

            DB::commit();

            return response()->json(['message' => 'Actual cost deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to delete actual cost: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Import actual costs from transactions
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'reference_type' => 'required|in:invoice,payroll,petty_cash,journal',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // This would implement automatic import from AP invoices, payroll, etc.
        // Placeholder for actual implementation
        
        return response()->json(['message' => 'Import functionality to be implemented']);
    }
}
