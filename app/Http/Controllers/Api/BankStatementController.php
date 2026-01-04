<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankStatement;
use App\Models\BankStatementLine;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BankStatementController extends Controller
{
    /**
     * Display a listing of bank statements.
     */
    public function index(Request $request)
    {
        $query = BankStatement::with(['bankAccount', 'company', 'reconciledBy']);

        // Filter by bank account
        if ($request->has('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $bankStatements = $query->latest('statement_date')->get();

        return response()->json([
            'success' => true,
            'data' => $bankStatements,
        ]);
    }

    /**
     * Store a newly created bank statement.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'statement_date' => 'required|date',
            'opening_balance' => 'required|numeric',
            'closing_balance' => 'required|numeric',
            'company_id' => 'required|exists:companies,id',
            'lines' => 'required|array|min:1',
            'lines.*.transaction_date' => 'required|date',
            'lines.*.value_date' => 'nullable|date',
            'lines.*.description' => 'nullable|string',
            'lines.*.reference_number' => 'nullable|string',
            'lines.*.debit_amount' => 'required|numeric|min:0',
            'lines.*.credit_amount' => 'required|numeric|min:0',
            'lines.*.balance' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();
            $lines = $data['lines'];
            unset($data['lines']);

            // Create bank statement
            $bankStatement = BankStatement::create($data);

            // Create statement lines
            foreach ($lines as $line) {
                $bankStatement->lines()->create($line);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bank statement created successfully',
                'data' => $bankStatement->load('lines'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create bank statement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified bank statement.
     */
    public function show($id)
    {
        $bankStatement = BankStatement::with([
            'bankAccount',
            'company',
            'reconciledBy',
            'lines' => function ($query) {
                $query->orderBy('transaction_date');
            }
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $bankStatement,
        ]);
    }

    /**
     * Import bank statement from CSV/Excel file.
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,xlsx,xls',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'statement_date' => 'required|date',
            'opening_balance' => 'required|numeric',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $data = $this->parseImportFile($file);

            if (empty($data)) {
                throw new \Exception('No valid data found in the file');
            }

            // Calculate closing balance
            $closingBalance = $request->opening_balance;
            foreach ($data as $line) {
                $closingBalance += ($line['credit_amount'] - $line['debit_amount']);
            }

            // Create bank statement
            $bankStatement = BankStatement::create([
                'bank_account_id' => $request->bank_account_id,
                'statement_date' => $request->statement_date,
                'opening_balance' => $request->opening_balance,
                'closing_balance' => $closingBalance,
                'status' => 'imported',
                'company_id' => $request->company_id,
            ]);

            // Create statement lines
            foreach ($data as $line) {
                $bankStatement->lines()->create($line);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bank statement imported successfully',
                'data' => $bankStatement->load('lines'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to import bank statement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reconcile a bank statement.
     */
    public function reconcile(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reconciliations' => 'required|array',
            'reconciliations.*.line_id' => 'required|exists:bank_statement_lines,id',
            'reconciliations.*.matched_transaction_type' => 'nullable|string',
            'reconciliations.*.matched_transaction_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $bankStatement = BankStatement::findOrFail($id);

        DB::beginTransaction();
        try {
            // Update reconciliation status for each line
            foreach ($request->reconciliations as $reconciliation) {
                BankStatementLine::where('id', $reconciliation['line_id'])
                    ->where('bank_statement_id', $id)
                    ->update([
                        'is_reconciled' => true,
                        'matched_transaction_type' => $reconciliation['matched_transaction_type'] ?? null,
                        'matched_transaction_id' => $reconciliation['matched_transaction_id'] ?? null,
                    ]);
            }

            // Check if all lines are reconciled
            $unreconciledCount = $bankStatement->lines()->where('is_reconciled', false)->count();
            
            if ($unreconciledCount === 0) {
                $bankStatement->update([
                    'status' => 'reconciled',
                    'reconciled_by_id' => auth()->id(),
                    'reconciled_at' => now(),
                ]);
            } else {
                $bankStatement->update([
                    'status' => 'reconciling',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bank statement reconciliation updated successfully',
                'data' => $bankStatement->fresh(['lines']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to reconcile bank statement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Parse import file and extract data.
     */
    private function parseImportFile($file)
    {
        $extension = $file->getClientOriginalExtension();
        $data = [];

        if ($extension === 'csv') {
            $handle = fopen($file->getRealPath(), 'r');
            $header = fgetcsv($handle); // Skip header row
            
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 5) {
                    $data[] = [
                        'transaction_date' => $row[0] ?? now(),
                        'value_date' => $row[1] ?? null,
                        'description' => $row[2] ?? '',
                        'reference_number' => $row[3] ?? null,
                        'debit_amount' => (float)($row[4] ?? 0),
                        'credit_amount' => (float)($row[5] ?? 0),
                        'balance' => isset($row[6]) ? (float)$row[6] : null,
                    ];
                }
            }
            fclose($handle);
        }

        return $data;
    }
}
