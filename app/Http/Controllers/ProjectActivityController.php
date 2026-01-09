<?php

namespace App\Http\Controllers;

use App\Models\ProjectActivity;
use App\Models\Project;
use App\Models\ProjectWbs;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProjectActivity::with(['wbs', 'responsible', 'project']);

        // Filters
        if ($request->filled('wbs_id')) {
            $query->where('wbs_id', $request->wbs_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('responsible_id')) {
            $query->where('responsible_id', $request->responsible_id);
        }

        if ($request->filled('is_critical')) {
            $query->where('is_critical', $request->is_critical);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('activity_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        $activities = $query->orderBy('activity_code')->paginate(50);
        $wbsItems = ProjectWbs::orderBy('wbs_code')->get();
        $users = User::where('is_active', true)->get();

        return view('activities.index', compact('activities', 'wbsItems', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::where('status', '!=', 'cancelled')->get();
        $wbsItems = ProjectWbs::orderBy('wbs_code')->get();
        $users = User::where('is_active', true)->get();

        return view('activities.create', compact('projects', 'wbsItems', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'wbs_id' => 'required|exists:project_wbs,id',
            'activity_code' => 'required|unique:project_activities,activity_code',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'planned_start_date' => 'required|date',
            'planned_end_date' => 'required|date|after_or_equal:planned_start_date',
            'planned_effort_hours' => 'nullable|numeric|min:0',
            'progress_percent' => 'nullable|numeric|min:0|max:100',
            'progress_method' => 'required|in:manual,duration,effort,units',
            'type' => 'required|in:task,milestone,summary',
            'responsible_id' => 'nullable|exists:users,id',
            'status' => 'required|in:not_started,in_progress,completed,on_hold,cancelled',
            'budgeted_cost' => 'nullable|numeric|min:0',
            'priority' => 'required|in:low,medium,high,critical',
            'notes' => 'nullable|string',
        ]);

        $activity = new ProjectActivity($validated);
        $activity->calculatePlannedDuration();
        $activity->save();

        return redirect()->route('activities.index')->with('success', 'تم إضافة النشاط بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProjectActivity $activity)
    {
        $activity->load(['wbs', 'responsible', 'project', 'predecessors', 'successors', 'milestones']);
        
        return view('activities.show', compact('activity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProjectActivity $activity)
    {
        $projects = Project::where('status', '!=', 'cancelled')->get();
        $wbsItems = ProjectWbs::orderBy('wbs_code')->get();
        $users = User::where('is_active', true)->get();

        return view('activities.edit', compact('activity', 'projects', 'wbsItems', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectActivity $activity)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'wbs_id' => 'required|exists:project_wbs,id',
            'activity_code' => 'required|unique:project_activities,activity_code,' . $activity->id,
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'planned_start_date' => 'required|date',
            'planned_end_date' => 'required|date|after_or_equal:planned_start_date',
            'actual_start_date' => 'nullable|date',
            'actual_end_date' => 'nullable|date|after_or_equal:actual_start_date',
            'planned_effort_hours' => 'nullable|numeric|min:0',
            'actual_effort_hours' => 'nullable|numeric|min:0',
            'progress_percent' => 'nullable|numeric|min:0|max:100',
            'progress_method' => 'required|in:manual,duration,effort,units',
            'type' => 'required|in:task,milestone,summary',
            'responsible_id' => 'nullable|exists:users,id',
            'status' => 'required|in:not_started,in_progress,completed,on_hold,cancelled',
            'budgeted_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'priority' => 'required|in:low,medium,high,critical',
            'notes' => 'nullable|string',
        ]);

        $activity->fill($validated);
        $activity->calculatePlannedDuration();
        $activity->calculateActualDuration();
        $activity->calculateProgress();
        $activity->save();

        return redirect()->route('activities.show', $activity)->with('success', 'تم تحديث النشاط بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectActivity $activity)
    {
        $activity->delete();

        return redirect()->route('activities.index')->with('success', 'تم حذف النشاط بنجاح');
    }

    /**
     * Show the progress update form
     */
    public function progressUpdate(ProjectActivity $activity)
    {
        return view('activities.progress-update', compact('activity'));
    }

    /**
     * Update activity progress
     */
    public function updateProgress(Request $request, ProjectActivity $activity)
    {
        $validated = $request->validate([
            'progress_percent' => 'required|numeric|min:0|max:100',
            'actual_start_date' => 'nullable|date',
            'actual_end_date' => 'nullable|date|after_or_equal:actual_start_date',
            'actual_effort_hours' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $activity->fill($validated);
        
        // Auto update status based on progress
        if ($activity->progress_percent == 0) {
            $activity->status = 'not_started';
        } elseif ($activity->progress_percent == 100) {
            $activity->status = 'completed';
        } else {
            $activity->status = 'in_progress';
        }

        $activity->calculateActualDuration();
        $activity->save();

        return redirect()->route('activities.show', $activity)->with('success', 'تم تحديث التقدم بنجاح');
    }
}
