<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\CashTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CashAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CashAccount::with(['currency', 'branch', 'custodian', 'glAccount']);

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by type
        if ($request->has('account_type')) {
            $query->where('account_type', $request->account_type);
        }

        // Filter by branch
        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by currency
        if ($request->has('currency_id')) {
            $query->where('currency_id', $request->currency_id);
        }

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $accounts = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $accounts
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_name' => 'required|string|max:255',
            'account_name_en' => 'nullable|string|max:255',
            'account_type' => 'required|in:cash,bank,petty_cash,safe',
            'currency_id' => 'required|exists:currencies,id',
            'opening_balance' => 'nullable|numeric|min:0',
            'gl_account_id' => 'nullable|exists:gl_accounts,id',
            'custodian_id' => 'nullable|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $accountData = $validator->validated();
            $accountData['company_id'] = auth()->user()->company_id;
            $accountData['current_balance'] = $accountData['opening_balance'] ?? 0;

            $account = CashAccount::create($accountData);

            return response()->json([
                'success' => true,
                'message' => 'Cash account created successfully',
                'data' => $account->load(['currency', 'branch', 'custodian', 'glAccount'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create cash account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $account = CashAccount::with(['currency', 'branch', 'custodian', 'glAccount', 'company'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $account
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'account_name' => 'string|max:255',
            'account_name_en' => 'nullable|string|max:255',
            'account_type' => 'in:cash,bank,petty_cash,safe',
            'currency_id' => 'exists:currencies,id',
            'gl_account_id' => 'nullable|exists:gl_accounts,id',
            'custodian_id' => 'nullable|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $account = CashAccount::findOrFail($id);
            $account->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Cash account updated successfully',
                'data' => $account->load(['currency', 'branch', 'custodian', 'glAccount'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cash account',
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
            $account = CashAccount::findOrFail($id);
            
            // Check if account has transactions
            if ($account->transactions()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete account with existing transactions'
                ], 422);
            }

            $account->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cash account deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete cash account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get account balance
     */
    public function balance($id)
    {
        $account = CashAccount::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'account_id' => $account->id,
                'account_code' => $account->account_code,
                'account_name' => $account->account_name,
                'current_balance' => $account->current_balance,
                'available_balance' => $account->available_balance,
                'currency' => $account->currency
            ]
        ]);
    }

    /**
     * Get account statement
     */
    public function statement(Request $request, $id)
    {
        $account = CashAccount::findOrFail($id);
        
        $query = CashTransaction::where('cash_account_id', $id)
            ->with(['currency', 'postedBy']);

        if ($request->has('from_date')) {
            $query->where('transaction_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('transaction_date', '<=', $request->to_date);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => [
                'account' => $account,
                'transactions' => $transactions
            ]
        ]);
    }

    /**
     * Get account transactions
     */
    public function transactions(Request $request, $id)
    {
        $query = CashTransaction::where('cash_account_id', $id)
            ->with(['currency', 'postedBy']);

        if ($request->has('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }
}
