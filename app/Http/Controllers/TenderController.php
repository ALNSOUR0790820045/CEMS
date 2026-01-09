<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use App\Models\Country;
use App\Models\City;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TenderController extends Controller
{
    public function index(Request $request)
    {
        $query = Tender::with(['country', 'city', 'currency', 'assignedUser']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tender_type')) {
            $query->where('tender_type', $request->tender_type);
        }

        if ($request->filled('date_from')) {
            $query->where('submission_deadline', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('submission_deadline', '<=', $request->date_to);
        }

        $tenders = $query->orderBy('submission_deadline', 'asc')->paginate(15);

        return view('tenders.index', compact('tenders'));
    }

    public function dashboard()
    {
        $activeTenders = Tender::whereIn('status', ['announced', 'evaluating', 'decision_pending', 'preparing'])->count();
        $preparingTenders = Tender::where('status', 'preparing')->count();
        $wonTenders = Tender::where('status', 'awarded')->count();
        $lostTenders = Tender::where('status', 'lost')->count();
        
        $totalParticipated = $wonTenders + $lostTenders;
        $winRate = $totalParticipated > 0 ? round(($wonTenders / $totalParticipated) * 100, 1) : 0;
        
        $pipelineValue = Tender::whereIn('status', ['announced', 'evaluating', 'decision_pending', 'preparing'])
            ->sum('estimated_value');

        // Upcoming deadlines
        $upcomingTenders = Tender::whereIn('status', ['announced', 'evaluating', 'decision_pending', 'preparing'])
            ->where('submission_deadline', '>=', Carbon::now())
            ->orderBy('submission_deadline', 'asc')
            ->take(10)
            ->get();

        // Tenders by type
        $tendersByType = Tender::selectRaw('tender_type, count(*) as count')
            ->groupBy('tender_type')
            ->get();

        // Recent tenders
        $recentTenders = Tender::with(['country', 'currency', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('tenders.dashboard', compact(
            'activeTenders',
            'preparingTenders',
            'winRate',
            'pipelineValue',
            'upcomingTenders',
            'tendersByType',
            'recentTenders'
        ));
    }

    public function create()
    {
        $countries = Country::where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();
        $users = User::all();

        return view('tenders.create', compact('countries', 'currencies', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tender_name' => 'required|string|max:255',
            'description' => 'required|string',
            'owner_name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'tender_type' => 'required|in:construction,infrastructure,buildings,roads,bridges,water,electrical,mechanical,maintenance,consultancy,other',
            'contract_type' => 'required|in:lump_sum,unit_price,cost_plus,time_material,design_build,epc,bot,other',
            'currency_id' => 'required|exists:currencies,id',
            'submission_deadline' => 'required|date',
        ]);

        $tender = Tender::create($validated);

        return redirect()->route('tenders.show', $tender)
            ->with('success', 'تم إنشاء العطاء بنجاح');
    }

    public function show(Tender $tender)
    {
        $tender->load([
            'country',
            'city',
            'currency',
            'decider',
            'assignedUser',
            'siteVisits.reporter',
            'clarifications.asker',
            'competitors',
            'committeeDecisions.chairman'
        ]);

        return view('tenders.show', compact('tender'));
    }

    public function edit(Tender $tender)
    {
        $countries = Country::where('is_active', true)->get();
        $currencies = Currency::where('is_active', true)->get();
        $users = User::all();

        return view('tenders.edit', compact('tender', 'countries', 'currencies', 'users'));
    }

    public function update(Request $request, Tender $tender)
    {
        $validated = $request->validate([
            'tender_name' => 'required|string|max:255',
            'description' => 'required|string',
            'owner_name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'tender_type' => 'required',
            'contract_type' => 'required',
            'currency_id' => 'required|exists:currencies,id',
            'submission_deadline' => 'required|date',
        ]);

        $tender->update($validated);

        return redirect()->route('tenders.show', $tender)
            ->with('success', 'تم تحديث العطاء بنجاح');
    }

    public function destroy(Tender $tender)
    {
        $tender->delete();

        return redirect()->route('tenders.index')
            ->with('success', 'تم حذف العطاء بنجاح');
    }

    public function decision(Tender $tender)
    {
        return view('tenders.decision', compact('tender'));
    }

    public function storeDecision(Request $request, Tender $tender)
    {
        $validated = $request->validate([
            'participate' => 'required|boolean',
            'participation_decision_notes' => 'nullable|string',
        ]);

        $tender->update([
            'participate' => $validated['participate'],
            'participation_decision_notes' => $validated['participation_decision_notes'] ?? null,
            'decided_by' => Auth::id(),
            'decision_date' => Carbon::now(),
            'status' => $validated['participate'] ? 'preparing' : 'passed',
        ]);

        return redirect()->route('tenders.show', $tender)
            ->with('success', 'تم حفظ القرار بنجاح');
    }

    public function siteVisit(Tender $tender)
    {
        return view('tenders.site-visit', compact('tender'));
    }

    public function storeSiteVisit(Request $request, Tender $tender)
    {
        $validated = $request->validate([
            'visit_date' => 'required|date',
            'visit_time' => 'nullable',
            'attendees' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        // Convert attendees string to array
        $attendees = [];
        if ($request->filled('attendees')) {
            $attendees = array_filter(array_map('trim', explode("\n", $request->attendees)));
        }

        $tender->siteVisits()->create([
            'visit_date' => $validated['visit_date'],
            'visit_time' => $validated['visit_time'],
            'attendees' => $attendees,
            'observations' => $validated['observations'],
            'reported_by' => Auth::id(),
        ]);

        return redirect()->route('tenders.show', $tender)
            ->with('success', 'تم تسجيل زيارة الموقع بنجاح');
    }

    public function competitors(Tender $tender)
    {
        $tender->load('competitors');
        return view('tenders.competitors', compact('tender'));
    }

    public function storeCompetitor(Request $request, Tender $tender)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'classification' => 'required|in:strong,medium,weak',
            'estimated_price' => 'nullable|numeric',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $tender->competitors()->create($validated);

        return redirect()->route('tenders.competitors', $tender)
            ->with('success', 'تم إضافة المنافس بنجاح');
    }
}
