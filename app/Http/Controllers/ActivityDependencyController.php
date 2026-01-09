<?php

namespace App\Http\Controllers;

use App\Models\ActivityDependency;
use App\Models\ProjectActivity;
use Illuminate\Http\Request;

class ActivityDependencyController extends Controller
{
    /**
     * Display a listing of dependencies
     */
    public function index(Request $request)
    {
        $query = ActivityDependency::with(['predecessor', 'successor']);

        // Filter by project if provided
        if ($request->filled('project_id')) {
            $query->whereHas('predecessor', function($q) use ($request) {
                $q->where('project_id', $request->project_id);
            });
        }

        $dependencies = $query->paginate(50);
        $activities = ProjectActivity::orderBy('activity_code')->get();

        return view('activities.dependencies', compact('dependencies', 'activities'));
    }

    /**
     * Store a new dependency
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'predecessor_id' => 'required|exists:project_activities,id',
            'successor_id' => 'required|exists:project_activities,id|different:predecessor_id',
            'type' => 'required|in:FS,SS,FF,SF',
            'lag_days' => 'nullable|integer',
        ]);

        try {
            ActivityDependency::create($validated);
            return back()->with('success', 'تم إضافة العلاقة بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove a dependency
     */
    public function destroy(ActivityDependency $dependency)
    {
        $dependency->delete();

        return back()->with('success', 'تم حذف العلاقة بنجاح');
    }
}
