<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBankReconciliationRequest;
use App\Http\Requests\UpdateBankReconciliationRequest;
use App\Models\BankReconciliation;
use App\Models\ReconciliationItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankReconciliationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = BankReconciliation::with(['bankAccount', 'company', 'preparedBy']);

        // Filter by bank account
        if ($request->has('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('reconciliation_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->where('reconciliation_date', '<=', $request->to_date);
        }

        $reconciliations = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reconciliations,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBankReconciliationRequest $request): JsonResponse
    {
        $reconciliation = BankReconciliation::create(array_merge(
            $request->validated(),
            ['prepared_by_id' => auth()->id(), 'status' => 'draft']
        ));

        $reconciliation->load(['bankAccount', 'company', 'preparedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Bank reconciliation created successfully',
            'data' => $reconciliation,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(BankReconciliation $bankReconciliation): JsonResponse
    {
        $bankReconciliation->load([
            'bankAccount',
            'company',
            'preparedBy',
            'approvedBy',
            'items',
        ]);

        return response()->json([
            'success' => true,
            'data' => $bankReconciliation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBankReconciliationRequest $request, BankReconciliation $bankReconciliation): JsonResponse
    {
        $bankReconciliation->update($request->validated());
        $bankReconciliation->load(['bankAccount', 'items']);

        return response()->json([
            'success' => true,
            'message' => 'Bank reconciliation updated successfully',
            'data' => $bankReconciliation,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankReconciliation $bankReconciliation): JsonResponse
    {
        if ($bankReconciliation->status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete approved reconciliation',
            ], 422);
        }

        $bankReconciliation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bank reconciliation deleted successfully',
        ]);
    }

    /**
     * Match an item in the reconciliation.
     */
    public function matchItem(Request $request, BankReconciliation $bankReconciliation): JsonResponse
    {
        $request->validate([
            'item_type' => 'required|in:outstanding_check,deposit_in_transit,bank_charge,bank_interest,error,other',
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $item = $bankReconciliation->items()->create($request->all());

        // Recalculate balances
        $this->recalculateBalances($bankReconciliation);

        return response()->json([
            'success' => true,
            'message' => 'Reconciliation item added successfully',
            'data' => $item,
        ], 201);
    }

    /**
     * Unmatch an item in the reconciliation.
     */
    public function unmatchItem(Request $request, BankReconciliation $bankReconciliation): JsonResponse
    {
        $request->validate([
            'item_id' => 'required|exists:reconciliation_items,id',
        ]);

        $item = ReconciliationItem::find($request->item_id);

        if ($item->bank_reconciliation_id !== $bankReconciliation->id) {
            return response()->json([
                'success' => false,
                'message' => 'Item does not belong to this reconciliation',
            ], 422);
        }

        $item->delete();

        // Recalculate balances
        $this->recalculateBalances($bankReconciliation);

        return response()->json([
            'success' => true,
            'message' => 'Reconciliation item removed successfully',
        ]);
    }

    /**
     * Complete the reconciliation.
     */
    public function complete(BankReconciliation $bankReconciliation): JsonResponse
    {
        if ($bankReconciliation->status !== 'draft' && $bankReconciliation->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Reconciliation cannot be completed from current status',
            ], 422);
        }

        $bankReconciliation->update(['status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => 'Bank reconciliation completed successfully',
            'data' => $bankReconciliation,
        ]);
    }

    /**
     * Approve the reconciliation.
     */
    public function approve(BankReconciliation $bankReconciliation): JsonResponse
    {
        if ($bankReconciliation->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Only completed reconciliations can be approved',
            ], 422);
        }

        $bankReconciliation->update([
            'status' => 'approved',
            'approved_by_id' => auth()->id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bank reconciliation approved successfully',
            'data' => $bankReconciliation,
        ]);
    }

    /**
     * Recalculate adjusted balances and difference.
     */
    private function recalculateBalances(BankReconciliation $reconciliation): void
    {
        $items = $reconciliation->items;

        $adjustedBookBalance = $reconciliation->book_balance;
        $adjustedBankBalance = $reconciliation->bank_balance;

        foreach ($items as $item) {
            switch ($item->item_type) {
                case 'outstanding_check':
                    $adjustedBankBalance -= $item->amount;
                    break;
                case 'deposit_in_transit':
                    $adjustedBankBalance += $item->amount;
                    break;
                case 'bank_charge':
                    $adjustedBookBalance -= $item->amount;
                    break;
                case 'bank_interest':
                    $adjustedBookBalance += $item->amount;
                    break;
            }
        }

        $reconciliation->update([
            'adjusted_book_balance' => $adjustedBookBalance,
            'adjusted_bank_balance' => $adjustedBankBalance,
            'difference' => $adjustedBookBalance - $adjustedBankBalance,
        ]);
    }
}
