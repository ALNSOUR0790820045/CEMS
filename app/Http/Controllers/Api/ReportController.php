<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\LeaveRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Get attendance summary report.
     */
    public function attendanceSummary(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $query = AttendanceRecord::with(['employee.user'])
            ->forCompany($request->company_id)
            ->forDateRange($request->start_date, $request->end_date);

        if ($request->has('employee_id')) {
            $query->forEmployee($request->employee_id);
        }

        $records = $query->get();

        // Calculate summary statistics
        $summary = [
            'total_days' => $records->count(),
            'present_days' => $records->where('status', 'present')->count(),
            'absent_days' => $records->where('status', 'absent')->count(),
            'on_leave_days' => $records->where('status', 'on_leave')->count(),
            'half_days' => $records->where('status', 'half_day')->count(),
            'total_work_hours' => round($records->sum('work_hours'), 2),
            'total_overtime_hours' => round($records->sum('overtime_hours'), 2),
            'total_late_minutes' => $records->sum('late_minutes'),
            'total_early_leave_minutes' => $records->sum('early_leave_minutes'),
            'average_work_hours' => $records->count() > 0 ? round($records->avg('work_hours'), 2) : 0,
        ];

        return response()->json([
            'summary' => $summary,
            'records' => $records,
            'period' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]
        ]);
    }

    /**
     * Get daily attendance report.
     */
    public function dailyAttendance(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'date' => 'nullable|date',
        ]);

        $date = $request->date ?? Carbon::today()->format('Y-m-d');

        $records = AttendanceRecord::with(['employee.user', 'employee.shiftSchedule'])
            ->forCompany($request->company_id)
            ->forDate($date)
            ->get();

        // Get all active employees for the company
        $allEmployees = Employee::active()
            ->forCompany($request->company_id)
            ->with('user')
            ->get();

        // Find employees without attendance record for the day
        $recordedEmployeeIds = $records->pluck('employee_id')->toArray();
        $absentEmployees = $allEmployees->whereNotIn('id', $recordedEmployeeIds);

        return response()->json([
            'date' => $date,
            'attendance_records' => $records,
            'total_employees' => $allEmployees->count(),
            'present_count' => $records->whereIn('status', ['present', 'half_day'])->count(),
            'absent_count' => $absentEmployees->count(),
            'on_leave_count' => $records->where('status', 'on_leave')->count(),
            'absent_employees' => $absentEmployees->values(),
        ]);
    }

    /**
     * Get monthly attendance summary.
     */
    public function monthlyAttendance(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $startDate = Carbon::create($request->year, $request->month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $records = AttendanceRecord::with(['employee.user'])
            ->forCompany($request->company_id)
            ->forDateRange($startDate, $endDate)
            ->get();

        // Group by employee
        $employeeStats = $records->groupBy('employee_id')->map(function ($employeeRecords) {
            $employee = $employeeRecords->first()->employee;
            return [
                'employee_id' => $employee->id,
                'employee_name' => $employee->user->name,
                'employee_number' => $employee->employee_number,
                'total_days' => $employeeRecords->count(),
                'present_days' => $employeeRecords->where('status', 'present')->count(),
                'absent_days' => $employeeRecords->where('status', 'absent')->count(),
                'on_leave_days' => $employeeRecords->where('status', 'on_leave')->count(),
                'half_days' => $employeeRecords->where('status', 'half_day')->count(),
                'total_work_hours' => round($employeeRecords->sum('work_hours'), 2),
                'total_overtime_hours' => round($employeeRecords->sum('overtime_hours'), 2),
                'total_late_minutes' => $employeeRecords->sum('late_minutes'),
                'average_work_hours' => round($employeeRecords->avg('work_hours'), 2),
            ];
        })->values();

        return response()->json([
            'period' => [
                'year' => $request->year,
                'month' => $request->month,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'employee_statistics' => $employeeStats,
            'overall_summary' => [
                'total_records' => $records->count(),
                'total_work_hours' => round($records->sum('work_hours'), 2),
                'total_overtime_hours' => round($records->sum('overtime_hours'), 2),
            ]
        ]);
    }

    /**
     * Get leave report.
     */
    public function leaveReport(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:pending,approved,rejected,cancelled',
        ]);

        $query = LeaveRequest::with(['employee.user', 'approvedBy'])
            ->forCompany($request->company_id);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('start_date', [$request->start_date, $request->end_date]);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $leaveRequests = $query->orderBy('start_date', 'desc')->get();

        // Statistics by leave type
        $leaveTypeStats = $leaveRequests->groupBy('leave_type')->map(function ($leaves, $type) {
            return [
                'leave_type' => $type,
                'count' => $leaves->count(),
                'total_days' => $leaves->sum('total_days'),
                'approved_count' => $leaves->where('status', 'approved')->count(),
                'pending_count' => $leaves->where('status', 'pending')->count(),
                'rejected_count' => $leaves->where('status', 'rejected')->count(),
            ];
        })->values();

        return response()->json([
            'leave_requests' => $leaveRequests,
            'statistics' => [
                'total_requests' => $leaveRequests->count(),
                'total_days' => $leaveRequests->sum('total_days'),
                'by_status' => [
                    'pending' => $leaveRequests->where('status', 'pending')->count(),
                    'approved' => $leaveRequests->where('status', 'approved')->count(),
                    'rejected' => $leaveRequests->where('status', 'rejected')->count(),
                    'cancelled' => $leaveRequests->where('status', 'cancelled')->count(),
                ],
                'by_leave_type' => $leaveTypeStats,
            ]
        ]);
    }

    /**
     * Get overtime report.
     */
    public function overtimeReport(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $records = AttendanceRecord::with(['employee.user'])
            ->forCompany($request->company_id)
            ->forDateRange($request->start_date, $request->end_date)
            ->where('overtime_hours', '>', 0)
            ->get();

        // Group by employee
        $employeeOvertimeStats = $records->groupBy('employee_id')->map(function ($employeeRecords) {
            $employee = $employeeRecords->first()->employee;
            return [
                'employee_id' => $employee->id,
                'employee_name' => $employee->user->name,
                'employee_number' => $employee->employee_number,
                'department' => $employee->department,
                'total_overtime_hours' => round($employeeRecords->sum('overtime_hours'), 2),
                'overtime_days_count' => $employeeRecords->count(),
                'average_overtime_per_day' => round($employeeRecords->avg('overtime_hours'), 2),
            ];
        })->sortByDesc('total_overtime_hours')->values();

        return response()->json([
            'period' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ],
            'employee_overtime_statistics' => $employeeOvertimeStats,
            'overall_summary' => [
                'total_overtime_hours' => round($records->sum('overtime_hours'), 2),
                'total_employees_with_overtime' => $employeeOvertimeStats->count(),
                'average_overtime_per_employee' => $employeeOvertimeStats->count() > 0 
                    ? round($employeeOvertimeStats->avg('total_overtime_hours'), 2) 
                    : 0,
            ]
        ]);
    }
}
