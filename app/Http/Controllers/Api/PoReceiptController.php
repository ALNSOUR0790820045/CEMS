<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PoReceipt;
use App\Models\PoReceiptItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PoReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PoReceipt::with(['purchaseOrder.vendor', 'warehouse', 'receivedBy', 'items'])
            ->where('company_id', auth()->user()->company_id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('purchase_order_id')) {
            $query->where('purchase_order_id', $request->purchase_order_id);
        }

        if ($request->has('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $receipts = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($receipts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'receipt_date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'delivery_note_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.po_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_received' => 'required|numeric|min:0.01',
            'items.*.batch_number' => 'nullable|string',
            'items.*.expiry_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $purchaseOrder = PurchaseOrder::findOrFail($request->purchase_order_id);

            // Validate quantities
            foreach ($request->items as $itemData) {
                $poItem = PurchaseOrderItem::findOrFail($itemData['po_item_id']);
                $alreadyReceived = $poItem->quantity_received ?? 0;
                $newTotal = $alreadyReceived + $itemData['quantity_received'];

                if ($newTotal > $poItem->quantity) {
                    return response()->json([
                        'error' => "Cannot receive more than ordered quantity for item: {$poItem->description}"
                    ], 400);
                }
            }

            // Generate receipt number
            $receiptNumber = $this->generateReceiptNumber();

            $receipt = PoReceipt::create([
                'receipt_number' => $receiptNumber,
                'purchase_order_id' => $request->purchase_order_id,
                'receipt_date' => $request->receipt_date,
                'received_by_id' => auth()->id(),
                'warehouse_id' => $request->warehouse_id,
                'delivery_note_number' => $request->delivery_note_number,
                'status' => 'pending_inspection',
                'notes' => $request->notes,
                'company_id' => auth()->user()->company_id,
            ]);

            foreach ($request->items as $itemData) {
                PoReceiptItem::create([
                    'po_receipt_id' => $receipt->id,
                    'po_item_id' => $itemData['po_item_id'],
                    'quantity_received' => $itemData['quantity_received'],
                    'quantity_accepted' => 0,
                    'quantity_rejected' => 0,
                    'batch_number' => $itemData['batch_number'] ?? null,
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                    'inspection_notes' => $itemData['inspection_notes'] ?? null,
                ]);

                // Update PO item received quantity
                $poItem = PurchaseOrderItem::findOrFail($itemData['po_item_id']);
                $poItem->quantity_received = ($poItem->quantity_received ?? 0) + $itemData['quantity_received'];
                $poItem->save();
            }

            // Update PO status
            $this->updatePurchaseOrderStatus($purchaseOrder);

            DB::commit();

            return response()->json([
                'message' => 'Receipt created successfully',
                'data' => $receipt->load(['items', 'purchaseOrder', 'warehouse'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create receipt: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $receipt = PoReceipt::with([
            'purchaseOrder.vendor',
            'warehouse',
            'receivedBy',
            'items.purchaseOrderItem.material'
        ])->findOrFail($id);

        return response()->json($receipt);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $receipt = PoReceipt::findOrFail($id);

        if ($receipt->status !== 'pending_inspection') {
            return response()->json(['error' => 'Cannot update receipt in current status'], 400);
        }

        $validator = Validator::make($request->all(), [
            'receipt_date' => 'sometimes|date',
            'delivery_note_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $receipt->update($request->all());

        return response()->json([
            'message' => 'Receipt updated successfully',
            'data' => $receipt
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $receipt = PoReceipt::findOrFail($id);

        if ($receipt->status !== 'pending_inspection') {
            return response()->json(['error' => 'Cannot delete receipt in current status'], 400);
        }

        DB::beginTransaction();
        try {
            // Reverse quantity updates
            foreach ($receipt->items as $item) {
                $poItem = $item->purchaseOrderItem;
                $poItem->quantity_received = ($poItem->quantity_received ?? 0) - $item->quantity_received;
                $poItem->save();
            }

            $receipt->delete();

            // Update PO status
            $this->updatePurchaseOrderStatus($receipt->purchaseOrder);

            DB::commit();

            return response()->json(['message' => 'Receipt deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to delete receipt: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Inspect receipt
     */
    public function inspect(Request $request, string $id)
    {
        $receipt = PoReceipt::findOrFail($id);

        if ($receipt->status !== 'pending_inspection') {
            return response()->json(['error' => 'Receipt already inspected'], 400);
        }

        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.id' => 'required|exists:po_receipt_items,id',
            'items.*.quantity_accepted' => 'required|numeric|min:0',
            'items.*.quantity_rejected' => 'required|numeric|min:0',
            'items.*.rejection_reason' => 'nullable|string',
            'items.*.inspection_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($request->items as $itemData) {
                $receiptItem = PoReceiptItem::findOrFail($itemData['id']);
                
                if (($itemData['quantity_accepted'] + $itemData['quantity_rejected']) != $receiptItem->quantity_received) {
                    return response()->json([
                        'error' => 'Accepted + Rejected must equal received quantity'
                    ], 400);
                }

                $receiptItem->update([
                    'quantity_accepted' => $itemData['quantity_accepted'],
                    'quantity_rejected' => $itemData['quantity_rejected'],
                    'rejection_reason' => $itemData['rejection_reason'] ?? null,
                    'inspection_notes' => $itemData['inspection_notes'] ?? null,
                ]);
            }

            $receipt->update(['status' => 'inspected']);

            DB::commit();

            return response()->json([
                'message' => 'Receipt inspected successfully',
                'data' => $receipt->load('items')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to inspect receipt: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Accept receipt
     */
    public function accept(Request $request, string $id)
    {
        $receipt = PoReceipt::findOrFail($id);

        if ($receipt->status !== 'inspected') {
            return response()->json(['error' => 'Receipt must be inspected first'], 400);
        }

        $receipt->update(['status' => 'accepted']);

        return response()->json([
            'message' => 'Receipt accepted successfully',
            'data' => $receipt
        ]);
    }

    /**
     * Generate receipt number
     */
    private function generateReceiptNumber()
    {
        $year = date('Y');
        $lastReceipt = PoReceipt::where('receipt_number', 'like', "RCV-{$year}-%")
            ->orderBy('receipt_number', 'desc')
            ->first();

        if ($lastReceipt) {
            $lastNumber = intval(substr($lastReceipt->receipt_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "RCV-{$year}-{$newNumber}";
    }

    /**
     * Update purchase order status based on received quantities
     */
    private function updatePurchaseOrderStatus($purchaseOrder)
    {
        $allReceived = true;
        $anyReceived = false;

        foreach ($purchaseOrder->items as $item) {
            if (($item->quantity_received ?? 0) < $item->quantity) {
                $allReceived = false;
            }
            if (($item->quantity_received ?? 0) > 0) {
                $anyReceived = true;
            }
        }

        if ($allReceived) {
            $purchaseOrder->update(['status' => 'received']);
        } elseif ($anyReceived) {
            $purchaseOrder->update(['status' => 'partially_received']);
        }
    }
}
