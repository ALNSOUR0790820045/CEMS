<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        $query = Employee::with(['user', 'company', 'shiftSchedule']);

        // Filter by company
        if ($request->has('company_id')) {
            $query->forCompany($request->company_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            // Default to active employees
            $query->active();
        }

        // Filter by department
        if ($request->has('department')) {
            $query->where('department', $request->department);
        }

        $perPage = $request->get('per_page', 15);
        $employees = $query->orderBy('employee_number')
            ->paginate($perPage);

        return response()->json($employees);
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id|unique:employees,user_id',
            'employee_number' => 'required|string|unique:employees,employee_number',
            'hire_date' => 'nullable|date',
            'department' => 'nullable|string',
            'position' => 'nullable|string',
            'employment_type' => 'nullable|in:full_time,part_time,contract,internship',
            'shift_schedule_id' => 'nullable|exists:shift_schedules,id',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive,terminated,on_leave',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $employee = Employee::create($request->all());

        return response()->json([
            'message' => 'Employee created successfully',
            'data' => $employee->load(['user', 'company', 'shiftSchedule'])
        ], 201);
    }

    /**
     * Display the specified employee.
     */
    public function show($id)
    {
        $employee = Employee::with(['user', 'company', 'shiftSchedule', 'attendanceRecords', 'leaveRequests'])
            ->findOrFail($id);

        return response()->json($employee);
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_number' => 'nullable|string|unique:employees,employee_number,' . $id,
            'hire_date' => 'nullable|date',
            'termination_date' => 'nullable|date',
            'department' => 'nullable|string',
            'position' => 'nullable|string',
            'employment_type' => 'nullable|in:full_time,part_time,contract,internship',
            'shift_schedule_id' => 'nullable|exists:shift_schedules,id',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive,terminated,on_leave',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $employee->update($request->all());

        return response()->json([
            'message' => 'Employee updated successfully',
            'data' => $employee->load(['user', 'company', 'shiftSchedule'])
        ]);
    }

    /**
     * Remove the specified employee.
     */
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        
        // Soft delete
        $employee->delete();

        return response()->json([
            'message' => 'Employee deleted successfully'
        ]);
    }
}
