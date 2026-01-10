<?php

namespace App\Http\Controllers;

use App\Models\EotClaim;
use App\Models\Project;
use App\Models\ProjectActivity;
use App\Models\TimeBarClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EotClaimController extends Controller
{
    /**
     * Display the dashboard with KPIs and charts.
     */
    public function dashboard()
    {
        $totalClaims = EotClaim::count();
        $requestedDays = EotClaim::sum('requested_days');
        $approvedDays = EotClaim::sum('approved_days');
        $totalCostsClaimed = EotClaim::sum('total_prolongation_cost');
        
        // Calculate approval rate
        $approvalRate = $requestedDays > 0 ? ($approvedDays / $requestedDays) * 100 : 0;
        
        // EOT by cause
        $eotByCause = EotClaim::select('cause_category', DB::raw('COUNT(*) as count'))
            ->groupBy('cause_category')
            ->get();
        
        // Recent claims
        $recentClaims = EotClaim::with(['project', 'preparedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('eot.dashboard', compact(
            'totalClaims',
            'requestedDays',
            'approvedDays',
            'totalCostsClaimed',
            'approvalRate',
            'eotByCause',
            'recentClaims'
        ));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $eotClaims = EotClaim::with(['project', 'preparedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('eot.index', compact('eotClaims'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::all();
        $timeBarClaims = TimeBarClaim::where('status', 'open')->get();
        
        return view('eot.create', compact('projects', 'timeBarClaims'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'time_bar_claim_id' => 'nullable|exists:time_bar_claims,id',
            'claim_date' => 'required|date',
            'event_start_date' => 'required|date',
            'event_end_date' => 'nullable|date|after_or_equal:event_start_date',
            'event_duration_days' => 'required|integer|min:1',
            'requested_days' => 'required|integer|min:1',
            'cause_category' => 'required|in:client_delay,consultant_delay,variations,unforeseeable_conditions,force_majeure,weather,delays_by_others,suspension,late_drawings,late_approvals,other',
            'event_description' => 'required|string',
            'impact_description' => 'required|string',
            'justification' => 'required|string',
            'fidic_clause_reference' => 'nullable|string',
            'affects_critical_path' => 'boolean',
        ]);

        // Generate EOT number
        $year = date('Y');
        $lastEot = EotClaim::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $number = $lastEot ? intval(substr($lastEot->eot_number, -3)) + 1 : 1;
        $eotNumber = sprintf('EOT-%s-%03d', $year, $number);

        $validated['eot_number'] = $eotNumber;
        $validated['prepared_by'] = Auth::id();
        $validated['status'] = 'draft';

        // Get project dates
        $project = Project::find($validated['project_id']);
        $validated['original_completion_date'] = $project->original_completion_date;
        $validated['current_completion_date'] = $project->current_completion_date;
        $validated['requested_new_completion_date'] = $project->current_completion_date->addDays($validated['requested_days']);

        $eotClaim = EotClaim::create($validated);

        return redirect()->route('eot.show', $eotClaim)
            ->with('success', 'تم إنشاء مطالبة EOT بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(EotClaim $eotClaim)
    {
        $eotClaim->load([
            'project',
            'timeBarClaim',
            'preparedBy',
            'consultantReviewedBy',
            'clientApprovedBy',
            'prolongationCostItems',
            'affectedActivities.activity'
        ]);

        return view('eot.show', compact('eotClaim'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EotClaim $eotClaim)
    {
        // Only allow editing if status is draft
        if ($eotClaim->status !== 'draft') {
            return redirect()->route('eot.show', $eotClaim)
                ->with('error', 'لا يمكن تعديل المطالبة بعد تقديمها');
        }

        $projects = Project::all();
        $timeBarClaims = TimeBarClaim::where('status', 'open')->get();
        
        return view('eot.edit', compact('eotClaim', 'projects', 'timeBarClaims'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EotClaim $eotClaim)
    {
        // Only allow updating if status is draft
        if ($eotClaim->status !== 'draft') {
            return redirect()->route('eot.show', $eotClaim)
                ->with('error', 'لا يمكن تعديل المطالبة بعد تقديمها');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'time_bar_claim_id' => 'nullable|exists:time_bar_claims,id',
            'claim_date' => 'required|date',
            'event_start_date' => 'required|date',
            'event_end_date' => 'nullable|date|after_or_equal:event_start_date',
            'event_duration_days' => 'required|integer|min:1',
            'requested_days' => 'required|integer|min:1',
            'cause_category' => 'required',
            'event_description' => 'required|string',
            'impact_description' => 'required|string',
            'justification' => 'required|string',
            'fidic_clause_reference' => 'nullable|string',
            'affects_critical_path' => 'boolean',
        ]);

        $eotClaim->update($validated);

        return redirect()->route('eot.show', $eotClaim)
            ->with('success', 'تم تحديث المطالبة بنجاح');
    }

    /**
     * Submit claim for review.
     */
    public function submit(EotClaim $eotClaim)
    {
        if ($eotClaim->status !== 'draft') {
            return redirect()->route('eot.show', $eotClaim)
                ->with('error', 'المطالبة مقدمة بالفعل');
        }

        $eotClaim->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('eot.show', $eotClaim)
            ->with('success', 'تم تقديم المطالبة بنجاح');
    }

    /**
     * Show approval form.
     */
    public function approvalForm(EotClaim $eotClaim)
    {
        return view('eot.approve', compact('eotClaim'));
    }

    /**
     * Process approval/rejection.
     */
    public function approve(Request $request, EotClaim $eotClaim)
    {
        $validated = $request->validate([
            'decision' => 'required|in:approve,partial,reject',
            'approved_days' => 'required_if:decision,approve,partial|integer|min:0',
            'rejected_days' => 'nullable|integer|min:0',
            'comments' => 'required|string',
        ]);

        $updates = [];
        
        if ($validated['decision'] === 'approve') {
            $updates['status'] = 'approved';
            $updates['approved_days'] = $eotClaim->requested_days;
            $updates['rejected_days'] = 0;
        } elseif ($validated['decision'] === 'partial') {
            $updates['status'] = 'partially_approved';
            $updates['approved_days'] = $validated['approved_days'];
            $updates['rejected_days'] = $eotClaim->requested_days - $validated['approved_days'];
        } else {
            $updates['status'] = 'rejected';
            $updates['approved_days'] = 0;
            $updates['rejected_days'] = $eotClaim->requested_days;
        }

        // Determine reviewer role
        if ($eotClaim->status === 'submitted') {
            $updates['consultant_reviewed_by'] = Auth::id();
            $updates['consultant_reviewed_at'] = now();
            $updates['consultant_comments'] = $validated['comments'];
            $updates['status'] = 'under_review_consultant';
        } else {
            $updates['client_approved_by'] = Auth::id();
            $updates['client_approved_at'] = now();
            $updates['client_comments'] = $validated['comments'];
        }

        if (isset($updates['approved_days']) && $updates['approved_days'] > 0) {
            $project = $eotClaim->project;
            $updates['approved_new_completion_date'] = $project->current_completion_date->addDays($updates['approved_days']);
        }

        $eotClaim->update($updates);

        return redirect()->route('eot.show', $eotClaim)
            ->with('success', 'تم معالجة المطالبة بنجاح');
    }

    /**
     * Generate report.
     */
    public function report()
    {
        $eotClaims = EotClaim::with(['project', 'preparedBy'])->get();
        
        $statistics = [
            'total_claims' => $eotClaims->count(),
            'requested_days' => $eotClaims->sum('requested_days'),
            'approved_days' => $eotClaims->sum('approved_days'),
            'rejected_days' => $eotClaims->sum('rejected_days'),
            'total_costs' => $eotClaims->sum('total_prolongation_cost'),
        ];

        // Group by cause
        $byCause = $eotClaims->groupBy('cause_category')->map(function ($group) {
            return [
                'count' => $group->count(),
                'requested_days' => $group->sum('requested_days'),
                'approved_days' => $group->sum('approved_days'),
                'approval_rate' => $group->sum('requested_days') > 0 
                    ? ($group->sum('approved_days') / $group->sum('requested_days')) * 100 
                    : 0,
            ];
        });

        // Group by status
        $byStatus = $eotClaims->groupBy('status')->map(function ($group) {
            return $group->count();
        });

        return view('eot.report', compact('eotClaims', 'statistics', 'byCause', 'byStatus'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EotClaim $eotClaim)
    {
        // Only allow deleting if status is draft
        if ($eotClaim->status !== 'draft') {
            return redirect()->route('eot.index')
                ->with('error', 'لا يمكن حذف المطالبة بعد تقديمها');
        }

        $eotClaim->delete();

        return redirect()->route('eot.index')
            ->with('success', 'تم حذف المطالبة بنجاح');
    }
}
