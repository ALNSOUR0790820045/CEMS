<?php

namespace App\Http\Controllers;

use App\Models\PayrollEntry;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollEntryController extends Controller
{
    public function index(Request $request)
    {
        $query = PayrollEntry::with(['employee', 'payrollPeriod', 'bankAccount'])
            ->where('company_id', Auth::user()->company_id);

        if ($request->has('payroll_period_id')) {
            $query->where('payroll_period_id', $request->payroll_period_id);
        }

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $entries = $query->latest()->paginate(15);

        return response()->json($entries);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payroll_period_id' => 'required|exists:payroll_periods,id',
            'employee_id' => 'required|exists:users,id',
            'basic_salary' => 'required|numeric|min:0',
            'days_worked' => 'required|integer|min:0',
            'days_absent' => 'nullable|integer|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_amount' => 'nullable|numeric|min:0',
            'payment_method' => ['required', Rule::in(['bank_transfer', 'cash', 'check'])],
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'notes' => 'nullable|string',
            'allowances' => 'nullable|array',
            'allowances.*.allowance_type' => ['required', Rule::in(['housing', 'transport', 'food', 'mobile', 'other'])],
            'allowances.*.allowance_name' => 'required|string',
            'allowances.*.amount' => 'required|numeric|min:0',
            'allowances.*.is_taxable' => 'nullable|boolean',
            'deductions' => 'nullable|array',
            'deductions.*.deduction_type' => ['required', Rule::in(['tax', 'social_insurance', 'loan', 'advance', 'penalty', 'other'])],
            'deductions.*.deduction_name' => 'required|string',
            'deductions.*.amount' => 'required|numeric|min:0',
        ]);

        $validated['company_id'] = Auth::user()->company_id;
        $validated['status'] = 'draft';

        $entry = PayrollEntry::create($validated);

        // Create allowances
        if (isset($validated['allowances'])) {
            foreach ($validated['allowances'] as $allowance) {
                $entry->allowances()->create($allowance);
            }
        }

        // Create deductions
        if (isset($validated['deductions'])) {
            foreach ($validated['deductions'] as $deduction) {
                $entry->deductions()->create($deduction);
            }
        }

        // Calculate totals
        $entry->calculateTotals();

        return response()->json([
            'message' => 'Payroll entry created successfully',
            'data' => $entry->load(['employee', 'allowances', 'deductions', 'bankAccount']),
        ], 201);
    }

    public function show(PayrollEntry $payrollEntry)
    {
        $this->authorize('view', $payrollEntry);

        return response()->json($payrollEntry->load([
            'employee',
            'payrollPeriod',
            'allowances',
            'deductions',
            'bankAccount',
        ]));
    }

    public function update(Request $request, PayrollEntry $payrollEntry)
    {
        $this->authorize('update', $payrollEntry);

        if (!in_array($payrollEntry->status, ['draft', 'calculated'])) {
            return response()->json([
                'message' => 'Cannot update payroll entry in current status',
            ], 400);
        }

        $validated = $request->validate([
            'basic_salary' => 'sometimes|required|numeric|min:0',
            'days_worked' => 'sometimes|required|integer|min:0',
            'days_absent' => 'nullable|integer|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_amount' => 'nullable|numeric|min:0',
            'payment_method' => ['sometimes', 'required', Rule::in(['bank_transfer', 'cash', 'check'])],
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'notes' => 'nullable|string',
        ]);

        $payrollEntry->update($validated);

        return response()->json([
            'message' => 'Payroll entry updated successfully',
            'data' => $payrollEntry->load(['employee', 'allowances', 'deductions', 'bankAccount']),
        ]);
    }

    public function payslip(PayrollEntry $payrollEntry)
    {
        $this->authorize('view', $payrollEntry);

        $data = [
            'entry' => $payrollEntry->load([
                'employee',
                'payrollPeriod',
                'allowances',
                'deductions',
                'company',
            ]),
        ];

        $pdf = Pdf::loadView('payroll.payslip', $data);

        return $pdf->download('payslip-' . $payrollEntry->employee->name . '-' . $payrollEntry->payrollPeriod->period_name . '.pdf');
    }
}
