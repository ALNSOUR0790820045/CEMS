<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['vendor', 'project', 'items.material', 'creator', 'approvedBy'])
            ->where('company_id', auth()->user()->company_id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('from_date')) {
            $query->where('po_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('po_date', '<=', $request->to_date);
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
            'project_id' => 'nullable|exists:projects,id',
            'purchase_requisition_id' => 'nullable|exists:purchase_requisitions,id',
            'delivery_address' => 'nullable|string',
            'delivery_date' => 'nullable|date',
            'currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'nullable|exists:materials,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_id' => 'nullable|exists:units,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.tax_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // Generate PO number
            $poNumber = $this->generatePoNumber();

            // Calculate totals
            $subtotal = 0;
            $totalDiscount = 0;
            $totalTax = 0;

            foreach ($request->items as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $discountAmount = $itemSubtotal * ($item['discount_percentage'] ?? 0) / 100;
                $afterDiscount = $itemSubtotal - $discountAmount;
                $taxAmount = $afterDiscount * ($item['tax_percentage'] ?? 0) / 100;
                
                $subtotal += $itemSubtotal;
                $totalDiscount += $discountAmount;
                $totalTax += $taxAmount;
            }

            $totalAmount = $subtotal - $totalDiscount + $totalTax;

            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $poNumber,
                'po_date' => $request->po_date,
                'vendor_id' => $request->vendor_id,
                'purchase_requisition_id' => $request->purchase_requisition_id,
                'project_id' => $request->project_id,
                'delivery_address' => $request->delivery_address,
                'delivery_date' => $request->delivery_date,
                'currency_id' => $request->currency_id,
                'exchange_rate' => $request->exchange_rate ?? 1,
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount,
                'tax_amount' => $totalTax,
                'total_amount' => $totalAmount,
                'status' => 'draft',
                'notes' => $request->notes,
                'terms_and_conditions' => $request->terms_and_conditions,
                'company_id' => auth()->user()->company_id,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $itemData) {
                $itemSubtotal = $itemData['quantity'] * $itemData['unit_price'];
                $discountPercentage = $itemData['discount_percentage'] ?? 0;
                $taxPercentage = $itemData['tax_percentage'] ?? 0;
                
                $discountAmount = $itemSubtotal * $discountPercentage / 100;
                $afterDiscount = $itemSubtotal - $discountAmount;
                $taxAmount = $afterDiscount * $taxPercentage / 100;
                $totalPrice = $afterDiscount + $taxAmount;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'material_id' => $itemData['material_id'] ?? null,
                    'description' => $itemData['description'],
                    'specifications' => $itemData['specifications'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'unit_id' => $itemData['unit_id'] ?? null,
                    'unit_price' => $itemData['unit_price'],
                    'discount_percentage' => $discountPercentage,
                    'discount_amount' => $discountAmount,
                    'tax_percentage' => $taxPercentage,
                    'tax_amount' => $taxAmount,
                    'total_price' => $totalPrice,
                    'delivery_date' => $itemData['delivery_date'] ?? null,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Purchase order created successfully',
                'data' => $purchaseOrder->load(['items', 'vendor', 'project'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create purchase order: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $purchaseOrder = PurchaseOrder::with([
            'vendor', 
            'project', 
            'items.material', 
            'items.unit',
            'creator', 
            'approvedBy',
            'receipts.items',
            'amendments'
        ])->findOrFail($id);

        return response()->json($purchaseOrder);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if (!in_array($purchaseOrder->status, ['draft', 'pending_approval'])) {
            return response()->json(['error' => 'Cannot update purchase order in current status'], 400);
        }

        $validator = Validator::make($request->all(), [
            'po_date' => 'sometimes|date',
            'vendor_id' => 'sometimes|exists:vendors,id',
            'delivery_address' => 'nullable|string',
            'delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'sometimes|array|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $purchaseOrder->update($request->except('items'));

            if ($request->has('items')) {
                // Delete existing items
                $purchaseOrder->items()->delete();

                // Recalculate totals
                $subtotal = 0;
                $totalDiscount = 0;
                $totalTax = 0;

                foreach ($request->items as $itemData) {
                    $itemSubtotal = $itemData['quantity'] * $itemData['unit_price'];
                    $discountPercentage = $itemData['discount_percentage'] ?? 0;
                    $taxPercentage = $itemData['tax_percentage'] ?? 0;
                    
                    $discountAmount = $itemSubtotal * $discountPercentage / 100;
                    $afterDiscount = $itemSubtotal - $discountAmount;
                    $taxAmount = $afterDiscount * $taxPercentage / 100;
                    $totalPrice = $afterDiscount + $taxAmount;

                    PurchaseOrderItem::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'material_id' => $itemData['material_id'] ?? null,
                        'description' => $itemData['description'],
                        'specifications' => $itemData['specifications'] ?? null,
                        'quantity' => $itemData['quantity'],
                        'unit_id' => $itemData['unit_id'] ?? null,
                        'unit_price' => $itemData['unit_price'],
                        'discount_percentage' => $discountPercentage,
                        'discount_amount' => $discountAmount,
                        'tax_percentage' => $taxPercentage,
                        'tax_amount' => $taxAmount,
                        'total_price' => $totalPrice,
                        'notes' => $itemData['notes'] ?? null,
                    ]);

                    $subtotal += $itemSubtotal;
                    $totalDiscount += $discountAmount;
                    $totalTax += $taxAmount;
                }

                $totalAmount = $subtotal - $totalDiscount + $totalTax;

                $purchaseOrder->update([
                    'subtotal' => $subtotal,
                    'discount_amount' => $totalDiscount,
                    'tax_amount' => $totalTax,
                    'total_amount' => $totalAmount,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Purchase order updated successfully',
                'data' => $purchaseOrder->load(['items', 'vendor', 'project'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update purchase order: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if ($purchaseOrder->status !== 'draft') {
            return response()->json(['error' => 'Cannot delete purchase order in current status'], 400);
        }

        $purchaseOrder->delete();

        return response()->json(['message' => 'Purchase order deleted successfully']);
    }

    /**
     * Approve purchase order
     */
    public function approve(Request $request, string $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if (!in_array($purchaseOrder->status, ['draft', 'pending_approval'])) {
            return response()->json(['error' => 'Cannot approve purchase order in current status'], 400);
        }

        $purchaseOrder->update([
            'status' => 'approved',
            'approved_by_id' => auth()->id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Purchase order approved successfully',
            'data' => $purchaseOrder
        ]);
    }

    /**
     * Reject purchase order
     */
    public function reject(Request $request, string $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if (!in_array($purchaseOrder->status, ['draft', 'pending_approval'])) {
            return response()->json(['error' => 'Cannot reject purchase order in current status'], 400);
        }

        $purchaseOrder->update([
            'status' => 'cancelled',
        ]);

        return response()->json([
            'message' => 'Purchase order rejected successfully',
            'data' => $purchaseOrder
        ]);
    }

    /**
     * Send purchase order to vendor
     */
    public function send(Request $request, string $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if ($purchaseOrder->status !== 'approved') {
            return response()->json(['error' => 'Purchase order must be approved before sending'], 400);
        }

        $purchaseOrder->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return response()->json([
            'message' => 'Purchase order sent successfully',
            'data' => $purchaseOrder
        ]);
    }

    /**
     * Cancel purchase order
     */
    public function cancel(Request $request, string $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if (in_array($purchaseOrder->status, ['received', 'closed', 'cancelled'])) {
            return response()->json(['error' => 'Cannot cancel purchase order in current status'], 400);
        }

        $purchaseOrder->update([
            'status' => 'cancelled',
        ]);

        return response()->json([
            'message' => 'Purchase order cancelled successfully',
            'data' => $purchaseOrder
        ]);
    }

    /**
     * Close purchase order
     */
    public function close(Request $request, string $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if (!in_array($purchaseOrder->status, ['sent', 'partially_received', 'received'])) {
            return response()->json(['error' => 'Cannot close purchase order in current status'], 400);
        }

        $purchaseOrder->update([
            'status' => 'closed',
        ]);

        return response()->json([
            'message' => 'Purchase order closed successfully',
            'data' => $purchaseOrder
        ]);
    }

    /**
     * Generate PO number
     */
    private function generatePoNumber()
    {
        $year = date('Y');
        $lastPo = PurchaseOrder::where('po_number', 'like', "PO-{$year}-%")
            ->orderBy('po_number', 'desc')
            ->first();

        if ($lastPo) {
            $lastNumber = intval(substr($lastPo->po_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "PO-{$year}-{$newNumber}";
    }

    /**
     * Print purchase order
     */
    public function print(string $id)
    {
        $purchaseOrder = PurchaseOrder::with(['vendor', 'project', 'items.material', 'items.unit'])
            ->findOrFail($id);

        return response()->json([
            'message' => 'Purchase order ready for printing',
            'data' => $purchaseOrder
        ]);
    }

    /**
     * Generate PDF
     */
    public function generatePdf(string $id)
    {
        $purchaseOrder = PurchaseOrder::with(['vendor', 'project', 'items.material', 'items.unit'])
            ->findOrFail($id);

        return response()->json([
            'message' => 'PDF generation not implemented yet',
            'data' => $purchaseOrder
        ]);
    }

    /**
     * Create from Purchase Requisition
     */
    public function createFromPR(Request $request, string $prId)
    {
        return response()->json([
            'message' => 'Create from PR not implemented yet - PR ID: ' . $prId
        ]);
    }
}
