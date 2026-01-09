<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PettyCashAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PettyCashAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PettyCashAccount::with(['company', 'custodian', 'glAccount', 'project', 'branch'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->has('custodian_id')) {
            $query->where('custodian_id', $request->custodian_id);
        }

        if ($request->has('low_balance')) {
            $query->lowBalance();
        }

        $accounts = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($accounts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_code' => 'required|string|unique:petty_cash_accounts,account_code',
            'account_name' => 'required|string|max:255',
            'custodian_id' => 'required|exists:users,id',
            'float_amount' => 'required|numeric|min:0',
            'minimum_balance' => 'nullable|numeric|min:0',
            'gl_account_id' => 'nullable|exists:gl_accounts,id',
            'project_id' => 'nullable|exists:projects,id',
            'branch_id' => 'nullable|exists:branches,id',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $account = PettyCashAccount::create([
            'account_code' => $request->account_code,
            'account_name' => $request->account_name,
            'custodian_id' => $request->custodian_id,
            'float_amount' => $request->float_amount,
            'current_balance' => $request->float_amount, // Initial balance = float amount
            'minimum_balance' => $request->minimum_balance ?? 0,
            'gl_account_id' => $request->gl_account_id,
            'project_id' => $request->project_id,
            'branch_id' => $request->branch_id,
            'is_active' => $request->is_active ?? true,
            'company_id' => $request->user()->company_id,
        ]);

        return response()->json($account->load(['custodian', 'glAccount', 'project', 'branch']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $account = PettyCashAccount::with(['company', 'custodian', 'glAccount', 'project', 'branch'])
            ->findOrFail($id);

        return response()->json($account);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $account = PettyCashAccount::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'account_code' => 'required|string|unique:petty_cash_accounts,account_code,' . $id,
            'account_name' => 'required|string|max:255',
            'custodian_id' => 'required|exists:users,id',
            'float_amount' => 'required|numeric|min:0',
            'minimum_balance' => 'nullable|numeric|min:0',
            'gl_account_id' => 'nullable|exists:gl_accounts,id',
            'project_id' => 'nullable|exists:projects,id',
            'branch_id' => 'nullable|exists:branches,id',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $account->update($request->all());

        return response()->json($account->load(['custodian', 'glAccount', 'project', 'branch']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $account = PettyCashAccount::findOrFail($id);
        $account->delete();

        return response()->json(['message' => 'Petty cash account deleted successfully']);
    }

    /**
     * Get account statement.
     */
    public function statement(Request $request, string $id)
    {
        $account = PettyCashAccount::findOrFail($id);
        
        $query = $account->transactions()
            ->with(['expenseCategory', 'requestedBy', 'approvedBy'])
            ->orderBy('transaction_date', 'desc');

        if ($request->has('from_date')) {
            $query->where('transaction_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('transaction_date', '<=', $request->to_date);
        }

        $transactions = $query->paginate($request->per_page ?? 50);

        return response()->json([
            'account' => $account,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Get account balance.
     */
    public function balance(string $id)
    {
        $account = PettyCashAccount::findOrFail($id);

        return response()->json([
            'account_code' => $account->account_code,
            'account_name' => $account->account_name,
            'current_balance' => $account->current_balance,
            'float_amount' => $account->float_amount,
            'minimum_balance' => $account->minimum_balance,
            'is_low_balance' => $account->isLowBalance(),
        ]);
    }
}
