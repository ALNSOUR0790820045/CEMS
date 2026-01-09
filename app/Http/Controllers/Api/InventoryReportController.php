<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class InventoryReportController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Get inventory valuation report
     * GET /api/inventory/reports/valuation
     */
    public function valuation(Request $request): JsonResponse
    {
        try {
            $companyId = auth()->user()->company_id;
            $warehouseId = $request->warehouse_id;

            $report = $this->inventoryService->getValuationReport($companyId, $warehouseId);

            return response()->json([
                'success' => true,
                'data' => $report,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get stock status report
     * GET /api/inventory/reports/stock-status
     */
    public function stockStatus(Request $request): JsonResponse
    {
        try {
            $filters = [
                'company_id' => auth()->user()->company_id,
                'warehouse_id' => $request->warehouse_id,
                'per_page' => $request->per_page ?? 100,
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
     * Get movement history report
     * GET /api/inventory/reports/movement
     */
    public function movement(Request $request): JsonResponse
    {
        try {
            $filters = [
                'company_id' => auth()->user()->company_id,
                'material_id' => $request->material_id,
                'warehouse_id' => $request->warehouse_id,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'per_page' => $request->per_page ?? 50,
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

    /**
     * Get low stock alert report
     * GET /api/inventory/reports/low-stock
     */
    public function lowStock(Request $request): JsonResponse
    {
        try {
            $filters = [
                'company_id' => auth()->user()->company_id,
                'warehouse_id' => $request->warehouse_id,
                'low_stock' => true,
                'per_page' => $request->per_page ?? 50,
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
}
