<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBankStatementRequest;
use App\Models\BankStatement;
use App\Models\BankStatementLine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankStatementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = BankStatement::with(['bankAccount', 'company']);

        // Filter by bank account
        if ($request->has('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('statement_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->where('statement_date', '<=', $request->to_date);
        }

        $statements = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $statements,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBankStatementRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $statement = BankStatement::create($request->except('lines'));

            // Create statement lines if provided
            if ($request->has('lines') && is_array($request->lines)) {
                foreach ($request->lines as $line) {
                    $statement->lines()->create($line);
                }
            }

            $statement->load(['bankAccount', 'lines']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bank statement created successfully',
                'data' => $statement,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create bank statement: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BankStatement $bankStatement): JsonResponse
    {
        $bankStatement->load(['bankAccount', 'lines', 'company', 'reconciledBy']);

        return response()->json([
            'success' => true,
            'data' => $bankStatement,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankStatement $bankStatement): JsonResponse
    {
        $bankStatement->update($request->all());
        $bankStatement->load(['bankAccount', 'lines']);

        return response()->json([
            'success' => true,
            'message' => 'Bank statement updated successfully',
            'data' => $bankStatement,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankStatement $bankStatement): JsonResponse
    {
        $bankStatement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bank statement deleted successfully',
        ]);
    }

    /**
     * Import bank statement from CSV file.
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'statement_date' => 'required|date',
            'company_id' => 'required|exists:companies,id',
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $data = array_map('str_getcsv', file($file->getRealPath()));
            $header = array_shift($data);

            $statement = BankStatement::create([
                'bank_account_id' => $request->bank_account_id,
                'statement_date' => $request->statement_date,
                'period_from' => $request->period_from ?? $request->statement_date,
                'period_to' => $request->period_to ?? $request->statement_date,
                'opening_balance' => $request->opening_balance ?? 0,
                'closing_balance' => 0,
                'total_deposits' => 0,
                'total_withdrawals' => 0,
                'status' => 'imported',
                'company_id' => $request->company_id,
            ]);

            $totalDeposits = 0;
            $totalWithdrawals = 0;
            $runningBalance = $statement->opening_balance;

            foreach ($data as $row) {
                if (count($row) < 4) continue;

                $debit = (float)($row[3] ?? 0);
                $credit = (float)($row[4] ?? 0);
                
                $runningBalance += $credit - $debit;
                $totalDeposits += $credit;
                $totalWithdrawals += $debit;

                $statement->lines()->create([
                    'transaction_date' => $row[0],
                    'description' => $row[1],
                    'reference_number' => $row[2] ?? null,
                    'debit_amount' => $debit,
                    'credit_amount' => $credit,
                    'balance' => $runningBalance,
                ]);
            }

            $statement->update([
                'closing_balance' => $runningBalance,
                'total_deposits' => $totalDeposits,
                'total_withdrawals' => $totalWithdrawals,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bank statement imported successfully',
                'data' => $statement->load(['bankAccount', 'lines']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to import bank statement: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Auto-match statement lines with transactions.
     */
    public function autoMatch(BankStatement $bankStatement): JsonResponse
    {
        $matched = 0;
        $unmatchedLines = $bankStatement->lines()->unmatched()->get();

        foreach ($unmatchedLines as $line) {
            // Simple matching logic based on amount and date
            $amount = $line->credit_amount - $line->debit_amount;
            
            // Try to match with AR receipts
            $receipt = \App\Models\ARReceipt::where('receipt_date', $line->transaction_date)
                ->where('amount', abs($amount))
                ->where('bank_account_id', $bankStatement->bank_account_id)
                ->whereNull('matched_statement_line_id')
                ->first();

            if ($receipt) {
                $line->update([
                    'is_matched' => true,
                    'matched_transaction_type' => 'receipt',
                    'matched_transaction_id' => $receipt->id,
                    'match_date' => now(),
                ]);
                $matched++;
                continue;
            }

            // Try to match with AP payments
            $payment = \App\Models\ApPayment::where('payment_date', $line->transaction_date)
                ->where('amount', abs($amount))
                ->where('bank_account_id', $bankStatement->bank_account_id)
                ->whereNull('matched_statement_line_id')
                ->first();

            if ($payment) {
                $line->update([
                    'is_matched' => true,
                    'matched_transaction_type' => 'payment',
                    'matched_transaction_id' => $payment->id,
                    'match_date' => now(),
                ]);
                $matched++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Matched {$matched} transactions automatically",
            'data' => [
                'matched_count' => $matched,
                'total_lines' => $unmatchedLines->count(),
            ],
        ]);
    }
}
