<?php

namespace App\Services;

use App\Models\InventoryBalance;
use App\Models\InventoryTransaction;
use App\Models\Material;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * Record a receipt transaction (stock in)
     */
    public function recordReceipt(array $data): InventoryTransaction
    {
        return DB::transaction(function () use ($data) {
            $transaction = InventoryTransaction::create([
                'transaction_date' => $data['transaction_date'],
                'transaction_type' => 'receipt',
                'material_id' => $data['material_id'],
                'warehouse_id' => $data['warehouse_id'],
                'quantity' => abs($data['quantity']),
                'unit_cost' => $data['unit_cost'],
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'company_id' => $data['company_id'],
                'created_by_id' => $data['created_by_id'],
            ]);

            $this->updateInventoryBalance(
                $data['material_id'],
                $data['warehouse_id'],
                abs($data['quantity']),
                $data['unit_cost'],
                $data['transaction_date'],
                $data['company_id']
            );

            return $transaction;
        });
    }

    /**
     * Record an issue transaction (stock out)
     */
    public function recordIssue(array $data): InventoryTransaction
    {
        return DB::transaction(function () use ($data) {
            // Check available quantity
            $balance = InventoryBalance::where('material_id', $data['material_id'])
                ->where('warehouse_id', $data['warehouse_id'])
                ->first();

            if (!$balance || $balance->quantity_available < abs($data['quantity'])) {
                throw new Exception('Insufficient stock available');
            }

            $transaction = InventoryTransaction::create([
                'transaction_date' => $data['transaction_date'],
                'transaction_type' => 'issue',
                'material_id' => $data['material_id'],
                'warehouse_id' => $data['warehouse_id'],
                'quantity' => -abs($data['quantity']),
                'unit_cost' => $data['unit_cost'] ?? $balance->average_cost ?? 0,
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'project_id' => $data['project_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'company_id' => $data['company_id'],
                'created_by_id' => $data['created_by_id'],
            ]);

            $this->updateInventoryBalance(
                $data['material_id'],
                $data['warehouse_id'],
                -abs($data['quantity']),
                $transaction->unit_cost,
                $data['transaction_date'],
                $data['company_id']
            );

            return $transaction;
        });
    }

    /**
     * Record an adjustment transaction
     */
    public function recordAdjustment(array $data): InventoryTransaction
    {
        return DB::transaction(function () use ($data) {
            $transaction = InventoryTransaction::create([
                'transaction_date' => $data['transaction_date'],
                'transaction_type' => 'adjustment',
                'material_id' => $data['material_id'],
                'warehouse_id' => $data['warehouse_id'],
                'quantity' => $data['quantity'],
                'unit_cost' => $data['unit_cost'],
                'notes' => $data['notes'] ?? null,
                'company_id' => $data['company_id'],
                'created_by_id' => $data['created_by_id'],
            ]);

            $this->updateInventoryBalance(
                $data['material_id'],
                $data['warehouse_id'],
                $data['quantity'],
                $data['unit_cost'],
                $data['transaction_date'],
                $data['company_id']
            );

            return $transaction;
        });
    }

    /**
     * Update inventory balance using average cost method
     */
    protected function updateInventoryBalance(
        int $materialId,
        int $warehouseId,
        float $quantity,
        float $unitCost,
        string $transactionDate,
        int $companyId
    ): void {
        $balance = InventoryBalance::firstOrNew([
            'material_id' => $materialId,
            'warehouse_id' => $warehouseId,
        ]);

        if (!$balance->exists) {
            $balance->company_id = $companyId;
            $balance->quantity_on_hand = 0;
            $balance->quantity_reserved = 0;
            $balance->average_cost = 0;
        }

        $oldQuantity = $balance->quantity_on_hand;
        $oldValue = $oldQuantity * ($balance->average_cost ?? 0);

        if ($quantity > 0) {
            // Receipt or positive adjustment
            $newQuantity = $oldQuantity + $quantity;
            $newValue = $oldValue + ($quantity * $unitCost);
            $balance->average_cost = $newQuantity > 0 ? $newValue / $newQuantity : 0;
        } else {
            // Issue or negative adjustment
            $newQuantity = $oldQuantity + $quantity;
            // Average cost remains the same on issues
        }

        $balance->quantity_on_hand = $newQuantity;
        $balance->last_cost = $unitCost;
        $balance->last_transaction_date = $transactionDate;
        $balance->save();
    }

    /**
     * Get inventory balance for a specific material and warehouse
     */
    public function getBalance(int $materialId, int $warehouseId)
    {
        return InventoryBalance::with(['material', 'warehouse'])
            ->where('material_id', $materialId)
            ->where('warehouse_id', $warehouseId)
            ->first();
    }

    /**
     * Get all inventory balances with filters
     */
    public function getAllBalances(array $filters = [])
    {
        $query = InventoryBalance::with(['material', 'warehouse'])
            ->where('company_id', $filters['company_id']);

        if (!empty($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        if (!empty($filters['material_id'])) {
            $query->where('material_id', $filters['material_id']);
        }

        if (!empty($filters['low_stock'])) {
            $query->whereHas('material', function ($q) {
                $q->whereRaw('inventory_balances.quantity_on_hand <= materials.reorder_level');
            });
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get transaction history
     */
    public function getTransactionHistory(array $filters = [])
    {
        $query = InventoryTransaction::with([
            'material',
            'warehouse',
            'project',
            'createdBy'
        ])->where('company_id', $filters['company_id']);

        if (!empty($filters['material_id'])) {
            $query->where('material_id', $filters['material_id']);
        }

        if (!empty($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        if (!empty($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('transaction_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('transaction_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get valuation report
     */
    public function getValuationReport(int $companyId, ?int $warehouseId = null)
    {
        $query = InventoryBalance::with(['material', 'warehouse'])
            ->where('company_id', $companyId)
            ->where('quantity_on_hand', '>', 0);

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        $balances = $query->get();

        return [
            'items' => $balances,
            'total_value' => $balances->sum(function ($balance) {
                return $balance->quantity_on_hand * ($balance->average_cost ?? 0);
            }),
            'total_quantity' => $balances->sum('quantity_on_hand'),
        ];
    }
}
