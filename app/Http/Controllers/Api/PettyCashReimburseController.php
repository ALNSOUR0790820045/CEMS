<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PettyCashTransaction;
use App\Models\PettyCashAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PettyCashReimburseController extends Controller
{
    /**
     * Process petty cash reimbursement request.
     */
    public function reimburse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'petty_cash_account_id' => 'required|exists:petty_cash_accounts,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'receipt_number' => 'nullable|string|max:255',
            'attachment_path' => 'nullable|string|max:255',
            'approved_by_id' => 'nullable|exists:users,id',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $account = PettyCashAccount::findOrFail($request->petty_cash_account_id);

            // Create reimbursement transaction
            $transaction = PettyCashTransaction::create([
                'transaction_date' => now(),
                'petty_cash_account_id' => $request->petty_cash_account_id,
                'transaction_type' => PettyCashTransaction::TYPE_REIMBURSEMENT,
                'amount' => $request->amount,
                'description' => $request->description,
                'receipt_number' => $request->receipt_number,
                'attachment_path' => $request->attachment_path,
                'approved_by_id' => $request->approved_by_id,
                'company_id' => $request->company_id,
                'created_by_id' => Auth::id(),
            ]);

            // Update account balance
            $account->current_balance += $request->amount;
            $account->save();

            DB::commit();

            $transaction->load(['pettyCashAccount', 'company', 'approvedBy', 'createdBy']);

            return response()->json([
                'message' => 'Reimbursement processed successfully',
                'transaction' => $transaction,
                'new_balance' => $account->current_balance,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to process reimbursement: ' . $e->getMessage()], 500);
        }
    }
}
