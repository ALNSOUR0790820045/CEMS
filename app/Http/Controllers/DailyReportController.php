<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\DailyReportPhoto;
use App\Models\Project;
use App\Models\ProjectActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DailyReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DailyReport::with(['project', 'preparedBy', 'photos']);

        // Filters
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('report_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('report_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('report_number', 'like', "%{$request->search}%")
                  ->orWhere('work_executed', 'like', "%{$request->search}%");
            });
        }

        $reports = $query->latest('report_date')->paginate(20);
        $projects = Project::all();

        return view('daily-reports.index', compact('reports', 'projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::with('activities')->where('status', 'active')->get();
        $reportNumber = DailyReport::generateReportNumber();
        
        return view('daily-reports.create', compact('projects', 'reportNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'report_date' => 'required|date',
            'weather_condition' => 'nullable|string',
            'temperature' => 'nullable|numeric',
            'humidity' => 'nullable|numeric',
            'site_conditions' => 'nullable|string',
            'work_start_time' => 'nullable|date_format:H:i',
            'work_end_time' => 'nullable|date_format:H:i',
            'total_work_hours' => 'nullable|numeric',
            'workers_count' => 'nullable|integer',
            'workers_breakdown' => 'nullable|json',
            'attendance_notes' => 'nullable|string',
            'equipment_hours' => 'nullable|json',
            'equipment_notes' => 'nullable|string',
            'work_executed' => 'nullable|string',
            'activities_progress' => 'nullable|json',
            'quality_notes' => 'nullable|string',
            'materials_received' => 'nullable|json',
            'materials_notes' => 'nullable|string',
            'problems' => 'nullable|string',
            'delays' => 'nullable|string',
            'safety_incidents' => 'nullable|string',
            'visitors' => 'nullable|json',
            'meetings' => 'nullable|string',
            'instructions_received' => 'nullable|string',
            'general_notes' => 'nullable|string',
        ]);

        $validated['report_number'] = DailyReport::generateReportNumber();
        $validated['prepared_by'] = Auth::id();
        $validated['status'] = $request->input('submit_action') === 'submit' ? 'submitted' : 'draft';
        
        if ($validated['status'] === 'submitted') {
            $validated['prepared_at'] = now();
        }

        $report = DailyReport::create($validated);

        // Handle photo uploads
        if ($request->hasFile('photos')) {
            $this->handlePhotoUploads($request, $report);
        }

        return redirect()->route('daily-reports.show', $report)
            ->with('success', 'تم إنشاء التقرير اليومي بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(DailyReport $dailyReport)
    {
        $dailyReport->load(['project', 'photos.activity', 'preparedBy', 'reviewedBy', 'consultantApprovedBy', 'clientApprovedBy']);
        
        return view('daily-reports.show', compact('dailyReport'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DailyReport $dailyReport)
    {
        if ($dailyReport->status !== 'draft') {
            return redirect()->route('daily-reports.show', $dailyReport)
                ->with('error', 'لا يمكن تعديل التقرير بعد الإرسال');
        }

        $projects = Project::with('activities')->where('status', 'active')->get();
        
        return view('daily-reports.edit', compact('dailyReport', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DailyReport $dailyReport)
    {
        if ($dailyReport->status !== 'draft') {
            return redirect()->route('daily-reports.show', $dailyReport)
                ->with('error', 'لا يمكن تعديل التقرير بعد الإرسال');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'report_date' => 'required|date',
            'weather_condition' => 'nullable|string',
            'temperature' => 'nullable|numeric',
            'humidity' => 'nullable|numeric',
            'site_conditions' => 'nullable|string',
            'work_start_time' => 'nullable|date_format:H:i',
            'work_end_time' => 'nullable|date_format:H:i',
            'total_work_hours' => 'nullable|numeric',
            'workers_count' => 'nullable|integer',
            'workers_breakdown' => 'nullable|json',
            'attendance_notes' => 'nullable|string',
            'equipment_hours' => 'nullable|json',
            'equipment_notes' => 'nullable|string',
            'work_executed' => 'nullable|string',
            'activities_progress' => 'nullable|json',
            'quality_notes' => 'nullable|string',
            'materials_received' => 'nullable|json',
            'materials_notes' => 'nullable|string',
            'problems' => 'nullable|string',
            'delays' => 'nullable|string',
            'safety_incidents' => 'nullable|string',
            'visitors' => 'nullable|json',
            'meetings' => 'nullable|string',
            'instructions_received' => 'nullable|string',
            'general_notes' => 'nullable|string',
        ]);

        if ($request->input('submit_action') === 'submit') {
            $validated['status'] = 'submitted';
            $validated['prepared_at'] = now();
        }

        $dailyReport->update($validated);

        // Handle photo uploads
        if ($request->hasFile('photos')) {
            $this->handlePhotoUploads($request, $dailyReport);
        }

        return redirect()->route('daily-reports.show', $dailyReport)
            ->with('success', 'تم تحديث التقرير اليومي بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DailyReport $dailyReport)
    {
        if ($dailyReport->status !== 'draft') {
            return redirect()->route('daily-reports.index')
                ->with('error', 'لا يمكن حذف التقرير بعد الإرسال');
        }

        // Delete photos from storage
        foreach ($dailyReport->photos as $photo) {
            Storage::disk('public')->delete($photo->photo_path);
        }

        $dailyReport->delete();

        return redirect()->route('daily-reports.index')
            ->with('success', 'تم حذف التقرير بنجاح');
    }

    /**
     * Show sign page
     */
    public function sign(DailyReport $dailyReport)
    {
        if (!$dailyReport->canBeSigned()) {
            return redirect()->route('daily-reports.show', $dailyReport)
                ->with('error', 'التقرير غير جاهز للتوقيع');
        }

        return view('daily-reports.sign', compact('dailyReport'));
    }

    /**
     * Sign the report
     */
    public function signReport(Request $request, DailyReport $dailyReport)
    {
        $request->validate([
            'signature_type' => 'required|in:review,consultant,client',
            'action' => 'required|in:approve,reject',
            'comments' => 'nullable|string',
        ]);

        $user = Auth::user();
        $action = $request->input('action');

        if ($action === 'reject') {
            $dailyReport->update(['status' => 'rejected']);
            return redirect()->route('daily-reports.show', $dailyReport)
                ->with('success', 'تم رفض التقرير');
        }

        // Approve signature
        $signatureType = $request->input('signature_type');
        
        switch ($signatureType) {
            case 'review':
                $dailyReport->update([
                    'reviewed_by' => $user->id,
                    'reviewed_at' => now(),
                ]);
                break;
            case 'consultant':
                $dailyReport->update([
                    'consultant_approved_by' => $user->id,
                    'consultant_approved_at' => now(),
                ]);
                break;
            case 'client':
                $dailyReport->update([
                    'client_approved_by' => $user->id,
                    'client_approved_at' => now(),
                ]);
                break;
        }

        // Update status if fully signed
        if ($dailyReport->isFullySigned()) {
            $dailyReport->update(['status' => 'approved']);
        }

        return redirect()->route('daily-reports.show', $dailyReport)
            ->with('success', 'تم التوقيع بنجاح');
    }

    /**
     * Show photos gallery
     */
    public function photos(Request $request)
    {
        $query = DailyReportPhoto::with(['dailyReport.project', 'activity', 'uploadedBy']);

        // Filters
        if ($request->filled('project_id')) {
            $query->whereHas('dailyReport', function($q) use ($request) {
                $q->where('project_id', $request->project_id);
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        if ($request->filled('date_from')) {
            $query->whereHas('dailyReport', function($q) use ($request) {
                $q->whereDate('report_date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('dailyReport', function($q) use ($request) {
                $q->whereDate('report_date', '<=', $request->date_to);
            });
        }

        $photos = $query->latest()->paginate(24);
        $projects = Project::all();
        $activities = ProjectActivity::all();

        return view('daily-reports.photos', compact('photos', 'projects', 'activities'));
    }

    /**
     * Show weather log
     */
    public function weatherLog(Request $request)
    {
        $query = DailyReport::with('project')
            ->whereNotNull('weather_condition');

        // Filters
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('report_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('report_date', '<=', $request->date_to);
        }

        $reports = $query->orderBy('report_date', 'desc')->paginate(30);
        $projects = Project::all();

        return view('daily-reports.weather-log', compact('reports', 'projects'));
    }

    /**
     * Handle photo uploads
     */
    private function handlePhotoUploads(Request $request, DailyReport $report)
    {
        $photos = $request->file('photos');
        $photoData = $request->input('photo_data', []);

        foreach ($photos as $index => $photo) {
            $path = $photo->store('daily-reports', 'public');
            
            $photoRecord = new DailyReportPhoto([
                'daily_report_id' => $report->id,
                'photo_path' => $path,
                'photo_title' => $photoData[$index]['title'] ?? null,
                'description' => $photoData[$index]['description'] ?? null,
                'latitude' => $photoData[$index]['latitude'] ?? null,
                'longitude' => $photoData[$index]['longitude'] ?? null,
                'captured_at' => $photoData[$index]['captured_at'] ?? now(),
                'device_info' => $photoData[$index]['device_info'] ?? null,
                'category' => $photoData[$index]['category'] ?? 'general',
                'activity_id' => $photoData[$index]['activity_id'] ?? null,
                'location_name' => $photoData[$index]['location_name'] ?? null,
                'uploaded_by' => Auth::id(),
            ]);

            $photoRecord->save();
        }
    }
}
