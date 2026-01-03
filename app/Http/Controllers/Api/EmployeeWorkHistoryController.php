<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Employee;
use App\Models\EmployeeWorkHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeWorkHistoryController extends BaseApiController
{
    public function index(Employee $employee)
    {
        return response()->json($employee->workHistory);
    }

    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'responsibilities' => 'nullable|string',
            'leaving_reason' => 'nullable|string',
        ]);

        $validated['company_id'] = $this->getCompanyId();

        $workHistory = $employee->workHistory()->create($validated);

        return response()->json($workHistory, 201);
    }

    public function update(Request $request, Employee $employee, EmployeeWorkHistory $workHistory)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'responsibilities' => 'nullable|string',
            'leaving_reason' => 'nullable|string',
        ]);

        $workHistory->update($validated);

        return response()->json($workHistory);
    }

    public function destroy(Employee $employee, EmployeeWorkHistory $workHistory)
    {
        $workHistory->delete();
        return response()->json(null, 204);
    }
}
