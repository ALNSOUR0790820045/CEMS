<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGRNRequest;
use App\Http\Requests\UpdateGRNRequest;
use App\Http\Requests\InspectGRNRequest;
use App\Models\GRN;
use App\Models\GRNItem;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GRNController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = GRN::with(['vendor', 'warehouse', 'purchaseOrder', 'receivedBy', 'items.material'])
            ->where('company_id', auth()->user()->company_id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->has('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $grns = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($grns);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGRNRequest $request)
    {
        try {
            DB::beginTransaction();

            $grn = GRN::create([
                'grn_date' => $request->grn_date,
                'purchase_order_id' => $request->purchase_order_id,
                'vendor_id' => $request->vendor_id,
                'warehouse_id' => $request->warehouse_id,
                'project_id' => $request->project_id,
                'delivery_note_number' => $request->delivery_note_number,
                'vehicle_number' => $request->vehicle_number,
                'driver_name' => $request->driver_name,
                'status' => 'received',
                'notes' => $request->notes,
                'received_by_id' => auth()->id(),
                'company_id' => auth()->user()->company_id,
            ]);

            foreach ($request->items as $itemData) {
                GRNItem::create([
                    'grn_id' => $grn->id,
                    'purchase_order_item_id' => $itemData['purchase_order_item_id'] ?? null,
                    'material_id' => $itemData['material_id'],
                    'ordered_quantity' => $itemData['ordered_quantity'] ?? null,
                    'received_quantity' => $itemData['received_quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'batch_number' => $itemData['batch_number'] ?? null,
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            $grn->calculateTotalValue();

            DB::commit();

            return response()->json([
                'message' => 'GRN created successfully',
                'data' => $grn->load(['items.material', 'vendor', 'warehouse'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating GRN',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $grn = GRN::with([
            'vendor',
            'warehouse',
            'project',
            'purchaseOrder',
            'receivedBy',
            'inspectedBy',
            'items.material',
            'items.purchaseOrderItem'
        ])->findOrFail($id);

        return response()->json($grn);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGRNRequest $request, string $id)
    {
        $grn = GRN::findOrFail($id);

        if (!in_array($grn->status, ['draft', 'received'])) {
            return response()->json([
                'message' => 'Cannot update GRN in current status'
            ], 422);
        }

        $grn->update($request->validated());

        return response()->json([
            'message' => 'GRN updated successfully',
            'data' => $grn->load(['items.material', 'vendor', 'warehouse'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $grn = GRN::findOrFail($id);

        if (!in_array($grn->status, ['draft'])) {
            return response()->json([
                'message' => 'Cannot delete GRN in current status'
            ], 422);
        }

        $grn->delete();

        return response()->json([
            'message' => 'GRN deleted successfully'
        ]);
    }

    /**
     * Inspect GRN items
     */
    public function inspect(InspectGRNRequest $request, string $id)
    {
        try {
            DB::beginTransaction();

            $grn = GRN::with('items')->findOrFail($id);

            if ($grn->status !== 'received') {
                return response()->json([
                    'message' => 'GRN must be in received status for inspection'
                ], 422);
            }

            $grn->update([
                'inspection_notes' => $request->inspection_notes,
                'inspected_by_id' => auth()->id(),
                'status' => 'inspected',
            ]);

            foreach ($request->items as $itemData) {
                $grnItem = GRNItem::findOrFail($itemData['grn_item_id']);
                
                $grnItem->update([
                    'accepted_quantity' => $itemData['accepted_quantity'],
                    'rejected_quantity' => $itemData['rejected_quantity'],
                    'inspection_status' => $itemData['inspection_status'],
                    'rejection_reason' => $itemData['rejection_reason'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'GRN inspected successfully',
                'data' => $grn->fresh(['items.material', 'vendor', 'warehouse'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error inspecting GRN',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Accept GRN and create inventory transactions
     */
    public function accept(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $grn = GRN::with('items.material')->findOrFail($id);

            if ($grn->status !== 'inspected') {
                return response()->json([
                    'message' => 'GRN must be inspected before acceptance'
                ], 422);
            }

            // Create inventory transactions for accepted items
            foreach ($grn->items as $item) {
                if ($item->accepted_quantity > 0) {
                    InventoryTransaction::create([
                        'material_id' => $item->material_id,
                        'warehouse_id' => $grn->warehouse_id,
                        'transaction_type' => 'in',
                        'reference_type' => 'GRN',
                        'reference_id' => $grn->id,
                        'quantity' => $item->accepted_quantity,
                        'unit_price' => $item->unit_price,
                        'batch_number' => $item->batch_number,
                        'expiry_date' => $item->expiry_date,
                        'notes' => "GRN acceptance for GRN #{$grn->grn_number}",
                        'company_id' => $grn->company_id,
                        'created_by' => auth()->id(),
                    ]);
                }
            }

            // Determine final status
            $hasRejections = $grn->items()->where('rejected_quantity', '>', 0)->exists();
            $hasAcceptances = $grn->items()->where('accepted_quantity', '>', 0)->exists();

            if ($hasAcceptances && $hasRejections) {
                $status = 'partial';
            } elseif ($hasRejections) {
                $status = 'rejected';
            } else {
                $status = 'accepted';
            }

            $grn->update(['status' => $status]);

            DB::commit();

            return response()->json([
                'message' => 'GRN accepted and inventory updated successfully',
                'data' => $grn->fresh(['items.material', 'vendor', 'warehouse'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error accepting GRN',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get GRNs pending inspection
     */
    public function pendingInspection()
    {
        $grns = GRN::with(['vendor', 'warehouse', 'receivedBy', 'items.material'])
            ->where('company_id', auth()->user()->company_id)
            ->where('status', 'received')
            ->latest()
            ->get();

        return response()->json($grns);
    }
}
