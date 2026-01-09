<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectTimesheet;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TimesheetService
{
    /**
     * Create a new timesheet entry
     */
    public function createTimesheet(array $data): ProjectTimesheet
    {
        $employee = Employee::findOrFail($data['employee_id']);
        
        // Calculate cost
        $regularCost = $data['regular_hours'] * $employee->hourly_rate;
        $overtimeCost = $data['overtime_hours'] * $employee->overtime_rate;
        $totalCost = $regularCost + $overtimeCost;
        
        return ProjectTimesheet::create(array_merge($data, [
            'total_hours' => $data['regular_hours'] + $data['overtime_hours'],
            'cost' => $totalCost,
            'status' => 'draft',
        ]));
    }

    /**
     * Bulk create timesheets for multiple employees
     */
    public function bulkCreateTimesheets(array $timesheets): Collection
    {
        $created = collect();
        
        foreach ($timesheets as $timesheetData) {
            $created->push($this->createTimesheet($timesheetData));
        }
        
        return $created;
    }

    /**
     * Submit timesheet for approval
     */
    public function submitTimesheet(ProjectTimesheet $timesheet): void
    {
        $timesheet->update(['status' => 'submitted']);
    }

    /**
     * Approve timesheet
     */
    public function approveTimesheet(ProjectTimesheet $timesheet, int $userId): void
    {
        $timesheet->approve($userId);
    }

    /**
     * Reject timesheet
     */
    public function rejectTimesheet(ProjectTimesheet $timesheet): void
    {
        $timesheet->reject();
    }

    /**
     * Get timesheets pending approval
     */
    public function getPendingTimesheets(Project $project = null)
    {
        $query = ProjectTimesheet::with(['employee', 'project', 'activity'])
            ->where('status', 'submitted')
            ->orderBy('work_date', 'desc');
        
        if ($project) {
            $query->where('project_id', $project->id);
        }
        
        return $query->get();
    }

    /**
     * Get timesheets for a specific period
     */
    public function getTimesheetsForPeriod(Project $project, Carbon $startDate, Carbon $endDate, ?string $status = null)
    {
        $query = $project->timesheets()
            ->with(['employee', 'activity', 'approver'])
            ->whereBetween('work_date', [$startDate, $endDate]);
        
        if ($status) {
            $query->where('status', $status);
        }
        
        return $query->orderBy('work_date', 'desc')->get();
    }

    /**
     * Get employee timesheets
     */
    public function getEmployeeTimesheets(Employee $employee, Carbon $startDate, Carbon $endDate)
    {
        return $employee->timesheets()
            ->with(['project', 'activity'])
            ->whereBetween('work_date', [$startDate, $endDate])
            ->orderBy('work_date', 'desc')
            ->get();
    }

    /**
     * Calculate timesheet summary for project
     */
    public function getProjectTimesheetSummary(Project $project, Carbon $startDate, Carbon $endDate): array
    {
        $timesheets = $project->timesheets()
            ->whereBetween('work_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->get();
        
        return [
            'total_hours' => $timesheets->sum('total_hours'),
            'regular_hours' => $timesheets->sum('regular_hours'),
            'overtime_hours' => $timesheets->sum('overtime_hours'),
            'total_cost' => $timesheets->sum('cost'),
            'entries_count' => $timesheets->count(),
            'employees_count' => $timesheets->unique('employee_id')->count(),
        ];
    }

    /**
     * Calculate employee timesheet summary
     */
    public function getEmployeeTimesheetSummary(Employee $employee, Carbon $startDate, Carbon $endDate): array
    {
        $timesheets = $employee->timesheets()
            ->whereBetween('work_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->get();
        
        return [
            'total_hours' => $timesheets->sum('total_hours'),
            'regular_hours' => $timesheets->sum('regular_hours'),
            'overtime_hours' => $timesheets->sum('overtime_hours'),
            'total_cost' => $timesheets->sum('cost'),
            'entries_count' => $timesheets->count(),
            'projects_count' => $timesheets->unique('project_id')->count(),
        ];
    }

    /**
     * Export timesheets for payroll
     */
    public function exportForPayroll(Carbon $startDate, Carbon $endDate, ?int $companyId = null): Collection
    {
        $query = ProjectTimesheet::with(['employee', 'project'])
            ->where('status', 'approved')
            ->whereBetween('work_date', [$startDate, $endDate]);
        
        if ($companyId) {
            $query->whereHas('employee', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }
        
        $timesheets = $query->get();
        
        // Group by employee
        return $timesheets->groupBy('employee_id')->map(function ($employeeTimesheets, $employeeId) use ($startDate, $endDate) {
            $employee = $employeeTimesheets->first()->employee;
            
            return [
                'employee_id' => $employeeId,
                'employee_code' => $employee->employee_code,
                'employee_name' => $employee->name,
                'period_start' => $startDate->format('Y-m-d'),
                'period_end' => $endDate->format('Y-m-d'),
                'total_hours' => $employeeTimesheets->sum('total_hours'),
                'regular_hours' => $employeeTimesheets->sum('regular_hours'),
                'overtime_hours' => $employeeTimesheets->sum('overtime_hours'),
                'total_cost' => $employeeTimesheets->sum('cost'),
                'entries_count' => $employeeTimesheets->count(),
            ];
        })->values();
    }

    /**
     * Get daily timesheet totals for a project
     */
    public function getDailyTotals(Project $project, Carbon $startDate, Carbon $endDate): Collection
    {
        return $project->timesheets()
            ->whereBetween('work_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->selectRaw('work_date, SUM(total_hours) as hours, SUM(cost) as cost, COUNT(*) as entries')
            ->groupBy('work_date')
            ->orderBy('work_date')
            ->get();
    }
}
