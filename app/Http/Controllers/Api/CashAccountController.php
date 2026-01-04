<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CashAccount::with(['currency', 'glAccount', 'company'])
            ->where('company_id', Auth::user()->company_id);

        if ($request->has('account_type')) {
            $query->where('account_type', $request->account_type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $cashAccounts = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $cashAccounts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_code' => 'required|string|unique:cash_accounts,account_code',
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:cash,bank,petty_cash',
            'currency_id' => 'required|exists:currencies,id',
            'current_balance' => 'nullable|numeric|min:0',
            'gl_account_id' => 'nullable|exists:gl_accounts,id',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = Auth::user()->company_id;
        $validated['current_balance'] = $validated['current_balance'] ?? 0;

        $cashAccount = CashAccount::create($validated);
        $cashAccount->load(['currency', 'glAccount', 'company']);

        return response()->json([
            'success' => true,
            'message' => 'Cash account created successfully',
            'data' => $cashAccount,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CashAccount $cashAccount)
    {
        // Ensure user can only view their company's cash accounts
        if ($cashAccount->company_id !== Auth::user()->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $cashAccount->load(['currency', 'glAccount', 'company', 'transactions']);

        return response()->json([
            'success' => true,
            'data' => $cashAccount,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CashAccount $cashAccount)
    {
        // Ensure user can only update their company's cash accounts
        if ($cashAccount->company_id !== Auth::user()->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $validated = $request->validate([
            'account_code' => 'required|string|unique:cash_accounts,account_code,'.$cashAccount->id,
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:cash,bank,petty_cash',
            'currency_id' => 'required|exists:currencies,id',
            'gl_account_id' => 'nullable|exists:gl_accounts,id',
            'is_active' => 'boolean',
        ]);

        $cashAccount->update($validated);
        $cashAccount->load(['currency', 'glAccount', 'company']);

        return response()->json([
            'success' => true,
            'message' => 'Cash account updated successfully',
            'data' => $cashAccount,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CashAccount $cashAccount)
    {
        // Ensure user can only delete their company's cash accounts
        if ($cashAccount->company_id !== Auth::user()->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        // Check if account has transactions
        if ($cashAccount->transactions()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete cash account with existing transactions',
            ], 422);
        }

        $cashAccount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cash account deleted successfully',
        ]);
    }
}
