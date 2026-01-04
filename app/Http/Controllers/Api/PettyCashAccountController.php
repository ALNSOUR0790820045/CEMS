<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PettyCashAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PettyCashAccountController extends Controller
{
    /**
     * Display a listing of petty cash accounts.
     */
    public function index(Request $request)
    {
        $query = PettyCashAccount::with(['custodian', 'company']);

        // Filter by company if provided
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $accounts = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($accounts);
    }

    /**
     * Store a newly created petty cash account.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_name' => 'required|string|max:255',
            'custodian_id' => 'required|exists:users,id',
            'fund_limit' => 'required|numeric|min:0',
            'current_balance' => 'nullable|numeric|min:0',
            'gl_account_id' => 'nullable|integer',
            'is_active' => 'boolean',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $account = PettyCashAccount::create($request->all());
        $account->load(['custodian', 'company']);

        return response()->json($account, 201);
    }

    /**
     * Display the specified petty cash account.
     */
    public function show(PettyCashAccount $pettyCashAccount)
    {
        $pettyCashAccount->load(['custodian', 'company', 'transactions']);
        return response()->json($pettyCashAccount);
    }

    /**
     * Update the specified petty cash account.
     */
    public function update(Request $request, PettyCashAccount $pettyCashAccount)
    {
        $validator = Validator::make($request->all(), [
            'account_name' => 'sometimes|required|string|max:255',
            'custodian_id' => 'sometimes|required|exists:users,id',
            'fund_limit' => 'sometimes|required|numeric|min:0',
            'current_balance' => 'sometimes|nullable|numeric|min:0',
            'gl_account_id' => 'nullable|integer',
            'is_active' => 'boolean',
            'company_id' => 'sometimes|required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pettyCashAccount->update($request->all());
        $pettyCashAccount->load(['custodian', 'company']);

        return response()->json($pettyCashAccount);
    }

    /**
     * Remove the specified petty cash account.
     */
    public function destroy(PettyCashAccount $pettyCashAccount)
    {
        // Check if account has transactions
        if ($pettyCashAccount->transactions()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete account with existing transactions'
            ], 422);
        }

        $pettyCashAccount->delete();
        return response()->json(['message' => 'Petty cash account deleted successfully'], 200);
    }
}
