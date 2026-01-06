<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::with(['client', 'projectManager'])
            ->latest()
            ->get();
        
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();
        
        return view('projects.create', compact('clients', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'type' => 'required|in:building,infrastructure,industrial,maintenance,fit_out,other',
            'category' => 'required|in:new_construction,renovation,expansion,maintenance',
            'location' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'region' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'commencement_date' => 'required|date',
            'original_completion_date' => 'required|date|after:commencement_date',
            'original_duration_days' => 'required|integer|min:1',
            'original_contract_value' => 'required|numeric|min:0',
            'revised_contract_value' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'project_manager_id' => 'nullable|exists:users,id',
            'site_engineer_id' => 'nullable|exists:users,id',
            'status' => 'required|in:not_started,mobilization,in_progress,on_hold,suspended,completed,handed_over,final_handover,closed,terminated',
            'health' => 'required|in:on_track,at_risk,delayed,critical',
            'priority' => 'required|in:low,medium,high,critical',
        ]);

        // Generate project number
        $year = date('Y');
        $lastProject = Project::whereYear('created_at', $year)->latest('id')->first();
        $sequence = $lastProject ? intval(substr($lastProject->project_number, -4)) + 1 : 1;
        $validated['project_number'] = sprintf('PRJ-%s-%04d', $year, $sequence);
        
        $validated['created_by'] = auth()->id();

        $project = Project::create($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'تم إنشاء المشروع بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load([
            'client',
            'projectManager',
            'siteEngineer',
            'quantitySurveyor',
            'phases',
            'milestones',
            'team.user',
            'progressReports' => function($query) {
                $query->latest()->limit(5);
            },
            'issues' => function($query) {
                $query->whereIn('status', ['open', 'in_progress'])->latest();
            }
        ]);

        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $clients = Client::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();
        
        return view('projects.edit', compact('project', 'clients', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'type' => 'required|in:building,infrastructure,industrial,maintenance,fit_out,other',
            'category' => 'required|in:new_construction,renovation,expansion,maintenance',
            'location' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'region' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'commencement_date' => 'required|date',
            'original_completion_date' => 'required|date',
            'revised_completion_date' => 'nullable|date',
            'original_duration_days' => 'required|integer|min:1',
            'original_contract_value' => 'required|numeric|min:0',
            'revised_contract_value' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'project_manager_id' => 'nullable|exists:users,id',
            'site_engineer_id' => 'nullable|exists:users,id',
            'status' => 'required|in:not_started,mobilization,in_progress,on_hold,suspended,completed,handed_over,final_handover,closed,terminated',
            'health' => 'required|in:on_track,at_risk,delayed,critical',
            'priority' => 'required|in:low,medium,high,critical',
            'physical_progress' => 'nullable|numeric|min:0|max:100',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'تم تحديث المشروع بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();
        
        return redirect()->route('projects.index')
            ->with('success', 'تم حذف المشروع بنجاح');
    }

    /**
     * Display project dashboard.
     */
    public function dashboard(Project $project)
    {
        $project->load([
            'client',
            'projectManager',
            'phases',
            'milestones',
            'progressReports' => function($query) {
                $query->latest()->limit(10);
            },
            'issues' => function($query) {
                $query->whereIn('status', ['open', 'in_progress']);
            }
        ]);

        return view('projects.show', compact('project'));
    }

    /**
     * Display project progress reports.
     */
    public function progress(Project $project)
    {
        $reports = $project->progressReports()
            ->with(['preparedBy', 'approvedBy'])
            ->latest('report_date')
            ->paginate(20);

        return view('projects.progress', compact('project', 'reports'));
    }

    /**
     * Store a new progress report.
     */
    public function storeProgress(Request $request, Project $project)
    {
        $validated = $request->validate([
            'report_date' => 'required|date',
            'period_type' => 'required|in:daily,weekly,monthly',
            'physical_progress' => 'required|numeric|min:0|max:100',
            'planned_progress' => 'required|numeric|min:0|max:100',
            'work_done' => 'nullable|string',
            'planned_work' => 'nullable|string',
            'issues' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'manpower_count' => 'required|integer|min:0',
            'equipment_count' => 'required|integer|min:0',
            'weather' => 'required|in:sunny,cloudy,rainy,sandstorm',
        ]);

        $lastReport = $project->progressReports()->latest('report_number')->first();
        $validated['report_number'] = $lastReport ? $lastReport->report_number + 1 : 1;
        $validated['variance'] = $validated['physical_progress'] - $validated['planned_progress'];
        $validated['prepared_by'] = auth()->id();
        $validated['project_id'] = $project->id;

        $project->progressReports()->create($validated);

        // Update project physical progress
        $project->update(['physical_progress' => $validated['physical_progress']]);

        return redirect()->route('projects.progress', $project)
            ->with('success', 'تم إضافة تقرير التقدم بنجاح');
    }

    /**
     * Display project team.
     */
    public function team(Project $project)
    {
        $team = $project->team()
            ->with('user')
            ->where('is_active', true)
            ->get();

        $users = User::where('is_active', true)->get();

        return view('projects.team', compact('project', 'team', 'users'));
    }

    /**
     * Display project milestones.
     */
    public function milestones(Project $project)
    {
        $milestones = $project->milestones()
            ->with('phase')
            ->orderBy('target_date')
            ->get();

        return view('projects.milestones', compact('project', 'milestones'));
    }

    /**
     * Display project issues.
     */
    public function issues(Project $project)
    {
        $issues = $project->issues()
            ->with(['assignedTo', 'reportedBy'])
            ->latest('identified_date')
            ->paginate(20);

        return view('projects.issues', compact('project', 'issues'));
    }

    /**
     * Display portfolio view.
     */
    public function portfolio()
    {
        $projects = Project::with(['client', 'projectManager'])
            ->whereIn('status', ['mobilization', 'in_progress'])
            ->get();

        $stats = [
            'total_projects' => Project::count(),
            'active_projects' => Project::whereIn('status', ['mobilization', 'in_progress'])->count(),
            'completed_projects' => Project::where('status', 'completed')->count(),
            'total_value' => Project::sum('revised_contract_value'),
        ];

        return view('projects.portfolio', compact('projects', 'stats'));
    }

    /**
     * Get projects statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_projects' => Project::count(),
            'by_status' => Project::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get(),
            'by_health' => Project::selectRaw('health, COUNT(*) as count')
                ->groupBy('health')
                ->get(),
            'by_type' => Project::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
            'total_value' => Project::sum('revised_contract_value'),
            'avg_progress' => Project::avg('physical_progress'),
        ];

        return response()->json($stats);
    }
}
