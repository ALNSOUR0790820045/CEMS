<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseRequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PurchaseRequisition::with([
            'requestedBy',
            'department',
            'project',
            'approvedBy',
            'items.material',
            'items.unit'
        ]);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('pr_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('pr_date', '<=', $request->to_date);
        }

        $purchaseRequisitions = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($purchaseRequisitions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pr_date' => 'required|date',
            'required_date' => 'required|date|after_or_equal:pr_date',
            'requested_by_id' => 'required|exists:employees,id',
            'department_id' => 'nullable|exists:departments,id',
            'project_id' => 'nullable|exists:projects,id',
            'priority' => 'required|in:normal,urgent,critical',
            'status' => 'nullable|in:draft,submitted',
            'notes' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'nullable|exists:materials,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.estimated_unit_price' => 'nullable|numeric|min:0',
            'items.*.specifications' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $pr = PurchaseRequisition::create([
                'pr_date' => $validated['pr_date'],
                'required_date' => $validated['required_date'],
                'requested_by_id' => $validated['requested_by_id'],
                'department_id' => $validated['department_id'] ?? null,
                'project_id' => $validated['project_id'] ?? null,
                'priority' => $validated['priority'],
                'status' => $validated['status'] ?? 'draft',
                'notes' => $validated['notes'] ?? null,
                'company_id' => $validated['company_id'],
            ]);

            foreach ($validated['items'] as $item) {
                $pr->items()->create($item);
            }

            DB::commit();

            return response()->json([
                'message' => 'Purchase requisition created successfully',
                'data' => $pr->load(['requestedBy', 'department', 'project', 'items.material', 'items.unit'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create purchase requisition',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pr = PurchaseRequisition::with([
            'requestedBy',
            'department',
            'project',
            'approvedBy',
            'items.material',
            'items.unit'
        ])->findOrFail($id);

        return response()->json($pr);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pr = PurchaseRequisition::findOrFail($id);

        // Only draft or submitted PRs can be updated
        if (!in_array($pr->status, ['draft', 'submitted'])) {
            return response()->json([
                'message' => 'Cannot update a PR with status: ' . $pr->status
            ], 422);
        }

        $validated = $request->validate([
            'pr_date' => 'sometimes|date',
            'required_date' => 'sometimes|date|after_or_equal:pr_date',
            'requested_by_id' => 'sometimes|exists:employees,id',
            'department_id' => 'nullable|exists:departments,id',
            'project_id' => 'nullable|exists:projects,id',
            'priority' => 'sometimes|in:normal,urgent,critical',
            'notes' => 'nullable|string',
        ]);

        $pr->update($validated);

        return response()->json([
            'message' => 'Purchase requisition updated successfully',
            'data' => $pr->load(['requestedBy', 'department', 'project', 'items.material', 'items.unit'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pr = PurchaseRequisition::findOrFail($id);

        // Only draft PRs can be deleted
        if ($pr->status !== 'draft') {
            return response()->json([
                'message' => 'Only draft purchase requisitions can be deleted'
            ], 422);
        }

        $pr->delete();

        return response()->json([
            'message' => 'Purchase requisition deleted successfully'
        ]);
    }

    /**
     * Approve a purchase requisition
     */
    public function approve(Request $request, string $id)
    {
        $pr = PurchaseRequisition::findOrFail($id);

        if ($pr->status !== 'submitted') {
            return response()->json([
                'message' => 'Only submitted purchase requisitions can be approved'
            ], 422);
        }

        $validated = $request->validate([
            'approved_by_id' => 'required|exists:users,id',
        ]);

        $pr->approve($validated['approved_by_id']);

        return response()->json([
            'message' => 'Purchase requisition approved successfully',
            'data' => $pr->fresh(['requestedBy', 'department', 'project', 'approvedBy', 'items.material', 'items.unit'])
        ]);
    }

    /**
     * Reject a purchase requisition
     */
    public function reject(Request $request, string $id)
    {
        $pr = PurchaseRequisition::findOrFail($id);

        if ($pr->status !== 'submitted') {
            return response()->json([
                'message' => 'Only submitted purchase requisitions can be rejected'
            ], 422);
        }

        $validated = $request->validate([
            'approved_by_id' => 'required|exists:users,id',
            'rejection_reason' => 'required|string',
        ]);

        $pr->reject($validated['approved_by_id'], $validated['rejection_reason']);

        return response()->json([
            'message' => 'Purchase requisition rejected successfully',
            'data' => $pr->fresh(['requestedBy', 'department', 'project', 'approvedBy', 'items.material', 'items.unit'])
        ]);
    }

    /**
     * Convert purchase requisition to purchase order
     */
    public function convertToPO(Request $request, string $id)
    {
        $pr = PurchaseRequisition::findOrFail($id);

        if ($pr->status !== 'approved') {
            return response()->json([
                'message' => 'Only approved purchase requisitions can be converted to PO'
            ], 422);
        }

        $validated = $request->validate([
            'vendor_id' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.purchase_requisition_item_id' => 'required|exists:purchase_requisition_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            // Update converted quantities
            foreach ($validated['items'] as $item) {
                $prItem = $pr->items()->findOrFail($item['purchase_requisition_item_id']);
                $prItem->converted_quantity += $item['quantity'];
                $prItem->save();
            }

            // Check if all items are fully converted
            $allConverted = $pr->items->every(function ($item) {
                return $item->converted_quantity >= $item->quantity;
            });

            if ($allConverted) {
                $pr->status = 'converted_to_po';
                $pr->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Purchase requisition converted to PO successfully',
                'data' => $pr->fresh(['requestedBy', 'department', 'project', 'items.material', 'items.unit'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to convert purchase requisition to PO',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
