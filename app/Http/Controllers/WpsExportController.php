<?php

namespace App\Http\Controllers;

use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WpsExportController extends Controller
{
    public function export(Request $request)
    {
        $validated = $request->validate([
            'payroll_period_id' => 'required|exists:payroll_periods,id',
        ]);

        $payrollPeriod = PayrollPeriod::with([
            'entries.employee.primaryBankAccount',
            'company',
        ])->findOrFail($validated['payroll_period_id']);

        // Check if user has access to this payroll period
        if ($payrollPeriod->company_id !== Auth::user()->company_id) {
            return response()->json([
                'message' => 'Unauthorized access to this payroll period',
            ], 403);
        }

        // Check if payroll is approved
        if ($payrollPeriod->status !== 'approved' && $payrollPeriod->status !== 'paid') {
            return response()->json([
                'message' => 'Payroll period must be approved before generating WPS file',
            ], 400);
        }

        // Generate WPS file content
        $wpsContent = $this->generateWpsFile($payrollPeriod);

        // Store file temporarily
        $sanitizedPeriodName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $payrollPeriod->period_name);
        $filename = 'wps-' . $sanitizedPeriodName . '-' . now()->format('Y-m-d') . '.txt';
        Storage::disk('local')->put('temp/' . $filename, $wpsContent);

        return response()->download(
            storage_path('app/temp/' . $filename),
            $filename,
            ['Content-Type' => 'text/plain']
        )->deleteFileAfterSend();
    }

    protected function generateWpsFile(PayrollPeriod $payrollPeriod): string
    {
        $lines = [];

        // Header Record (SCR)
        $lines[] = $this->generateHeaderRecord($payrollPeriod);

        // Salary Records (EDR)
        foreach ($payrollPeriod->entries as $entry) {
            if ($entry->status === 'approved' && $entry->payment_method === 'bank_transfer') {
                $lines[] = $this->generateSalaryRecord($entry, $payrollPeriod);
            }
        }

        return implode("\n", $lines);
    }

    protected function generateHeaderRecord(PayrollPeriod $payrollPeriod): string
    {
        $company = $payrollPeriod->company;

        return sprintf(
            "SCR%s%s%s%s%s%s%s",
            str_pad($company->commercial_registration ?? '', 10, ' '),
            str_pad($company->name_en ?? $company->name, 140, ' '),
            str_pad($payrollPeriod->payment_date->format('Ymd'), 8, '0'),
            str_pad($payrollPeriod->entries->count(), 7, '0', STR_PAD_LEFT),
            str_pad(number_format($payrollPeriod->total_net, 2, '', ''), 15, '0', STR_PAD_LEFT),
            str_pad('01', 2, '0'), // Payment type: 01 for salary
            str_pad(' ', 8, ' ') // Reserved
        );
    }

    protected function generateSalaryRecord($entry, $payrollPeriod): string
    {
        $employee = $entry->employee;
        $bankAccount = $entry->bankAccount ?? $employee->primaryBankAccount;

        if (!$bankAccount) {
            throw new \Exception("Employee {$employee->name} does not have a bank account");
        }

        return sprintf(
            "EDR%s%s%s%s%s%s%s%s%s%s",
            str_pad($employee->employee_id ?? $employee->id, 14, ' '),
            str_pad($bankAccount->account_number ?? '', 24, ' '),
            str_pad('', 2, ' '), // Routing code (optional)
            str_pad($bankAccount->bank_name ?? '', 23, ' '),
            str_pad(number_format($entry->net_salary, 2, '', ''), 15, '0', STR_PAD_LEFT),
            str_pad($payrollPeriod->payment_date->format('Ymd'), 8, '0'),
            str_pad('', 3, ' '), // Days absent
            str_pad('', 3, ' '), // Extra working days
            str_pad('', 24, ' '), // Reference number
            str_pad(' ', 6, ' ') // Reserved
        );
    }

    public function generateBankTransferList(Request $request)
    {
        $validated = $request->validate([
            'payroll_period_id' => 'required|exists:payroll_periods,id',
        ]);

        $payrollPeriod = PayrollPeriod::with([
            'entries.employee.primaryBankAccount',
            'company',
        ])->findOrFail($validated['payroll_period_id']);

        // Check if user has access to this payroll period
        if ($payrollPeriod->company_id !== Auth::user()->company_id) {
            return response()->json([
                'message' => 'Unauthorized access to this payroll period',
            ], 403);
        }

        $transferList = $payrollPeriod->entries()
            ->where('payment_method', 'bank_transfer')
            ->with(['employee.primaryBankAccount'])
            ->get()
            ->map(function ($entry) {
                $bankAccount = $entry->bankAccount ?? $entry->employee->primaryBankAccount;

                return [
                    'employee_id' => $entry->employee->employee_id ?? $entry->employee->id,
                    'employee_name' => $entry->employee->name,
                    'bank_name' => $bankAccount->bank_name ?? 'N/A',
                    'account_number' => $bankAccount->account_number ?? 'N/A',
                    'iban' => $bankAccount->iban ?? 'N/A',
                    'net_salary' => $entry->net_salary,
                ];
            });

        return response()->json([
            'period' => $payrollPeriod->period_name,
            'payment_date' => $payrollPeriod->payment_date,
            'total_amount' => $payrollPeriod->total_net,
            'transfers' => $transferList,
        ]);
    }
}
