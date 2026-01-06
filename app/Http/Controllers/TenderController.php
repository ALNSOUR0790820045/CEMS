<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use App\Models\Project;
use App\Models\Tender;
use App\Models\TenderTimeline;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TenderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Tender::with(['company']);

        // Check if Client model exists
        if (class_exists('\App\Models\Client')) {
            $query->with(['client', 'assignedTo', 'createdBy']);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority != '') {
            $query->where('priority', $request->priority);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('tender_number', 'like', "%{$search}%")
                    ->orWhere('tender_code', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        $tenders = $query->latest()->paginate(20);

        return view('tenders.index', compact('tenders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [];
        
        if (class_exists('\App\Models\Client')) {
            $data['clients'] = Client::where('is_active', true)->get();
        }
        
        if (class_exists('\App\Models\User')) {
            $data['users'] = User::where('is_active', true)->get();
        }

        return view('tenders.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'tender_code' => 'nullable|unique:tenders,tender_code',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'nullable|exists: clients,id',
            'client_name' => 'nullable|string|max:255',
            'type' => 'nullable|in: public,private,limited,direct_order',
            'category' => 'nullable|in: building,infrastructure,industrial,maintenance,supply,other',
            'sector' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'submission_deadline' => 'nullable|date',
            'submission_date' => 'nullable|date',
            'submission_time' => 'nullable',
            'announcement_date' => 'nullable|date',
            'documents_deadline' => 'nullable|date',
            'questions_deadline' => 'nullable|date',
            'opening_date' => 'nullable|date',
            'expected_award_date' => 'nullable|date',
            'project_start_date' => 'nullable|date',
            'project_duration_days' => 'nullable|integer|min:1',
            'estimated_value' => 'nullable|numeric',
            'documents_cost' => 'nullable|numeric',
            'bid_bond_amount' => 'nullable|numeric',
            'bid_bond_percentage' => 'nullable|numeric',
            'currency' => 'nullable|string|max: 3',
            'priority' => 'nullable|in: low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
            'estimator_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:draft,identified,studying,go,no_go,documents_purchased,pricing,submitted,opened,negotiating,won,lost,cancelled,converted',
        ]);

        // Generate tender number if not provided
        if (empty($validated['tender_code'])) {
            $year = now()->year;
            $lastTender = Tender::whereYear('created_at', $year)->latest('id')->first();
            $nextNumber = $lastTender ? intval(substr($lastTender->tender_number ??  $lastTender->tender_code ??  '0000', -4)) + 1 : 1;
            $validated['tender_number'] = 'TND-'.$year.'-'.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            
            if (! isset($validated['tender_code'])) {
                $validated['tender_code'] = $validated['tender_number'];
            }
        }

        if (Auth::check()) {
            $validated['created_by'] = Auth:: id();
        }
        
        if (! isset($validated['status'])) {
            $validated['status'] = 'identified';
        }

        $tender = Tender::create($validated);

        // Add timeline entry if model exists
        if (class_exists('\App\Models\TenderTimeline') && Auth::check()) {
            TenderTimeline::create([
                'tender_id' => $tender->id,
                'action' => 'created',
                'description' => 'تم إنشاء المناقصة',
                'performed_by' => Auth::id(),
            ]);
        }

        return redirect()->route('tenders.show', $tender)
            ->with('success', 'تم إضافة المناقصة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tender = Tender::with(['wbsItems', 'activities', 'milestones', 'company'])
            ->findOrFail($id);

        // Load additional relationships if models exist
        if (class_exists('\App\Models\Client')) {
            $tender->load([
                'client',
                'assignedTo',
                'estimator',
                'createdBy',
            ]);
        }

        if (class_exists('\App\Models\TenderDocument')) {
            $tender->load('documents. uploadedBy');
        }

        if (class_exists('\App\Models\TenderCompetitor')) {
            $tender->load('competitors');
        }

        if (class_exists('\App\Models\TenderTimeline')) {
            $tender->load('timeline. performedBy');
        }

        if (class_exists('\App\Models\TenderQuestion')) {
            $tender->load('questions');
        }

        return view('tenders.show', compact('tender'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tender = Tender::findOrFail($id);
        
        $data = ['tender' => $tender];
        
        if (class_exists('\App\Models\Client')) {
            $data['clients'] = Client::where('is_active', true)->get();
        }
        
        if (class_exists('\App\Models\User')) {
            $data['users'] = User::where('is_active', true)->get();
        }

        return view('tenders.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tender = Tender::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'tender_code' => 'nullable|unique:tenders,tender_code,' . $id,
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',
            'client_name' => 'nullable|string|max:255',
            'type' => 'nullable|in:public,private,limited,direct_order',
            'category' => 'nullable|in:building,infrastructure,industrial,maintenance,supply,other',
            'sector' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'submission_deadline' => 'nullable|date',
            'submission_date' => 'nullable|date',
            'submission_time' => 'nullable',
            'announcement_date' => 'nullable|date',
            'documents_deadline' => 'nullable|date',
            'questions_deadline' => 'nullable|date',
            'opening_date' => 'nullable|date',
            'expected_award_date' => 'nullable|date',
            'project_start_date' => 'nullable|date',
            'project_duration_days' => 'nullable|integer|min:1',
            'estimated_value' => 'nullable|numeric',
            'documents_cost' => 'nullable|numeric',
            'bid_bond_amount' => 'nullable|numeric',
            'bid_bond_percentage' => 'nullable|numeric',
            'currency' => 'nullable|string|max:3',
            'priority' => 'nullable|in:low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
            'estimator_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:draft,identified,studying,go,no_go,documents_purchased,pricing,submitted,opened,negotiating,won,lost,cancelled,converted',
        ]);

        $oldStatus = $tender->status;
        $tender->update($validated);

        // Add timeline if status changed
        if (class_exists('\App\Models\TenderTimeline') && Auth::check() && isset($validated['status']) && $oldStatus !== $validated['status']) {
            TenderTimeline::create([
                'tender_id' => $tender->id,
                'action' => 'status_change',
                'from_status' => $oldStatus,
                'to_status' => $validated['status'],
                'description' => 'تم تغيير حالة المناقصة',
                'performed_by' => Auth::id(),
            ]);
        }

        return redirect()->route('tenders.show', $tender)
            ->with('success', 'تم تحديث المناقصة بنجاح');
    }

    /**
     * Remove the specified resource from storage. 
     */
    public function destroy($id)
    {
        try {
            $tender = Tender::findOrFail($id);
            $tender->delete();

            return redirect()->route('tenders.index')
                ->with('success', 'تم حذف المناقصة بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete tender: ' . $e->getMessage());
        }
    }
}