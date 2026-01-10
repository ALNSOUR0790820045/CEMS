<?php

namespace App\Http\Controllers\Progress;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTimesheet;
use App\Models\Employee;
use App\Services\TimesheetService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TimesheetController extends Controller
{
    protected $timesheetService;

    public function __construct(TimesheetService $timesheetService)
    {
        $this->timesheetService = $timesheetService;
    }

    /**
     * Display timesheets for a project
     */
    public function index(Request $request, Project $project)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());
        
        $timesheets = $this->timesheetService->getTimesheetsForPeriod(
            $project,
            Carbon::parse($startDate),
            Carbon::parse($endDate)
        );
        
        $summary = $this->timesheetService->getProjectTimesheetSummary(
            $project,
            Carbon::parse($startDate),
            Carbon::parse($endDate)
        );
        
        $employees = Employee::active()->get();
        $activities = $project->activities;
        
        return view('progress.timesheets', compact(
            'project',
            'timesheets',
            'summary',
            'employees',
            'activities',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Store a new timesheet
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'activity_id' => 'nullable|exists:project_activities,id',
            'work_date' => 'required|date',
            'regular_hours' => 'required|numeric|min:0|max:24',
            'overtime_hours' => 'nullable|numeric|min:0|max:24',
            'work_description' => 'nullable|string',
            'progress_achieved' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);
        
        $validated['project_id'] = $project->id;
        $validated['overtime_hours'] = $validated['overtime_hours'] ?? 0;
        
        $timesheet = $this->timesheetService->createTimesheet($validated);
        
        return redirect()
            ->route('progress.timesheets.index', $project)
            ->with('success', 'تم إضافة ورقة العمل بنجاح');
    }

    /**
     * Bulk create timesheets
     */
    public function bulkStore(Request $request, Project $project)
    {
        $validated = $request->validate([
            'timesheets' => 'required|array',
            'timesheets.*.employee_id' => 'required|exists:employees,id',
            'timesheets.*.activity_id' => 'nullable|exists:project_activities,id',
            'timesheets.*.work_date' => 'required|date',
            'timesheets.*.regular_hours' => 'required|numeric|min:0|max:24',
            'timesheets.*.overtime_hours' => 'nullable|numeric|min:0|max:24',
        ]);
        
        foreach ($validated['timesheets'] as &$timesheet) {
            $timesheet['project_id'] = $project->id;
            $timesheet['overtime_hours'] = $timesheet['overtime_hours'] ?? 0;
        }
        
        $this->timesheetService->bulkCreateTimesheets($validated['timesheets']);
        
        return redirect()
            ->route('progress.timesheets.index', $project)
            ->with('success', 'تم إضافة أوراق العمل بنجاح');
    }

    /**
     * Submit timesheet for approval
     */
    public function submit(ProjectTimesheet $timesheet)
    {
        $this->timesheetService->submitTimesheet($timesheet);
        
        return back()->with('success', 'تم تقديم ورقة العمل للموافقة');
    }

    /**
     * Approve timesheet
     */
    public function approve(ProjectTimesheet $timesheet)
    {
        $this->timesheetService->approveTimesheet($timesheet, auth()->id());
        
        return back()->with('success', 'تم الموافقة على ورقة العمل');
    }

    /**
     * Reject timesheet
     */
    public function reject(ProjectTimesheet $timesheet)
    {
        $this->timesheetService->rejectTimesheet($timesheet);
        
        return back()->with('success', 'تم رفض ورقة العمل');
    }

    /**
     * Show pending timesheets for approval
     */
    public function pending(Request $request)
    {
        $project = $request->has('project_id') 
            ? Project::find($request->project_id) 
            : null;
        
        $timesheets = $this->timesheetService->getPendingTimesheets($project);
        
        return view('progress.timesheets-pending', compact('timesheets', 'project'));
    }

    /**
     * Export timesheets for payroll
     */
    public function exportPayroll(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));
        
        $data = $this->timesheetService->exportForPayroll($startDate, $endDate);
        
        return response()->json($data);
    }
}
