<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashTransfer;
use App\Models\CashTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CashTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CashTransfer::with(['fromAccount', 'toAccount', 'requestedBy', 'approvedBy']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('transfer_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->where('transfer_date', '<=', $request->to_date);
        }

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $transfers = $query->orderBy('transfer_date', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $transfers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transfer_date' => 'required|date',
            'from_account_id' => 'required|exists:cash_accounts,id',
            'to_account_id' => 'required|exists:cash_accounts,id|different:from_account_id',
            'amount' => 'required|numeric|min:0',
            'fees' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $transferData = $validator->validated();
            
            // Get currencies from accounts
            $fromAccount = \App\Models\CashAccount::findOrFail($transferData['from_account_id']);
            $toAccount = \App\Models\CashAccount::findOrFail($transferData['to_account_id']);

            $transferData['from_currency_id'] = $fromAccount->currency_id;
            $transferData['to_currency_id'] = $toAccount->currency_id;
            $transferData['exchange_rate'] = 1; // Default, should be calculated based on currencies
            $transferData['company_id'] = auth()->user()->company_id;
            $transferData['requested_by_id'] = auth()->id();
            $transferData['status'] = 'pending';

            $transfer = CashTransfer::create($transferData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cash transfer created successfully',
                'data' => $transfer->load(['fromAccount', 'toAccount', 'requestedBy'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create cash transfer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transfer = CashTransfer::with(['fromAccount', 'toAccount', 'fromCurrency', 'toCurrency', 'requestedBy', 'approvedBy', 'company'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $transfer
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $transfer = CashTransfer::findOrFail($id);

        // Can only update pending transfers
        if ($transfer->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Can only update pending transfers'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'transfer_date' => 'date',
            'from_account_id' => 'exists:cash_accounts,id',
            'to_account_id' => 'exists:cash_accounts,id|different:from_account_id',
            'amount' => 'numeric|min:0',
            'fees' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $transfer->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Cash transfer updated successfully',
                'data' => $transfer->load(['fromAccount', 'toAccount', 'requestedBy'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cash transfer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $transfer = CashTransfer::findOrFail($id);

            // Can only delete pending transfers
            if ($transfer->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only delete pending transfers'
                ], 422);
            }

            $transfer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cash transfer deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete cash transfer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve a transfer
     */
    public function approve($id)
    {
        try {
            $transfer = CashTransfer::findOrFail($id);

            if ($transfer->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transfer is not in pending status'
                ], 422);
            }

            $transfer->status = 'approved';
            $transfer->approved_by_id = auth()->id();
            $transfer->approved_at = now();
            $transfer->save();

            return response()->json([
                'success' => true,
                'message' => 'Transfer approved successfully',
                'data' => $transfer->load(['fromAccount', 'toAccount', 'requestedBy', 'approvedBy'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve transfer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete a transfer
     */
    public function complete($id)
    {
        try {
            DB::beginTransaction();

            $transfer = CashTransfer::findOrFail($id);

            if ($transfer->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transfer must be approved first'
                ], 422);
            }

            // Check if from account has sufficient balance
            $fromAccount = $transfer->fromAccount;
            $totalAmount = $transfer->amount + ($transfer->fees ?? 0);

            if ($fromAccount->current_balance < $totalAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance in source account'
                ], 422);
            }

            // Create transfer-out transaction for from account
            CashTransaction::create([
                'transaction_date' => $transfer->transfer_date,
                'cash_account_id' => $transfer->from_account_id,
                'transaction_type' => 'transfer_out',
                'amount' => $totalAmount,
                'currency_id' => $transfer->from_currency_id,
                'exchange_rate' => $transfer->exchange_rate,
                'reference_type' => 'CashTransfer',
                'reference_id' => $transfer->id,
                'description' => "Transfer to {$transfer->toAccount->account_name}",
                'status' => 'posted',
                'posted_by_id' => auth()->id(),
                'posted_at' => now(),
                'company_id' => $transfer->company_id,
            ]);

            // Update from account balance
            $fromAccount->current_balance -= $totalAmount;
            $fromAccount->save();

            // Create transfer-in transaction for to account
            CashTransaction::create([
                'transaction_date' => $transfer->transfer_date,
                'cash_account_id' => $transfer->to_account_id,
                'transaction_type' => 'transfer_in',
                'amount' => $transfer->amount,
                'currency_id' => $transfer->to_currency_id,
                'exchange_rate' => $transfer->exchange_rate,
                'reference_type' => 'CashTransfer',
                'reference_id' => $transfer->id,
                'description' => "Transfer from {$transfer->fromAccount->account_name}",
                'status' => 'posted',
                'posted_by_id' => auth()->id(),
                'posted_at' => now(),
                'company_id' => $transfer->company_id,
            ]);

            // Update to account balance
            $toAccount = $transfer->toAccount;
            $toAccount->current_balance += $transfer->amount;
            $toAccount->save();

            $transfer->status = 'completed';
            $transfer->completed_at = now();
            $transfer->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transfer completed successfully',
                'data' => $transfer->load(['fromAccount', 'toAccount', 'requestedBy', 'approvedBy'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete transfer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a transfer
     */
    public function cancel($id)
    {
        try {
            $transfer = CashTransfer::findOrFail($id);

            if (!in_array($transfer->status, ['pending', 'approved'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel completed or cancelled transfer'
                ], 422);
            }

            $transfer->status = 'cancelled';
            $transfer->save();

            return response()->json([
                'success' => true,
                'message' => 'Transfer cancelled successfully',
                'data' => $transfer->load(['fromAccount', 'toAccount', 'requestedBy', 'approvedBy'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel transfer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
