<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDependent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeDependentController extends Controller
{
    public function index(Employee $employee)
    {
        return response()->json($employee->dependents);
    }

    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'relationship' => 'required|in:spouse,son,daughter,father,mother,other',
            'date_of_birth' => 'nullable|date',
            'national_id' => 'nullable|string|max:255',
            'is_covered_by_insurance' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = Auth::user()->company_id;

        $dependent = $employee->dependents()->create($validated);

        return response()->json($dependent, 201);
    }

    public function update(Request $request, Employee $employee, EmployeeDependent $dependent)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'relationship' => 'required|in:spouse,son,daughter,father,mother,other',
            'date_of_birth' => 'nullable|date',
            'national_id' => 'nullable|string|max:255',
            'is_covered_by_insurance' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $dependent->update($validated);

        return response()->json($dependent);
    }

    public function destroy(Employee $employee, EmployeeDependent $dependent)
    {
        $dependent->delete();
        return response()->json(null, 204);
    }
}
