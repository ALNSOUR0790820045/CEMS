<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseRequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PurchaseRequisition::with([
            'requestedBy', 
            'project', 
            'department', 
            'currency', 
            'items.unit'
        ]);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $requisitions = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($requisitions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'requisition_date' => 'required|date',
            'required_date' => 'required|date|after_or_equal:requisition_date',
            'project_id' => 'nullable|exists:projects,id',
            'department_id' => 'nullable|exists:departments,id',
            'priority' => 'required|in:low,normal,high,urgent',
            'type' => 'required|in:materials,services,equipment,subcontract',
            'currency_id' => 'required|exists:currencies,id',
            'justification' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'nullable|exists:materials,id',
            'items.*.item_description' => 'required|string',
            'items.*.specifications' => 'nullable|string',
            'items.*.quantity_requested' => 'required|numeric|min:0.001',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.estimated_unit_price' => 'required|numeric|min:0',
            'items.*.preferred_vendor_id' => 'nullable|exists:vendors,id',
            'items.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $requisition = PurchaseRequisition::create([
                'requisition_date' => $request->requisition_date,
                'required_date' => $request->required_date,
                'project_id' => $request->project_id,
                'department_id' => $request->department_id,
                'requested_by_id' => auth()->id(),
                'priority' => $request->priority,
                'type' => $request->type,
                'status' => 'draft',
                'currency_id' => $request->currency_id,
                'justification' => $request->justification,
                'notes' => $request->notes,
                'company_id' => auth()->user()->company_id,
            ]);

            $totalAmount = 0;
            foreach ($request->items as $itemData) {
                $estimatedTotal = $itemData['quantity_requested'] * $itemData['estimated_unit_price'];
                $totalAmount += $estimatedTotal;

                PurchaseRequisitionItem::create([
                    'purchase_requisition_id' => $requisition->id,
                    'material_id' => $itemData['material_id'] ?? null,
                    'item_description' => $itemData['item_description'],
                    'specifications' => $itemData['specifications'] ?? null,
                    'quantity_requested' => $itemData['quantity_requested'],
                    'unit_id' => $itemData['unit_id'],
                    'estimated_unit_price' => $itemData['estimated_unit_price'],
                    'estimated_total' => $estimatedTotal,
                    'preferred_vendor_id' => $itemData['preferred_vendor_id'] ?? null,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            $requisition->update(['total_estimated_amount' => $totalAmount]);

            DB::commit();

            return response()->json([
                'message' => 'Purchase requisition created successfully',
                'data' => $requisition->load(['items.unit', 'requestedBy', 'currency'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create purchase requisition', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $requisition = PurchaseRequisition::with([
            'items.unit',
            'items.material',
            'items.preferredVendor',
            'requestedBy',
            'approvedBy',
            'project',
            'department',
            'currency',
            'approvalWorkflows.approver',
            'quotes.vendor'
        ])->findOrFail($id);

        return response()->json($requisition);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $requisition = PurchaseRequisition::findOrFail($id);

        // Check if can be edited
        if (!in_array($requisition->status, ['draft'])) {
            return response()->json(['error' => 'Cannot edit requisition after submission'], 403);
        }

        $validator = Validator::make($request->all(), [
            'requisition_date' => 'sometimes|date',
            'required_date' => 'sometimes|date',
            'project_id' => 'nullable|exists:projects,id',
            'department_id' => 'nullable|exists:departments,id',
            'priority' => 'sometimes|in:low,normal,high,urgent',
            'type' => 'sometimes|in:materials,services,equipment,subcontract',
            'currency_id' => 'sometimes|exists:currencies,id',
            'justification' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'sometimes|array|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $requisition->update($request->only([
                'requisition_date',
                'required_date',
                'project_id',
                'department_id',
                'priority',
                'type',
                'currency_id',
                'justification',
                'notes',
            ]));

            // Update items if provided
            if ($request->has('items')) {
                $requisition->items()->delete();
                
                $totalAmount = 0;
                foreach ($request->items as $itemData) {
                    $estimatedTotal = $itemData['quantity_requested'] * $itemData['estimated_unit_price'];
                    $totalAmount += $estimatedTotal;

                    PurchaseRequisitionItem::create([
                        'purchase_requisition_id' => $requisition->id,
                        'material_id' => $itemData['material_id'] ?? null,
                        'item_description' => $itemData['item_description'],
                        'specifications' => $itemData['specifications'] ?? null,
                        'quantity_requested' => $itemData['quantity_requested'],
                        'unit_id' => $itemData['unit_id'],
                        'estimated_unit_price' => $itemData['estimated_unit_price'],
                        'estimated_total' => $estimatedTotal,
                        'preferred_vendor_id' => $itemData['preferred_vendor_id'] ?? null,
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                }

                $requisition->update(['total_estimated_amount' => $totalAmount]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Purchase requisition updated successfully',
                'data' => $requisition->load(['items.unit', 'requestedBy', 'currency'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update purchase requisition', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $requisition = PurchaseRequisition::findOrFail($id);

        // Check if can be deleted
        if (!in_array($requisition->status, ['draft', 'rejected'])) {
            return response()->json(['error' => 'Cannot delete requisition in current status'], 403);
        }

        $requisition->delete();

        return response()->json(['message' => 'Purchase requisition deleted successfully']);
    }

    /**
     * Submit requisition for approval
     */
    public function submit(string $id)
    {
        $requisition = PurchaseRequisition::findOrFail($id);

        if ($requisition->status !== 'draft') {
            return response()->json(['error' => 'Only draft requisitions can be submitted'], 403);
        }

        $requisition->submit();

        return response()->json([
            'message' => 'Purchase requisition submitted for approval',
            'data' => $requisition
        ]);
    }

    /**
     * Approve requisition
     */
    public function approve(Request $request, string $id)
    {
        $requisition = PurchaseRequisition::findOrFail($id);

        if ($requisition->status !== 'pending_approval') {
            return response()->json(['error' => 'Only pending requisitions can be approved'], 403);
        }

        $requisition->approve(auth()->user());

        return response()->json([
            'message' => 'Purchase requisition approved',
            'data' => $requisition
        ]);
    }

    /**
     * Reject requisition
     */
    public function reject(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $requisition = PurchaseRequisition::findOrFail($id);

        if ($requisition->status !== 'pending_approval') {
            return response()->json(['error' => 'Only pending requisitions can be rejected'], 403);
        }

        $requisition->reject(auth()->user(), $request->reason);

        return response()->json([
            'message' => 'Purchase requisition rejected',
            'data' => $requisition
        ]);
    }

    /**
     * Cancel requisition
     */
    public function cancel(string $id)
    {
        $requisition = PurchaseRequisition::findOrFail($id);

        if (in_array($requisition->status, ['ordered', 'cancelled'])) {
            return response()->json(['error' => 'Cannot cancel requisition in current status'], 403);
        }

        $requisition->cancel();

        return response()->json([
            'message' => 'Purchase requisition cancelled',
            'data' => $requisition
        ]);
    }

    /**
     * Get approval history
     */
    public function approvalHistory(string $id)
    {
        $requisition = PurchaseRequisition::findOrFail($id);
        $history = $requisition->approvalWorkflows()
            ->with('approver')
            ->orderBy('approval_level')
            ->get();

        return response()->json($history);
    }

    /**
     * Convert to Purchase Order
     */
    public function convertToPO(Request $request, string $id)
    {
        $requisition = PurchaseRequisition::findOrFail($id);

        if ($requisition->status !== 'approved') {
            return response()->json(['error' => 'Only approved requisitions can be converted to PO'], 403);
        }

        // This would create a Purchase Order - placeholder for now
        return response()->json([
            'message' => 'Purchase Order creation not yet implemented',
            'data' => $requisition
        ]);
    }
}
