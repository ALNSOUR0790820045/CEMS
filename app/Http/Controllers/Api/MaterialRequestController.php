<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestItem;
use App\Models\InventoryBalance;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class MaterialRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MaterialRequest::with([
            'project',
            'department',
            'requestedBy',
            'approvedBy',
            'items.material',
            'items.unit'
        ])->where('company_id', auth()->user()->company_id);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('requested_by_id')) {
            $query->where('requested_by_id', $request->requested_by_id);
        }

        $materialRequests = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($materialRequests);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_date' => 'required|date',
            'project_id' => 'required|exists:projects,id',
            'department_id' => 'required|exists:departments,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'required_date' => 'nullable|date|after_or_equal:request_date',
            'status' => 'sometimes|in:draft,pending_approval',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.description' => 'nullable|string',
            'items.*.quantity_requested' => 'required|numeric|min:0.01',
            'items.*.unit_id' => 'nullable|exists:units,id',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $materialRequest = MaterialRequest::create([
                'request_number' => MaterialRequest::generateRequestNumber(),
                'request_date' => $validated['request_date'],
                'project_id' => $validated['project_id'],
                'department_id' => $validated['department_id'],
                'requested_by_id' => auth()->id(),
                'priority' => $validated['priority'],
                'required_date' => $validated['required_date'] ?? null,
                'status' => $validated['status'] ?? 'draft',
                'notes' => $validated['notes'] ?? null,
                'company_id' => auth()->user()->company_id,
            ]);

            foreach ($validated['items'] as $itemData) {
                MaterialRequestItem::create([
                    'material_request_id' => $materialRequest->id,
                    'material_id' => $itemData['material_id'],
                    'description' => $itemData['description'] ?? null,
                    'quantity_requested' => $itemData['quantity_requested'],
                    'unit_id' => $itemData['unit_id'] ?? null,
                    'unit_price' => $itemData['unit_price'] ?? null,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Material request created successfully',
                'data' => $materialRequest->load(['items.material', 'items.unit', 'project', 'department', 'requestedBy'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating material request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error creating material request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $materialRequest = MaterialRequest::with([
            'project',
            'department',
            'requestedBy',
            'approvedBy',
            'items.material',
            'items.unit'
        ])->where('company_id', auth()->user()->company_id)
          ->findOrFail($id);

        return response()->json($materialRequest);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $materialRequest = MaterialRequest::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        if (!$materialRequest->canBeEdited()) {
            return response()->json([
                'message' => 'Material request cannot be edited in its current status'
            ], 422);
        }

        $validated = $request->validate([
            'request_date' => 'sometimes|date',
            'project_id' => 'sometimes|exists:projects,id',
            'department_id' => 'sometimes|exists:departments,id',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'required_date' => 'nullable|date',
            'status' => 'sometimes|in:draft,pending_approval',
            'notes' => 'nullable|string',
            'items' => 'sometimes|array|min:1',
            'items.*.id' => 'sometimes|exists:material_request_items,id',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.description' => 'nullable|string',
            'items.*.quantity_requested' => 'required|numeric|min:0.01',
            'items.*.unit_id' => 'nullable|exists:units,id',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $materialRequest->update($validated);

            if (isset($validated['items'])) {
                // Delete existing items not in the update
                $itemIds = collect($validated['items'])->pluck('id')->filter();
                $materialRequest->items()->whereNotIn('id', $itemIds)->delete();

                foreach ($validated['items'] as $itemData) {
                    if (isset($itemData['id'])) {
                        // Update existing item
                        MaterialRequestItem::where('id', $itemData['id'])
                            ->update([
                                'material_id' => $itemData['material_id'],
                                'description' => $itemData['description'] ?? null,
                                'quantity_requested' => $itemData['quantity_requested'],
                                'unit_id' => $itemData['unit_id'] ?? null,
                                'unit_price' => $itemData['unit_price'] ?? null,
                                'notes' => $itemData['notes'] ?? null,
                            ]);
                    } else {
                        // Create new item
                        MaterialRequestItem::create([
                            'material_request_id' => $materialRequest->id,
                            'material_id' => $itemData['material_id'],
                            'description' => $itemData['description'] ?? null,
                            'quantity_requested' => $itemData['quantity_requested'],
                            'unit_id' => $itemData['unit_id'] ?? null,
                            'unit_price' => $itemData['unit_price'] ?? null,
                            'notes' => $itemData['notes'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Material request updated successfully',
                'data' => $materialRequest->fresh()->load(['items.material', 'items.unit', 'project', 'department'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating material request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error updating material request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $materialRequest = MaterialRequest::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        if (!$materialRequest->canBeDeleted()) {
            return response()->json([
                'message' => 'Only draft material requests can be deleted'
            ], 422);
        }

        try {
            $materialRequest->delete();

            return response()->json([
                'message' => 'Material request deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting material request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error deleting material request'
            ], 500);
        }
    }

    /**
     * Approve a material request
     */
    public function approve(Request $request, string $id)
    {
        $materialRequest = MaterialRequest::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        if (!$materialRequest->canBeApproved()) {
            return response()->json([
                'message' => 'Material request cannot be approved in its current status'
            ], 422);
        }

        $validated = $request->validate([
            'items' => 'sometimes|array',
            'items.*.id' => 'required|exists:material_request_items,id',
            'items.*.quantity_approved' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $materialRequest->update([
                'status' => 'approved',
                'approved_by_id' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Update approved quantities if provided
            if (isset($validated['items'])) {
                foreach ($validated['items'] as $itemData) {
                    MaterialRequestItem::where('id', $itemData['id'])
                        ->where('material_request_id', $materialRequest->id)
                        ->update([
                            'quantity_approved' => $itemData['quantity_approved']
                        ]);
                }
            } else {
                // Approve all items with requested quantity
                $materialRequest->items()->update([
                    'quantity_approved' => DB::raw('quantity_requested')
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Material request approved successfully',
                'data' => $materialRequest->fresh()->load(['items.material', 'approvedBy'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving material request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error approving material request'
            ], 500);
        }
    }

    /**
     * Reject a material request
     */
    public function reject(Request $request, string $id)
    {
        $materialRequest = MaterialRequest::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        if (!$materialRequest->canBeApproved()) {
            return response()->json([
                'message' => 'Material request cannot be rejected in its current status'
            ], 422);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        try {
            $materialRequest->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'approved_by_id' => auth()->id(),
                'approved_at' => now(),
            ]);

            return response()->json([
                'message' => 'Material request rejected successfully',
                'data' => $materialRequest->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error('Error rejecting material request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error rejecting material request'
            ], 500);
        }
    }

    /**
     * Issue materials for a material request
     */
    public function issue(Request $request, string $id)
    {
        $materialRequest = MaterialRequest::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        if (!$materialRequest->canBeIssued()) {
            return response()->json([
                'message' => 'Material request cannot be issued in its current status'
            ], 422);
        }

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:material_request_items,id',
            'items.*.quantity_issued' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['items'] as $itemData) {
                $item = MaterialRequestItem::findOrFail($itemData['id']);
                
                // Check if quantity to issue is valid
                $remainingQty = $item->remaining_quantity;
                if ($itemData['quantity_issued'] > $remainingQty) {
                    throw new \Exception("Quantity to issue ({$itemData['quantity_issued']}) exceeds remaining quantity ({$remainingQty}) for item ID {$item->id}");
                }

                // Check inventory availability
                $inventoryBalance = InventoryBalance::where('material_id', $item->material_id)
                    ->where('warehouse_id', $validated['warehouse_id'])
                    ->first();

                if (!$inventoryBalance || $inventoryBalance->quantity < $itemData['quantity_issued']) {
                    throw new \Exception("Insufficient inventory for material ID {$item->material_id}. Available: " . ($inventoryBalance->quantity ?? 0));
                }

                // Update item issued quantity
                $item->increment('quantity_issued', $itemData['quantity_issued']);

                // Create inventory transaction
                InventoryTransaction::create([
                    'transaction_date' => now(),
                    'transaction_type' => 'issue',
                    'material_id' => $item->material_id,
                    'warehouse_id' => $validated['warehouse_id'],
                    'project_id' => $materialRequest->project_id,
                    'quantity' => $itemData['quantity_issued'],
                    'unit_cost' => $item->unit_price ?? $inventoryBalance->average_cost ?? 0,
                    'reference_type' => 'material_request',
                    'reference_id' => $materialRequest->id,
                    'notes' => $validated['notes'] ?? "Issued for Material Request {$materialRequest->request_number}",
                    'created_by' => auth()->id(),
                    'company_id' => auth()->user()->company_id,
                ]);

                // Update inventory balance
                $inventoryBalance->decrement('quantity', $itemData['quantity_issued']);
            }

            // Update material request status
            $allItemsIssued = $materialRequest->items()->get()->every(function ($item) {
                return $item->isFullyIssued();
            });

            $materialRequest->update([
                'status' => $allItemsIssued ? 'issued' : 'partially_issued'
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Materials issued successfully',
                'data' => $materialRequest->fresh()->load(['items.material'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error issuing materials', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error issuing materials',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert material request to purchase requisition
     */
    public function convertToPurchaseRequisition(Request $request, string $id)
    {
        $materialRequest = MaterialRequest::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        if ($materialRequest->status !== 'approved') {
            return response()->json([
                'message' => 'Only approved material requests can be converted to purchase requisitions'
            ], 422);
        }

        try {
            // This is a placeholder - actual implementation would create a purchase requisition
            // based on the material request items
            
            return response()->json([
                'message' => 'Conversion to purchase requisition initiated',
                'data' => [
                    'material_request_id' => $materialRequest->id,
                    'material_request_number' => $materialRequest->request_number,
                    'note' => 'Purchase requisition creation feature to be implemented'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error converting to purchase requisition', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error converting to purchase requisition'
            ], 500);
        }
    }
}
