<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['vendor', 'project', 'currency', 'createdBy', 'approvedBy']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $purchaseOrders = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($purchaseOrders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'po_date' => 'required|date',
            'vendor_id' => 'required|exists:vendors,id',
            'purchase_requisition_id' => 'nullable|exists:purchase_requisitions,id',
            'project_id' => 'nullable|exists:projects,id',
            'delivery_date' => 'required|date',
            'delivery_location' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.discount_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.delivery_date' => 'nullable|date',
            'items.*.specifications' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();
            
            $purchaseOrder = new PurchaseOrder();
            $purchaseOrder->po_number = $purchaseOrder->generatePoNumber();
            $purchaseOrder->fill($data);
            $purchaseOrder->created_by_id = auth()->id();
            $purchaseOrder->status = 'draft';
            $purchaseOrder->save();

            // Create items
            foreach ($data['items'] as $itemData) {
                $item = new PurchaseOrderItem($itemData);
                $item->purchase_order_id = $purchaseOrder->id;
                $item->save();
            }

            $purchaseOrder->calculateTotals();
            $purchaseOrder->load(['items', 'vendor', 'currency', 'project']);

            DB::commit();

            return response()->json([
                'message' => 'Purchase order created successfully',
                'data' => $purchaseOrder
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating purchase order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load([
            'items.material',
            'items.unit',
            'vendor',
            'project',
            'currency',
            'purchaseRequisition',
            'createdBy',
            'approvedBy'
        ]);

        return response()->json($purchaseOrder);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Only allow updates for draft or submitted status
        if (!in_array($purchaseOrder->status, ['draft', 'submitted'])) {
            return response()->json([
                'message' => 'Cannot update purchase order in current status'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'po_date' => 'sometimes|required|date',
            'vendor_id' => 'sometimes|required|exists:vendors,id',
            'purchase_requisition_id' => 'nullable|exists:purchase_requisitions,id',
            'project_id' => 'nullable|exists:projects,id',
            'delivery_date' => 'sometimes|required|date',
            'delivery_location' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'currency_id' => 'sometimes|required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'sometimes|array|min:1',
            'items.*.id' => 'nullable|exists:purchase_order_items,id',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.discount_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.delivery_date' => 'nullable|date',
            'items.*.specifications' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();
            $purchaseOrder->fill($data);
            $purchaseOrder->save();

            // Update items if provided
            if (isset($data['items'])) {
                $itemIds = [];
                foreach ($data['items'] as $itemData) {
                    if (isset($itemData['id'])) {
                        $item = PurchaseOrderItem::findOrFail($itemData['id']);
                        $item->fill($itemData);
                        $item->save();
                        $itemIds[] = $item->id;
                    } else {
                        $item = new PurchaseOrderItem($itemData);
                        $item->purchase_order_id = $purchaseOrder->id;
                        $item->save();
                        $itemIds[] = $item->id;
                    }
                }

                // Delete items not in the list
                $purchaseOrder->items()->whereNotIn('id', $itemIds)->delete();
            }

            $purchaseOrder->calculateTotals();
            $purchaseOrder->load(['items', 'vendor', 'currency', 'project']);

            DB::commit();

            return response()->json([
                'message' => 'Purchase order updated successfully',
                'data' => $purchaseOrder
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error updating purchase order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        // Only allow deletion for draft status
        if ($purchaseOrder->status !== 'draft') {
            return response()->json([
                'message' => 'Cannot delete purchase order in current status'
            ], 403);
        }

        $purchaseOrder->delete();

        return response()->json([
            'message' => 'Purchase order deleted successfully'
        ]);
    }

    /**
     * Approve a purchase order.
     */
    public function approve(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->canBeApproved()) {
            return response()->json([
                'message' => 'Purchase order cannot be approved in current status'
            ], 403);
        }

        $purchaseOrder->approve(auth()->user());

        return response()->json([
            'message' => 'Purchase order approved successfully',
            'data' => $purchaseOrder->load(['approvedBy'])
        ]);
    }

    /**
     * Send purchase order to vendor.
     */
    public function sendToVendor(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'approved') {
            return response()->json([
                'message' => 'Only approved purchase orders can be sent to vendor'
            ], 403);
        }

        $purchaseOrder->status = 'sent_to_vendor';
        $purchaseOrder->save();

        // TODO: Send email to vendor

        return response()->json([
            'message' => 'Purchase order sent to vendor successfully',
            'data' => $purchaseOrder
        ]);
    }

    /**
     * Amend a purchase order.
     */
    public function amend(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Can amend after approval but before full receiving
        if (!in_array($purchaseOrder->status, ['approved', 'sent_to_vendor', 'acknowledged', 'partially_received'])) {
            return response()->json([
                'message' => 'Purchase order cannot be amended in current status'
            ], 403);
        }

        // For now, we'll create a simple amendment by changing status back to submitted
        // In a full implementation, you'd want to track version history
        $purchaseOrder->status = 'submitted';
        $purchaseOrder->approved_by_id = null;
        $purchaseOrder->approved_at = null;
        $purchaseOrder->save();

        return response()->json([
            'message' => 'Purchase order amended and pending re-approval',
            'data' => $purchaseOrder
        ]);
    }

    /**
     * Get receiving status of a purchase order.
     */
    public function receivingStatus(PurchaseOrder $purchaseOrder)
    {
        $items = $purchaseOrder->items()->with(['material', 'unit'])->get();

        $totalQuantity = $items->sum('quantity');
        $receivedQuantity = $items->sum('received_quantity');
        $remainingQuantity = $items->sum('remaining_quantity');

        $receivingPercentage = $totalQuantity > 0 ? ($receivedQuantity / $totalQuantity) * 100 : 0;

        return response()->json([
            'purchase_order_id' => $purchaseOrder->id,
            'po_number' => $purchaseOrder->po_number,
            'status' => $purchaseOrder->status,
            'total_quantity' => $totalQuantity,
            'received_quantity' => $receivedQuantity,
            'remaining_quantity' => $remainingQuantity,
            'receiving_percentage' => round($receivingPercentage, 2),
            'items' => $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'material' => $item->material->name,
                    'quantity' => $item->quantity,
                    'received_quantity' => $item->received_quantity,
                    'remaining_quantity' => $item->remaining_quantity,
                    'unit' => $item->unit->abbreviation,
                ];
            })
        ]);
    }
}
