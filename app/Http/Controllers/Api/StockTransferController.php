<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StockTransferService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class StockTransferController extends Controller
{
    protected StockTransferService $transferService;

    public function __construct(StockTransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    /**
     * Get all stock transfers
     * GET /api/stock-transfers
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'company_id' => auth()->user()->company_id,
                'status' => $request->status,
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id' => $request->to_warehouse_id,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'per_page' => $request->per_page ?? 15,
            ];

            $transfers = $this->transferService->getTransfers($filters);

            return response()->json([
                'success' => true,
                'data' => $transfers,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new stock transfer
     * POST /api/stock-transfers
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'transfer_date' => 'required|date',
                'from_warehouse_id' => 'required|exists:warehouses,id',
                'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.material_id' => 'required|exists:materials,id',
                'items.*.requested_quantity' => 'required|numeric|min:0.01',
                'items.*.unit_cost' => 'required|numeric|min:0',
                'items.*.notes' => 'nullable|string',
            ]);

            $validated['company_id'] = auth()->user()->company_id;
            $validated['created_by_id'] = auth()->id();

            $transfer = $this->transferService->create($validated);

            return response()->json([
                'success' => true,
                'data' => $transfer,
                'message' => 'Stock transfer created successfully',
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get a specific stock transfer
     * GET /api/stock-transfers/{id}
     */
    public function show(int $id): JsonResponse
    {
        try {
            $transfer = \App\Models\StockTransfer::with([
                'items.material',
                'fromWarehouse',
                'toWarehouse',
                'createdBy',
                'approvedBy',
                'receivedBy'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $transfer,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stock transfer not found',
            ], 404);
        }
    }

    /**
     * Approve a stock transfer
     * POST /api/stock-transfers/{id}/approve
     */
    public function approve(int $id): JsonResponse
    {
        try {
            $transfer = $this->transferService->approve($id, auth()->id());

            return response()->json([
                'success' => true,
                'data' => $transfer,
                'message' => 'Stock transfer approved successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Receive a stock transfer
     * POST /api/stock-transfers/{id}/receive
     */
    public function receive(int $id, Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'received_quantities' => 'nullable|array',
                'received_quantities.*' => 'numeric|min:0',
            ]);

            $transfer = $this->transferService->receive(
                $id,
                auth()->id(),
                $validated['received_quantities'] ?? []
            );

            return response()->json([
                'success' => true,
                'data' => $transfer,
                'message' => 'Stock transfer received successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Cancel a stock transfer
     * POST /api/stock-transfers/{id}/cancel
     */
    public function cancel(int $id, Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'reason' => 'nullable|string',
            ]);

            $transfer = $this->transferService->cancel($id, $validated['reason'] ?? null);

            return response()->json([
                'success' => true,
                'data' => $transfer,
                'message' => 'Stock transfer cancelled successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
