<?php

namespace App\Http\Controllers;

use App\Models\Laborer;
use App\Models\LaborCategory;
use App\Models\LaborAssignment;
use App\Models\LaborDailyAttendance;
use App\Models\LaborProductivity;
use App\Models\LaborTimesheet;
use App\Models\LaborTimesheetEntry;
use App\Models\Project;
use App\Models\Subcontractor;
use App\Models\BoqItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaborController extends Controller
{
    // ========== Laborers CRUD ==========
    
    public function index()
    {
        $laborers = Laborer::with(['category', 'currentProject', 'subcontractor'])
            ->latest()
            ->get();
        
        return view('labor.index', compact('laborers'));
    }

    public function create()
    {
        $categories = LaborCategory::where('is_active', true)->get();
        $subcontractors = Subcontractor::where('is_active', true)->get();
        $projects = Project::where('is_active', true)->get();
        
        return view('labor.create', compact('categories', 'subcontractors', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'category_id' => 'required|exists:labor_categories,id',
            'nationality' => 'nullable|string|max:100',
            'id_number' => 'nullable|string|max:50',
            'id_expiry_date' => 'nullable|date',
            'passport_number' => 'nullable|string|max:50',
            'passport_expiry_date' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'employment_type' => 'required|in:permanent,temporary,subcontractor',
            'subcontractor_id' => 'nullable|exists:subcontractors,id',
            'joining_date' => 'required|date',
            'contract_end_date' => 'nullable|date',
            'daily_wage' => 'required|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'skills' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Generate labor number
        $lastLaborer = Laborer::orderBy('id', 'desc')->first();
        $nextNumber = $lastLaborer ? (intval(substr($lastLaborer->labor_number, 4)) + 1) : 1;
        $validated['labor_number'] = 'LBR-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        Laborer::create($validated);

        return redirect()->route('labor.index')
            ->with('success', 'تم إضافة العامل بنجاح');
    }

    public function show(Laborer $laborer)
    {
        $laborer->load([
            'category',
            'currentProject',
            'subcontractor',
            'assignments.project',
            'attendance' => function($query) {
                $query->latest('attendance_date')->limit(10);
            }
        ]);
        
        return view('labor.show', compact('laborer'));
    }

    public function edit(Laborer $laborer)
    {
        $categories = LaborCategory::where('is_active', true)->get();
        $subcontractors = Subcontractor::where('is_active', true)->get();
        $projects = Project::where('is_active', true)->get();
        
        return view('labor.edit', compact('laborer', 'categories', 'subcontractors', 'projects'));
    }

    public function update(Request $request, Laborer $laborer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'category_id' => 'required|exists:labor_categories,id',
            'nationality' => 'nullable|string|max:100',
            'id_number' => 'nullable|string|max:50',
            'id_expiry_date' => 'nullable|date',
            'passport_number' => 'nullable|string|max:50',
            'passport_expiry_date' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'employment_type' => 'required|in:permanent,temporary,subcontractor',
            'subcontractor_id' => 'nullable|exists:subcontractors,id',
            'joining_date' => 'required|date',
            'contract_end_date' => 'nullable|date',
            'daily_wage' => 'required|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,assigned,on_leave,sick,terminated',
            'current_project_id' => 'nullable|exists:projects,id',
            'skills' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $laborer->update($validated);

        return redirect()->route('labor.index')
            ->with('success', 'تم تحديث بيانات العامل بنجاح');
    }

    public function destroy(Laborer $laborer)
    {
        $laborer->delete();
        
        return redirect()->route('labor.index')
            ->with('success', 'تم حذف العامل بنجاح');
    }

    // ========== Assignment ==========
    
    public function assign(Request $request, Laborer $laborer)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'assignment_date' => 'required|date',
            'expected_end_date' => 'nullable|date',
            'work_scope' => 'nullable|string',
        ]);

        $validated['laborer_id'] = $laborer->id;
        $validated['assigned_by'] = Auth::id();

        LaborAssignment::create($validated);

        // Update laborer status and current project
        $laborer->update([
            'status' => 'assigned',
            'current_project_id' => $validated['project_id'],
        ]);

        return back()->with('success', 'تم تخصيص العامل للمشروع بنجاح');
    }

    public function projectLaborers($projectId)
    {
        $project = Project::findOrFail($projectId);
        $laborers = Laborer::where('current_project_id', $projectId)
            ->with(['category', 'subcontractor'])
            ->get();
        
        return view('labor.project-laborers', compact('project', 'laborers'));
    }

    // ========== Attendance ==========
    
    public function attendance()
    {
        $projects = Project::where('is_active', true)->get();
        $date = request('date', now()->format('Y-m-d'));
        
        $attendance = LaborDailyAttendance::with(['laborer', 'project'])
            ->whereDate('attendance_date', $date)
            ->get();
        
        return view('labor.attendance', compact('projects', 'date', 'attendance'));
    }

    public function storeAttendance(Request $request)
    {
        $validated = $request->validate([
            'laborer_id' => 'required|exists:laborers,id',
            'project_id' => 'required|exists:projects,id',
            'attendance_date' => 'required|date',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'regular_hours' => 'required|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'status' => 'required|in:present,absent,half_day,leave,sick',
            'work_area' => 'nullable|string',
            'work_description' => 'nullable|string',
        ]);

        $validated['total_hours'] = $validated['regular_hours'] + ($validated['overtime_hours'] ?? 0);
        $validated['recorded_by'] = Auth::id();

        LaborDailyAttendance::updateOrCreate(
            [
                'laborer_id' => $validated['laborer_id'],
                'attendance_date' => $validated['attendance_date']
            ],
            $validated
        );

        return back()->with('success', 'تم تسجيل الحضور بنجاح');
    }

    public function attendanceByDate($date)
    {
        $attendance = LaborDailyAttendance::with(['laborer', 'project'])
            ->whereDate('attendance_date', $date)
            ->get();
        
        return response()->json($attendance);
    }

    // ========== Timesheets ==========
    
    public function timesheets()
    {
        $timesheets = LaborTimesheet::with(['project', 'preparedByUser'])
            ->latest()
            ->get();
        
        return view('labor.timesheets', compact('timesheets'));
    }

    public function storeTimesheet(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'week_start_date' => 'required|date',
            'week_end_date' => 'required|date|after:week_start_date',
            'notes' => 'nullable|string',
        ]);

        // Generate timesheet number
        $lastTimesheet = LaborTimesheet::orderBy('id', 'desc')->first();
        $nextNumber = $lastTimesheet ? (intval(substr($lastTimesheet->timesheet_number, 3)) + 1) : 1;
        $validated['timesheet_number'] = 'TS-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        
        $validated['prepared_by'] = Auth::id();

        $timesheet = LaborTimesheet::create($validated);

        return redirect()->route('labor.timesheets')
            ->with('success', 'تم إنشاء الجدول الزمني بنجاح');
    }

    public function approveTimesheet(Request $request, LaborTimesheet $timesheet)
    {
        $timesheet->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'تم اعتماد الجدول الزمني بنجاح');
    }

    // ========== Productivity ==========
    
    public function productivity()
    {
        $projects = Project::where('is_active', true)->get();
        $productivityRecords = LaborProductivity::with(['project', 'boqItem'])
            ->latest('date')
            ->paginate(20);
        
        return view('labor.productivity', compact('projects', 'productivityRecords'));
    }

    public function storeProductivity(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'boq_item_id' => 'nullable|exists:boq_items,id',
            'date' => 'required|date',
            'activity_description' => 'required|string',
            'quantity_achieved' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'labor_count' => 'required|integer|min:1',
            'total_hours' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Calculate productivity rate
        $validated['productivity_rate'] = $validated['total_hours'] > 0 
            ? $validated['quantity_achieved'] / $validated['total_hours'] 
            : 0;
        
        $validated['recorded_by'] = Auth::id();

        LaborProductivity::create($validated);

        return back()->with('success', 'تم تسجيل الإنتاجية بنجاح');
    }

    // ========== Statistics ==========
    
    public function statistics()
    {
        $stats = [
            'total_laborers' => Laborer::where('is_active', true)->count(),
            'available_laborers' => Laborer::where('status', 'available')->count(),
            'assigned_laborers' => Laborer::where('status', 'assigned')->count(),
            'on_leave' => Laborer::where('status', 'on_leave')->count(),
            'sick' => Laborer::where('status', 'sick')->count(),
        ];

        return response()->json($stats);
    }

    // ========== Expiring Documents ==========
    
    public function expiringDocuments()
    {
        $daysThreshold = 30;
        $thresholdDate = now()->addDays($daysThreshold);

        $expiringIds = Laborer::where('is_active', true)
            ->where(function($query) use ($thresholdDate) {
                $query->where(function($q) use ($thresholdDate) {
                    $q->whereNotNull('id_expiry_date')
                      ->whereDate('id_expiry_date', '<=', $thresholdDate);
                })
                ->orWhere(function($q) use ($thresholdDate) {
                    $q->whereNotNull('passport_expiry_date')
                      ->whereDate('passport_expiry_date', '<=', $thresholdDate);
                })
                ->orWhere(function($q) use ($thresholdDate) {
                    $q->whereNotNull('safety_training_expiry')
                      ->whereDate('safety_training_expiry', '<=', $thresholdDate);
                });
            })
            ->with(['category', 'currentProject'])
            ->get();

        return response()->json($expiringIds);
    }

    // ========== Reports ==========
    
    public function reports()
    {
        $projects = Project::where('is_active', true)->get();
        $categories = LaborCategory::where('is_active', true)->get();
        
        return view('labor.reports', compact('projects', 'categories'));
    }
}
