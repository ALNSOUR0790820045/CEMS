<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class InventoryController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Get inventory balance
     * GET /api/inventory/balance
     */
    public function getBalance(Request $request): JsonResponse
    {
        try {
            $filters = [
                'company_id' => auth()->user()->company_id,
                'warehouse_id' => $request->warehouse_id,
                'material_id' => $request->material_id,
                'low_stock' => $request->low_stock,
                'per_page' => $request->per_page ?? 15,
            ];

            $balances = $this->inventoryService->getAllBalances($filters);

            return response()->json([
                'success' => true,
                'data' => $balances,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create inventory transaction
     * POST /api/inventory/transactions
     */
    public function createTransaction(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'transaction_type' => 'required|in:receipt,issue,adjustment,return',
                'transaction_date' => 'required|date',
                'material_id' => 'required|exists:materials,id',
                'warehouse_id' => 'required|exists:warehouses,id',
                'quantity' => 'required|numeric|min:0.01',
                'unit_cost' => 'required|numeric|min:0',
                'project_id' => 'nullable|exists:projects,id',
                'reference_type' => 'nullable|string',
                'reference_id' => 'nullable|integer',
                'notes' => 'nullable|string',
            ]);

            $validated['company_id'] = auth()->user()->company_id;
            $validated['created_by_id'] = auth()->id();

            $transaction = null;

            switch ($validated['transaction_type']) {
                case 'receipt':
                    $transaction = $this->inventoryService->recordReceipt($validated);
                    break;
                case 'issue':
                    $transaction = $this->inventoryService->recordIssue($validated);
                    break;
                case 'adjustment':
                    $transaction = $this->inventoryService->recordAdjustment($validated);
                    break;
                case 'return':
                    $transaction = $this->inventoryService->recordReceipt($validated);
                    break;
            }

            return response()->json([
                'success' => true,
                'data' => $transaction->load(['material', 'warehouse', 'project']),
                'message' => 'Transaction recorded successfully',
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get transaction history
     * GET /api/inventory/transactions
     */
    public function getTransactions(Request $request): JsonResponse
    {
        try {
            $filters = [
                'company_id' => auth()->user()->company_id,
                'material_id' => $request->material_id,
                'warehouse_id' => $request->warehouse_id,
                'transaction_type' => $request->transaction_type,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'per_page' => $request->per_page ?? 15,
            ];

            $transactions = $this->inventoryService->getTransactionHistory($filters);

            return response()->json([
                'success' => true,
                'data' => $transactions,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
