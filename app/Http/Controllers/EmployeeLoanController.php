<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EmployeeLoanController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeLoan::with(['employee', 'company'])
            ->where('company_id', Auth::user()->company_id);

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $loans = $query->latest()->paginate(15);

        return response()->json($loans);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'loan_date' => 'required|date',
            'loan_amount' => 'required|numeric|min:0',
            'installment_amount' => 'required|numeric|min:0',
            'total_installments' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = Auth::user()->company_id;
        $validated['status'] = 'active';
        $validated['paid_installments'] = 0;

        $loan = EmployeeLoan::create($validated);

        return response()->json([
            'message' => 'Employee loan created successfully',
            'data' => $loan->load(['employee', 'company']),
        ], 201);
    }

    public function show(EmployeeLoan $employeeLoan)
    {
        $this->authorize('view', $employeeLoan);

        return response()->json($employeeLoan->load(['employee', 'company']));
    }

    public function update(Request $request, EmployeeLoan $employeeLoan)
    {
        $this->authorize('update', $employeeLoan);

        if ($employeeLoan->status !== 'active') {
            return response()->json([
                'message' => 'Cannot update loan that is not active',
            ], 400);
        }

        $validated = $request->validate([
            'loan_date' => 'sometimes|required|date',
            'loan_amount' => 'sometimes|required|numeric|min:0',
            'installment_amount' => 'sometimes|required|numeric|min:0',
            'total_installments' => 'sometimes|required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $employeeLoan->update($validated);

        return response()->json([
            'message' => 'Employee loan updated successfully',
            'data' => $employeeLoan->load(['employee', 'company']),
        ]);
    }

    public function destroy(EmployeeLoan $employeeLoan)
    {
        $this->authorize('delete', $employeeLoan);

        if ($employeeLoan->paid_installments > 0) {
            return response()->json([
                'message' => 'Cannot delete loan with paid installments. Cancel it instead.',
            ], 400);
        }

        $employeeLoan->delete();

        return response()->json([
            'message' => 'Employee loan deleted successfully',
        ]);
    }

    public function cancel(EmployeeLoan $employeeLoan)
    {
        $this->authorize('update', $employeeLoan);

        if ($employeeLoan->status !== 'active') {
            return response()->json([
                'message' => 'Can only cancel active loans',
            ], 400);
        }

        $employeeLoan->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Employee loan cancelled successfully',
            'data' => $employeeLoan->load(['employee', 'company']),
        ]);
    }
}
