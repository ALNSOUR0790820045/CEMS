<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BankAccountController extends Controller
{
    /**
     * Display a listing of bank accounts.
     */
    public function index(Request $request)
    {
        $query = BankAccount::with(['currency', 'glAccount', 'company']);

        // Filter by company if provided
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $bankAccounts = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $bankAccounts,
        ]);
    }

    /**
     * Store a newly created bank account.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_number' => 'required|string|max:255|unique:bank_accounts',
            'account_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:50',
            'iban' => 'nullable|string|max:50',
            'currency_id' => 'required|exists:currencies,id',
            'current_balance' => 'nullable|numeric',
            'book_balance' => 'nullable|numeric',
            'gl_account_id' => 'required|exists:gl_accounts,id',
            'is_active' => 'boolean',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $bankAccount = BankAccount::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Bank account created successfully',
            'data' => $bankAccount->load(['currency', 'glAccount', 'company']),
        ], 201);
    }

    /**
     * Display the specified bank account.
     */
    public function show($id)
    {
        $bankAccount = BankAccount::with(['currency', 'glAccount', 'company', 'bankStatements'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $bankAccount,
        ]);
    }

    /**
     * Update the specified bank account.
     */
    public function update(Request $request, $id)
    {
        $bankAccount = BankAccount::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'account_number' => 'required|string|max:255|unique:bank_accounts,account_number,' . $id,
            'account_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:50',
            'iban' => 'nullable|string|max:50',
            'currency_id' => 'required|exists:currencies,id',
            'current_balance' => 'nullable|numeric',
            'book_balance' => 'nullable|numeric',
            'gl_account_id' => 'required|exists:gl_accounts,id',
            'is_active' => 'boolean',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $bankAccount->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Bank account updated successfully',
            'data' => $bankAccount->load(['currency', 'glAccount', 'company']),
        ]);
    }

    /**
     * Remove the specified bank account.
     */
    public function destroy($id)
    {
        $bankAccount = BankAccount::findOrFail($id);
        $bankAccount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bank account deleted successfully',
        ]);
    }
}
