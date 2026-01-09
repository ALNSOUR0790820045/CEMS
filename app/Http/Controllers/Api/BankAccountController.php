<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBankAccountRequest;
use App\Http\Requests\UpdateBankAccountRequest;
use App\Models\BankAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = BankAccount::with(['currency', 'glAccount', 'company']);

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('account_name', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%")
                  ->orWhere('bank_name', 'like', "%{$search}%");
            });
        }

        $bankAccounts = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $bankAccounts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBankAccountRequest $request): JsonResponse
    {
        $bankAccount = BankAccount::create($request->validated());
        $bankAccount->load(['currency', 'glAccount', 'company']);

        return response()->json([
            'success' => true,
            'message' => 'Bank account created successfully',
            'data' => $bankAccount,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(BankAccount $bankAccount): JsonResponse
    {
        $bankAccount->load(['currency', 'glAccount', 'company', 'transactions']);

        return response()->json([
            'success' => true,
            'data' => $bankAccount,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBankAccountRequest $request, BankAccount $bankAccount): JsonResponse
    {
        $bankAccount->update($request->validated());
        $bankAccount->load(['currency', 'glAccount', 'company']);

        return response()->json([
            'success' => true,
            'message' => 'Bank account updated successfully',
            'data' => $bankAccount,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bankAccount): JsonResponse
    {
        $bankAccount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bank account deleted successfully',
        ]);
    }

    /**
     * Get balance information for a bank account.
     */
    public function balance(BankAccount $bankAccount): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'account_number' => $bankAccount->account_number,
                'account_name' => $bankAccount->account_name,
                'current_balance' => $bankAccount->current_balance,
                'bank_balance' => $bankAccount->bank_balance,
                'difference' => $bankAccount->current_balance - $bankAccount->bank_balance,
            ],
        ]);
    }

    /**
     * Get transactions for a bank account.
     */
    public function transactions(Request $request, BankAccount $bankAccount): JsonResponse
    {
        $transactions = $bankAccount->transactions()
            ->with(['transactionable'])
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }
}
