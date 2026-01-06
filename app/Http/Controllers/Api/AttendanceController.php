<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance records.
     */
    public function index(Request $request)
    {
        $query = AttendanceRecord::with(['employee.user', 'approvedBy']);

        // Filter by company
        if ($request->has('company_id')) {
            $query->forCompany($request->company_id);
        }

        // Filter by employee
        if ($request->has('employee_id')) {
            $query->forEmployee($request->employee_id);
        }

        // Filter by date
        if ($request->has('date')) {
            $query->forDate($request->date);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->forDateRange($request->start_date, $request->end_date);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $attendance = $query->orderBy('attendance_date', 'desc')
            ->orderBy('check_in_time', 'desc')
            ->paginate($perPage);

        return response()->json($attendance);
    }

    /**
     * Store a newly created attendance record.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'check_in_time' => 'nullable|date_format:Y-m-d H:i:s',
            'check_out_time' => 'nullable|date_format:Y-m-d H:i:s',
            'status' => 'required|in:present,absent,on_leave,half_day,weekend,holiday',
            'location' => 'nullable|string',
            'device_id' => 'nullable|string',
            'notes' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $attendance = AttendanceRecord::create($request->all());

        // Calculate work hours if both times are present
        if ($attendance->check_in_time && $attendance->check_out_time) {
            $attendance->calculateWorkHours();
        }

        return response()->json([
            'message' => 'Attendance record created successfully',
            'data' => $attendance->load(['employee.user', 'approvedBy'])
        ], 201);
    }

    /**
     * Check-in endpoint.
     */
    public function checkIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'location' => 'nullable|string',
            'device_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $employee = Employee::findOrFail($request->employee_id);
        $today = Carbon::today()->format('Y-m-d');

        // Check if already checked in today
        $existingAttendance = AttendanceRecord::where('employee_id', $request->employee_id)
            ->whereDate('attendance_date', $today)
            ->first();

        if ($existingAttendance && $existingAttendance->check_in_time) {
            return response()->json([
                'message' => 'Already checked in today',
                'data' => $existingAttendance
            ], 400);
        }

        $checkInTime = now();

        if ($existingAttendance) {
            $existingAttendance->update([
                'check_in_time' => $checkInTime,
                'location' => $request->location,
                'device_id' => $request->device_id,
                'status' => 'present',
            ]);
            $attendance = $existingAttendance;
        } else {
            $attendance = AttendanceRecord::create([
                'employee_id' => $request->employee_id,
                'attendance_date' => $today,
                'check_in_time' => $checkInTime,
                'location' => $request->location,
                'device_id' => $request->device_id,
                'status' => 'present',
                'company_id' => $employee->company_id,
            ]);
        }

        // Calculate late minutes if shift schedule exists
        if ($employee->shiftSchedule) {
            $attendance->calculateLateMinutes($employee->shiftSchedule->start_time);
        }

        return response()->json([
            'message' => 'Checked in successfully',
            'data' => $attendance->load(['employee.user'])
        ], 200);
    }

    /**
     * Check-out endpoint.
     */
    public function checkOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $employee = Employee::findOrFail($request->employee_id);
        $today = Carbon::today()->format('Y-m-d');

        $attendance = AttendanceRecord::where('employee_id', $request->employee_id)
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in_time) {
            return response()->json([
                'message' => 'No check-in record found for today',
            ], 400);
        }

        if ($attendance->check_out_time) {
            return response()->json([
                'message' => 'Already checked out today',
                'data' => $attendance
            ], 400);
        }

        $attendance->update([
            'check_out_time' => now(),
        ]);

        // Calculate work hours
        $attendance->calculateWorkHours();

        // Calculate overtime if shift schedule exists
        if ($employee->shiftSchedule) {
            $attendance->calculateOvertimeHours($employee->shiftSchedule->working_hours);
        }

        return response()->json([
            'message' => 'Checked out successfully',
            'data' => $attendance->load(['employee.user'])
        ], 200);
    }

    /**
     * Display the specified attendance record.
     */
    public function show($id)
    {
        $attendance = AttendanceRecord::with(['employee.user', 'approvedBy'])
            ->findOrFail($id);

        return response()->json($attendance);
    }

    /**
     * Update the specified attendance record.
     */
    public function update(Request $request, $id)
    {
        $attendance = AttendanceRecord::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'check_in_time' => 'nullable|date_format:Y-m-d H:i:s',
            'check_out_time' => 'nullable|date_format:Y-m-d H:i:s',
            'status' => 'nullable|in:present,absent,on_leave,half_day,weekend,holiday',
            'notes' => 'nullable|string',
            'approved_by_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $attendance->update($request->all());

        // Recalculate work hours if times changed
        if ($request->has('check_in_time') || $request->has('check_out_time')) {
            if ($attendance->check_in_time && $attendance->check_out_time) {
                $attendance->calculateWorkHours();
            }
        }

        return response()->json([
            'message' => 'Attendance record updated successfully',
            'data' => $attendance->load(['employee.user', 'approvedBy'])
        ]);
    }

    /**
     * Remove the specified attendance record.
     */
    public function destroy($id)
    {
        $attendance = AttendanceRecord::findOrFail($id);
        $attendance->delete();

        return response()->json([
            'message' => 'Attendance record deleted successfully'
        ]);
    }
}
