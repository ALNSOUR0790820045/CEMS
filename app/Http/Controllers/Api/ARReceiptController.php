<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AllocateARReceiptRequest;
use App\Http\Requests\StoreARReceiptRequest;
use App\Http\Requests\UpdateARReceiptRequest;
use App\Http\Resources\ARReceiptResource;
use App\Models\ARReceipt;
use App\Models\ARReceiptAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ARReceiptController extends Controller
{
    public function index(Request $request)
    {
        $query = ARReceipt::with(['client', 'currency', 'createdBy']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by client
        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('receipt_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->where('receipt_date', '<=', $request->to_date);
        }

        $receipts = $query->orderBy('created_at', 'desc')->paginate(15);

        return ARReceiptResource::collection($receipts);
    }

    public function store(StoreARReceiptRequest $request)
    {
        try {
            $receiptData = $request->validated();
            
            // Add company and user
            $receiptData['company_id'] = auth()->user()->company_id;
            $receiptData['created_by_id'] = auth()->id();

            $receipt = ARReceipt::create($receiptData);

            return new ARReceiptResource($receipt->load(['client', 'currency', 'createdBy']));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create receipt', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $receipt = ARReceipt::with(['client', 'currency', 'bankAccount', 'allocations.arInvoice', 'createdBy'])
            ->findOrFail($id);

        return new ARReceiptResource($receipt);
    }

    public function update(UpdateARReceiptRequest $request, $id)
    {
        try {
            $receipt = ARReceipt::findOrFail($id);
            $receipt->update($request->validated());

            return new ARReceiptResource($receipt->load(['client', 'currency', 'createdBy']));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update receipt', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $receipt = ARReceipt::findOrFail($id);
            
            // Check if receipt has allocations
            if ($receipt->allocations()->count() > 0) {
                return response()->json(['error' => 'Cannot delete receipt with allocations'], 400);
            }

            $receipt->delete();

            return response()->json(['message' => 'Receipt deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete receipt', 'message' => $e->getMessage()], 500);
        }
    }

    public function allocate(AllocateARReceiptRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $receipt = ARReceipt::findOrFail($id);
            
            // Validate total allocation doesn't exceed receipt amount
            $totalAllocation = array_sum(array_column($request->allocations, 'allocated_amount'));
            $currentAllocated = $receipt->allocations()->sum('allocated_amount');
            
            if (($currentAllocated + $totalAllocation) > $receipt->amount) {
                return response()->json(['error' => 'Total allocation exceeds receipt amount'], 400);
            }

            // Create allocations
            foreach ($request->allocations as $allocation) {
                ARReceiptAllocation::create([
                    'a_r_receipt_id' => $receipt->id,
                    'a_r_invoice_id' => $allocation['a_r_invoice_id'],
                    'allocated_amount' => $allocation['allocated_amount'],
                ]);
            }

            DB::commit();

            return new ARReceiptResource($receipt->load(['client', 'currency', 'allocations.arInvoice', 'createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to allocate receipt', 'message' => $e->getMessage()], 500);
        }
    }
}
