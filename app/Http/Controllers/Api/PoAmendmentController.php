<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PoAmendment;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PoAmendmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PoAmendment::with(['purchaseOrder', 'requestedBy', 'approvedBy']);

        if ($request->has('purchase_order_id')) {
            $query->where('purchase_order_id', $request->purchase_order_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('amendment_type')) {
            $query->where('amendment_type', $request->amendment_type);
        }

        $amendments = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($amendments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'amendment_date' => 'required|date',
            'amendment_type' => 'required|in:quantity,price,delivery_date,cancel_item,add_item',
            'description' => 'required|string',
            'old_value' => 'nullable|string',
            'new_value' => 'nullable|string',
            'reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $purchaseOrder = PurchaseOrder::findOrFail($request->purchase_order_id);

            if (!in_array($purchaseOrder->status, ['approved', 'sent', 'partially_received'])) {
                return response()->json(['error' => 'Cannot amend purchase order in current status'], 400);
            }

            $amendmentNumber = $this->generateAmendmentNumber();

            $amendment = PoAmendment::create([
                'amendment_number' => $amendmentNumber,
                'purchase_order_id' => $request->purchase_order_id,
                'amendment_date' => $request->amendment_date,
                'amendment_type' => $request->amendment_type,
                'description' => $request->description,
                'old_value' => $request->old_value,
                'new_value' => $request->new_value,
                'reason' => $request->reason,
                'status' => 'pending',
                'requested_by_id' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'Amendment created successfully',
                'data' => $amendment
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create amendment: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $amendment = PoAmendment::with(['purchaseOrder', 'requestedBy', 'approvedBy'])
            ->findOrFail($id);

        return response()->json($amendment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $amendment = PoAmendment::findOrFail($id);

        if ($amendment->status !== 'pending') {
            return response()->json(['error' => 'Cannot update amendment in current status'], 400);
        }

        $validator = Validator::make($request->all(), [
            'amendment_date' => 'sometimes|date',
            'description' => 'sometimes|string',
            'reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $amendment->update($request->all());

        return response()->json([
            'message' => 'Amendment updated successfully',
            'data' => $amendment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $amendment = PoAmendment::findOrFail($id);

        if ($amendment->status !== 'pending') {
            return response()->json(['error' => 'Cannot delete amendment in current status'], 400);
        }

        $amendment->delete();

        return response()->json(['message' => 'Amendment deleted successfully']);
    }

    /**
     * Approve amendment
     */
    public function approve(Request $request, string $id)
    {
        $amendment = PoAmendment::findOrFail($id);

        if ($amendment->status !== 'pending') {
            return response()->json(['error' => 'Amendment already processed'], 400);
        }

        $amendment->update([
            'status' => 'approved',
            'approved_by_id' => auth()->id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Amendment approved successfully',
            'data' => $amendment
        ]);
    }

    /**
     * Reject amendment
     */
    public function reject(Request $request, string $id)
    {
        $amendment = PoAmendment::findOrFail($id);

        if ($amendment->status !== 'pending') {
            return response()->json(['error' => 'Amendment already processed'], 400);
        }

        $amendment->update([
            'status' => 'rejected',
            'approved_by_id' => auth()->id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Amendment rejected successfully',
            'data' => $amendment
        ]);
    }

    /**
     * Generate amendment number
     */
    private function generateAmendmentNumber()
    {
        $year = date('Y');
        $lastAmendment = PoAmendment::where('amendment_number', 'like', "AMD-{$year}-%")
            ->orderBy('amendment_number', 'desc')
            ->first();

        if ($lastAmendment) {
            $lastNumber = intval(substr($lastAmendment->amendment_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "AMD-{$year}-{$newNumber}";
    }
}
