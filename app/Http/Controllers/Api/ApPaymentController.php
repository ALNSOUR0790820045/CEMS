<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApPayment;
use App\Models\ApPaymentAllocation;
use App\Models\ApInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ApPayment::with(['vendor', 'currency', 'bankAccount', 'createdBy', 'allocations.apInvoice'])
            ->where('company_id', $request->user()->company_id);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->has('from_date')) {
            $query->where('payment_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('payment_date', '<=', $request->to_date);
        }

        $payments = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($payments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_date' => 'required|date',
            'vendor_id' => 'required|exists:vendors,id',
            'payment_method' => 'required|in:cash,check,bank_transfer,credit_card',
            'amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'check_number' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payment = ApPayment::create([
            'payment_date' => $request->payment_date,
            'vendor_id' => $request->vendor_id,
            'payment_method' => $request->payment_method,
            'amount' => $request->amount,
            'currency_id' => $request->currency_id,
            'exchange_rate' => $request->exchange_rate ?? 1,
            'bank_account_id' => $request->bank_account_id,
            'check_number' => $request->check_number,
            'reference_number' => $request->reference_number,
            'status' => 'pending',
            'notes' => $request->notes,
            'company_id' => $request->user()->company_id,
            'created_by_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Payment created successfully',
            'payment' => $payment->load('vendor', 'currency')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ApPayment $payment)
    {
        $payment->load(['vendor', 'currency', 'bankAccount', 'createdBy', 'allocations.apInvoice']);

        return response()->json($payment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ApPayment $payment)
    {
        // Only pending payments can be updated
        if ($payment->status !== 'pending') {
            return response()->json(['error' => 'Only pending payments can be updated'], 422);
        }

        $validator = Validator::make($request->all(), [
            'payment_date' => 'required|date',
            'vendor_id' => 'required|exists:vendors,id',
            'payment_method' => 'required|in:cash,check,bank_transfer,credit_card',
            'amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'check_number' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payment->update([
            'payment_date' => $request->payment_date,
            'vendor_id' => $request->vendor_id,
            'payment_method' => $request->payment_method,
            'amount' => $request->amount,
            'currency_id' => $request->currency_id,
            'exchange_rate' => $request->exchange_rate ?? 1,
            'bank_account_id' => $request->bank_account_id,
            'check_number' => $request->check_number,
            'reference_number' => $request->reference_number,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Payment updated successfully',
            'payment' => $payment->load('vendor', 'currency')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApPayment $payment)
    {
        // Only pending payments without allocations can be deleted
        if ($payment->status !== 'pending') {
            return response()->json(['error' => 'Only pending payments can be deleted'], 422);
        }

        if ($payment->allocations()->count() > 0) {
            return response()->json(['error' => 'Cannot delete payment with allocations'], 422);
        }

        $payment->delete();

        return response()->json(['message' => 'Payment deleted successfully']);
    }

    /**
     * Allocate payment to invoices.
     */
    public function allocate(Request $request, ApPayment $payment)
    {
        $validator = Validator::make($request->all(), [
            'allocations' => 'required|array|min:1',
            'allocations.*.invoice_id' => 'required|exists:ap_invoices,id',
            'allocations.*.amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Validate payment vendor matches invoice vendors
            $totalAllocated = 0;
            foreach ($request->allocations as $allocation) {
                $invoice = ApInvoice::findOrFail($allocation['invoice_id']);
                
                if ($invoice->vendor_id !== $payment->vendor_id) {
                    return response()->json(['error' => 'Invoice vendor must match payment vendor'], 422);
                }

                if ($invoice->status === 'paid' || $invoice->status === 'cancelled') {
                    return response()->json(['error' => 'Cannot allocate to paid or cancelled invoices'], 422);
                }

                $totalAllocated += $allocation['amount'];
            }

            // Validate total allocated doesn't exceed payment amount
            if ($totalAllocated > $payment->amount) {
                return response()->json(['error' => 'Total allocation exceeds payment amount'], 422);
            }

            // Delete existing allocations
            $payment->allocations()->delete();

            // Create new allocations
            foreach ($request->allocations as $allocation) {
                ApPaymentAllocation::create([
                    'ap_payment_id' => $payment->id,
                    'ap_invoice_id' => $allocation['invoice_id'],
                    'allocated_amount' => $allocation['amount'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Payment allocated successfully',
                'payment' => $payment->load('allocations.apInvoice')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to allocate payment: ' . $e->getMessage()], 500);
        }
    }
}
