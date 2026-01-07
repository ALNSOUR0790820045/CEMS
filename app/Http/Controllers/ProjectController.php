<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Currency;
use App\Models\City;
use App\Models\Country;
use App\Models\User;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Project::with(['client', 'currency', 'projectManager', 'city', 'country']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('project_status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('project_manager_id')) {
            $query->where('project_manager_id', $request->project_manager_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('project_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $projects = $query->latest()->paginate(15);
        $clients = Client:: active()->get();
        $projectManagers = User::where('is_active', true)->get();

        return view('projects.index', compact('projects', 'clients', 'projectManagers'));
    }

    /**
     * Show the form for creating a new resource. 
     */
    public function create()
    {
        $projectCode = Project::generateProjectCode();
        $clients = Client::active()->get();
        $contracts = Contract::active()->get();
        $currencies = Currency::active()->get();
        $countries = Country::active()->get();
        $cities = City::active()->get();
        $users = User::where('is_active', true)->get();

        return view('projects. create', compact(
            'projectCode',
            'clients',
            'contracts',
            'currencies',
            'countries',
            'cities',
            'users'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $validated = $request->validated();
        
        // Generate project code
        $validated['project_code'] = Project::generateProjectCode();
        
        // Calculate contract duration
        $startDate = Carbon::parse($validated['contract_start_date']);
        $endDate = Carbon::parse($validated['contract_end_date']);
        $validated['contract_duration_days'] = $startDate->diffInDays($endDate);
        
        // Add company_id from authenticated user
        if (!auth()->user()->company_id) {
            return redirect()->back()
                ->withErrors(['error' => 'لم يتم تعيين شركة للمستخدم. يرجى الاتصال بالمسؤول. '])
                ->withInput();
        }
        $validated['company_id'] = auth()->user()->company_id;
        
        // Handle checkbox
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $project = Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'تم إضافة المشروع بنجاح');
    }

    /**
     * Display the specified resource. 
     */
    public function show(Project $project)
    {
        $project->load([
            'client',
            'contract',
            'currency',
            'city',
            'country',
            'projectManager',
            'siteEngineer',
            'contractManager',
            'company'
        ]);

        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $clients = Client::active()->get();
        $contracts = Contract::active()->get();
        $currencies = Currency::active()->get();
        $countries = Country::active()->get();
        $cities = City::active()->get();
        $users = User::where('is_active', true)->get();

        return view('projects. edit', compact(
            'project',
            'clients',
            'contracts',
            'currencies',
            'countries',
            'cities',
            'users'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $validated = $request->validated();
        
        // Calculate contract duration
        $startDate = Carbon::parse($validated['contract_start_date']);
        $endDate = Carbon::parse($validated['contract_end_date']);
        $validated['contract_duration_days'] = $startDate->diffInDays($endDate);
        
        // Handle checkbox
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $project->update($validated);

        return redirect()->route('projects.index')
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
     * Generate a new project code (API endpoint).
     */
    public function generateCode()
    {
        return response()->json([
            'code' => Project:: generateProjectCode()
        ]);
    }
}