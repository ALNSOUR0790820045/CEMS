<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Employee;
use App\Models\EmployeeQualification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeQualificationController extends BaseApiController
{
    public function index(Employee $employee)
    {
        return response()->json($employee->qualifications);
    }

    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'qualification_type' => 'required|in:degree,diploma,certificate,training,license',
            'qualification_name' => 'required|string|max:255',
            'institution' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'grade' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|max:5120',
        ]);

        $validated['company_id'] = $this->getCompanyId();

        if ($request->hasFile('attachment')) {
            $validated['attachment_path'] = $request->file('attachment')
                ->store('employees/qualifications', 'public');
        }

        $qualification = $employee->qualifications()->create($validated);

        return response()->json($qualification, 201);
    }

    public function update(Request $request, Employee $employee, EmployeeQualification $qualification)
    {
        $validated = $request->validate([
            'qualification_type' => 'required|in:degree,diploma,certificate,training,license',
            'qualification_name' => 'required|string|max:255',
            'institution' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'grade' => 'nullable|string|max:255',
        ]);

        $qualification->update($validated);

        return response()->json($qualification);
    }

    public function destroy(Employee $employee, EmployeeQualification $qualification)
    {
        $qualification->delete();
        return response()->json(null, 204);
    }
}
