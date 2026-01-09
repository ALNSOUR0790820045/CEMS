<?php

namespace App\Http\Controllers;

use App\Models\ProjectMilestone;
use App\Models\Project;
use App\Models\ProjectActivity;
use Illuminate\Http\Request;

class ProjectMilestoneController extends Controller
{
    /**
     * Display a listing of milestones
     */
    public function index(Request $request)
    {
        $query = ProjectMilestone::with(['project', 'activity']);

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $milestones = $query->orderBy('target_date')->paginate(30);
        $projects = Project::where('status', '!=', 'cancelled')->get();

        return view('activities.milestones', compact('milestones', 'projects'));
    }

    /**
     * Store a new milestone
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'activity_id' => 'nullable|exists:project_activities,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_date' => 'required|date',
            'actual_date' => 'nullable|date',
            'status' => 'required|in:pending,achieved,missed',
            'type' => 'required|in:project,contractual,payment,technical',
            'is_critical' => 'nullable|boolean',
            'deliverables' => 'nullable|string',
        ]);

        ProjectMilestone::create($validated);

        return back()->with('success', 'تم إضافة المعلم بنجاح');
    }

    /**
     * Update milestone
     */
    public function update(Request $request, ProjectMilestone $milestone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_date' => 'required|date',
            'actual_date' => 'nullable|date',
            'status' => 'required|in:pending,achieved,missed',
            'type' => 'required|in:project,contractual,payment,technical',
            'is_critical' => 'nullable|boolean',
            'deliverables' => 'nullable|string',
        ]);

        $milestone->update($validated);

        return back()->with('success', 'تم تحديث المعلم بنجاح');
    }

    /**
     * Delete milestone
     */
    public function destroy(ProjectMilestone $milestone)
    {
        $milestone->delete();

        return back()->with('success', 'تم حذف المعلم بنجاح');
    }
}
