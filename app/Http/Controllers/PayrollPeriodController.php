<?php

namespace App\Http\Controllers;

use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PayrollPeriodController extends Controller
{
    public function index(Request $request)
    {
        $query = PayrollPeriod::with(['company', 'calculatedBy', 'approvedBy'])
            ->where('company_id', Auth::user()->company_id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $periods = $query->latest()->paginate(15);

        return response()->json($periods);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'period_name' => 'required|string|max:255',
            'period_type' => ['required', Rule::in(['monthly', 'weekly', 'daily'])],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'payment_date' => 'required|date',
        ]);

        $validated['company_id'] = Auth::user()->company_id;
        $validated['status'] = 'open';

        $period = PayrollPeriod::create($validated);

        return response()->json([
            'message' => 'Payroll period created successfully',
            'data' => $period->load(['company', 'calculatedBy', 'approvedBy']),
        ], 201);
    }

    public function show(PayrollPeriod $payrollPeriod)
    {
        $this->authorize('view', $payrollPeriod);

        return response()->json($payrollPeriod->load([
            'entries.employee',
            'entries.allowances',
            'entries.deductions',
            'company',
            'calculatedBy',
            'approvedBy',
        ]));
    }

    public function update(Request $request, PayrollPeriod $payrollPeriod)
    {
        $this->authorize('update', $payrollPeriod);

        if ($payrollPeriod->status !== 'open') {
            return response()->json([
                'message' => 'Cannot update payroll period that is not in open status',
            ], 400);
        }

        $validated = $request->validate([
            'period_name' => 'sometimes|required|string|max:255',
            'period_type' => ['sometimes', 'required', Rule::in(['monthly', 'weekly', 'daily'])],
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'payment_date' => 'sometimes|required|date',
        ]);

        $payrollPeriod->update($validated);

        return response()->json([
            'message' => 'Payroll period updated successfully',
            'data' => $payrollPeriod->load(['company', 'calculatedBy', 'approvedBy']),
        ]);
    }

    public function destroy(PayrollPeriod $payrollPeriod)
    {
        $this->authorize('delete', $payrollPeriod);

        if ($payrollPeriod->status !== 'open') {
            return response()->json([
                'message' => 'Cannot delete payroll period that is not in open status',
            ], 400);
        }

        $payrollPeriod->delete();

        return response()->json([
            'message' => 'Payroll period deleted successfully',
        ]);
    }

    public function calculate(PayrollPeriod $payrollPeriod)
    {
        $this->authorize('update', $payrollPeriod);

        try {
            $payrollPeriod->calculate(Auth::user());

            return response()->json([
                'message' => 'Payroll period calculated successfully',
                'data' => $payrollPeriod->load([
                    'entries.employee',
                    'entries.allowances',
                    'entries.deductions',
                ]),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function approve(PayrollPeriod $payrollPeriod)
    {
        $this->authorize('update', $payrollPeriod);

        try {
            $payrollPeriod->approve(Auth::user());

            return response()->json([
                'message' => 'Payroll period approved successfully',
                'data' => $payrollPeriod->load([
                    'entries.employee',
                    'entries.allowances',
                    'entries.deductions',
                ]),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
