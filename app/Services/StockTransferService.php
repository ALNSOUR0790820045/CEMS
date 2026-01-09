<?php

namespace App\Services;

use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class StockTransferService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Create a new stock transfer
     */
    public function create(array $data): StockTransfer
    {
        return DB::transaction(function () use ($data) {
            $transfer = StockTransfer::create([
                'transfer_date' => $data['transfer_date'],
                'from_warehouse_id' => $data['from_warehouse_id'],
                'to_warehouse_id' => $data['to_warehouse_id'],
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
                'company_id' => $data['company_id'],
                'created_by_id' => $data['created_by_id'],
            ]);

            foreach ($data['items'] as $item) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'material_id' => $item['material_id'],
                    'requested_quantity' => $item['requested_quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            return $transfer->load('items.material', 'fromWarehouse', 'toWarehouse');
        });
    }

    /**
     * Approve a stock transfer
     */
    public function approve(int $transferId, int $approvedById): StockTransfer
    {
        return DB::transaction(function () use ($transferId, $approvedById) {
            $transfer = StockTransfer::with('items')->findOrFail($transferId);

            if ($transfer->status !== 'pending') {
                throw new Exception('Only pending transfers can be approved');
            }

            $transfer->update([
                'status' => 'approved',
                'approved_by_id' => $approvedById,
            ]);

            // Set transferred quantities to requested quantities
            foreach ($transfer->items as $item) {
                $item->update([
                    'transferred_quantity' => $item->requested_quantity,
                ]);
            }

            return $transfer->fresh(['items.material', 'fromWarehouse', 'toWarehouse', 'approvedBy']);
        });
    }

    /**
     * Mark transfer as in transit
     */
    public function markInTransit(int $transferId): StockTransfer
    {
        $transfer = StockTransfer::findOrFail($transferId);

        if ($transfer->status !== 'approved') {
            throw new Exception('Only approved transfers can be marked as in transit');
        }

        $transfer->update(['status' => 'in_transit']);

        return $transfer->fresh(['items.material', 'fromWarehouse', 'toWarehouse']);
    }

    /**
     * Receive a stock transfer and update inventory
     */
    public function receive(int $transferId, int $receivedById, array $receivedQuantities): StockTransfer
    {
        return DB::transaction(function () use ($transferId, $receivedById, $receivedQuantities) {
            $transfer = StockTransfer::with('items')->findOrFail($transferId);

            if (!in_array($transfer->status, ['approved', 'in_transit'])) {
                throw new Exception('Only approved or in-transit transfers can be received');
            }

            // Update received quantities and create inventory transactions
            foreach ($transfer->items as $item) {
                $receivedQty = $receivedQuantities[$item->id] ?? $item->transferred_quantity;
                
                $item->update([
                    'received_quantity' => $receivedQty,
                ]);

                if ($receivedQty > 0) {
                    // Issue from source warehouse
                    $this->inventoryService->recordIssue([
                        'transaction_date' => now()->format('Y-m-d'),
                        'material_id' => $item->material_id,
                        'warehouse_id' => $transfer->from_warehouse_id,
                        'quantity' => $receivedQty,
                        'unit_cost' => $item->unit_cost,
                        'reference_type' => 'stock_transfer',
                        'reference_id' => $transfer->id,
                        'notes' => "Transfer to warehouse: {$transfer->toWarehouse->name}",
                        'company_id' => $transfer->company_id,
                        'created_by_id' => $receivedById,
                    ]);

                    // Receipt to destination warehouse
                    $this->inventoryService->recordReceipt([
                        'transaction_date' => now()->format('Y-m-d'),
                        'material_id' => $item->material_id,
                        'warehouse_id' => $transfer->to_warehouse_id,
                        'quantity' => $receivedQty,
                        'unit_cost' => $item->unit_cost,
                        'reference_type' => 'stock_transfer',
                        'reference_id' => $transfer->id,
                        'notes' => "Transfer from warehouse: {$transfer->fromWarehouse->name}",
                        'company_id' => $transfer->company_id,
                        'created_by_id' => $receivedById,
                    ]);
                }
            }

            $transfer->update([
                'status' => 'completed',
                'received_by_id' => $receivedById,
            ]);

            return $transfer->fresh(['items.material', 'fromWarehouse', 'toWarehouse', 'receivedBy']);
        });
    }

    /**
     * Cancel a stock transfer
     */
    public function cancel(int $transferId, string $reason = null): StockTransfer
    {
        $transfer = StockTransfer::findOrFail($transferId);

        if ($transfer->status === 'completed') {
            throw new Exception('Completed transfers cannot be cancelled');
        }

        $transfer->update([
            'status' => 'cancelled',
            'notes' => $transfer->notes . "\n\nCancellation reason: " . ($reason ?? 'Not specified'),
        ]);

        return $transfer->fresh(['items.material', 'fromWarehouse', 'toWarehouse']);
    }

    /**
     * Get stock transfers with filters
     */
    public function getTransfers(array $filters = [])
    {
        $query = StockTransfer::with([
            'items.material',
            'fromWarehouse',
            'toWarehouse',
            'createdBy',
            'approvedBy',
            'receivedBy'
        ])->where('company_id', $filters['company_id']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['from_warehouse_id'])) {
            $query->where('from_warehouse_id', $filters['from_warehouse_id']);
        }

        if (!empty($filters['to_warehouse_id'])) {
            $query->where('to_warehouse_id', $filters['to_warehouse_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('transfer_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('transfer_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('transfer_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }
}
