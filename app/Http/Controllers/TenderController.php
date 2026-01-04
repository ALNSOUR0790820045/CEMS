<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use App\Models\Client;
use App\Models\User;
use App\Models\TenderTimeline;
use App\Models\Project;
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
        $query = Tender::with(['client', 'assignedTo', 'createdBy']);

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
        $clients = Client::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();
        
        return view('tenders.create', compact('clients', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',
            'client_name' => 'nullable|string|max:255',
            'type' => 'required|in:public,private,limited,direct_order',
            'category' => 'required|in:building,infrastructure,industrial,maintenance,supply,other',
            'sector' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'submission_deadline' => 'required|date',
            'submission_time' => 'nullable',
            'announcement_date' => 'nullable|date',
            'documents_deadline' => 'nullable|date',
            'questions_deadline' => 'nullable|date',
            'opening_date' => 'nullable|date',
            'expected_award_date' => 'nullable|date',
            'estimated_value' => 'nullable|numeric',
            'documents_cost' => 'nullable|numeric',
            'bid_bond_amount' => 'nullable|numeric',
            'bid_bond_percentage' => 'nullable|numeric',
            'currency' => 'nullable|string|max:3',
            'priority' => 'nullable|in:low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
            'estimator_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        // Generate tender number
        $year = date('Y');
        $lastTender = Tender::whereYear('created_at', $year)->latest('id')->first();
        $nextNumber = $lastTender ? intval(substr($lastTender->tender_number, -4)) + 1 : 1;
        $validated['tender_number'] = 'TND-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'identified';

        $tender = Tender::create($validated);

        // Add timeline entry
        TenderTimeline::create([
            'tender_id' => $tender->id,
            'action' => 'created',
            'description' => 'تم إنشاء المناقصة',
            'performed_by' => Auth::id(),
        ]);

        return redirect()->route('tenders.show', $tender)
            ->with('success', 'تم إضافة المناقصة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tender $tender)
    {
        $tender->load([
            'client',
            'assignedTo',
            'estimator',
            'createdBy',
            'documents.uploadedBy',
            'competitors',
            'timeline.performedBy',
            'questions'
        ]);

        return view('tenders.show', compact('tender'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tender $tender)
    {
        $clients = Client::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();
        
        return view('tenders.edit', compact('tender', 'clients', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tender $tender)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',
            'client_name' => 'nullable|string|max:255',
            'type' => 'required|in:public,private,limited,direct_order',
            'category' => 'required|in:building,infrastructure,industrial,maintenance,supply,other',
            'sector' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'submission_deadline' => 'required|date',
            'submission_time' => 'nullable',
            'announcement_date' => 'nullable|date',
            'documents_deadline' => 'nullable|date',
            'questions_deadline' => 'nullable|date',
            'opening_date' => 'nullable|date',
            'expected_award_date' => 'nullable|date',
            'estimated_value' => 'nullable|numeric',
            'documents_cost' => 'nullable|numeric',
            'bid_bond_amount' => 'nullable|numeric',
            'bid_bond_percentage' => 'nullable|numeric',
            'currency' => 'nullable|string|max:3',
            'priority' => 'nullable|in:low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
            'estimator_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:identified,studying,go,no_go,documents_purchased,pricing,submitted,opened,negotiating,won,lost,cancelled,converted',
        ]);

        $oldStatus = $tender->status;
        $tender->update($validated);

        // Add timeline if status changed
        if (isset($validated['status']) && $oldStatus !== $validated['status']) {
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
    public function destroy(Tender $tender)
    {
        $tender->delete();
        
        return redirect()->route('tenders.index')
            ->with('success', 'تم حذف المناقصة بنجاح');
    }

    /**
     * Make Go/No-Go decision
     */
    public function goDecision(Request $request, Tender $tender)
    {
        $validated = $request->validate([
            'go_decision' => 'required|boolean',
            'go_decision_notes' => 'nullable|string',
        ]);

        $tender->update([
            'go_decision' => $validated['go_decision'],
            'go_decision_notes' => $validated['go_decision_notes'] ?? null,
            'go_decided_by' => Auth::id(),
            'go_decided_at' => now(),
            'status' => $validated['go_decision'] ? 'go' : 'no_go',
        ]);

        TenderTimeline::create([
            'tender_id' => $tender->id,
            'action' => 'go_decision',
            'description' => $validated['go_decision'] ? 'قرار المشاركة (GO)' : 'قرار عدم المشاركة (NO-GO)',
            'performed_by' => Auth::id(),
        ]);

        return redirect()->route('tenders.show', $tender)
            ->with('success', 'تم تسجيل القرار بنجاح');
    }

    /**
     * Submit tender
     */
    public function submit(Request $request, Tender $tender)
    {
        $validated = $request->validate([
            'our_offer_value' => 'required|numeric',
        ]);

        $tender->update([
            'our_offer_value' => $validated['our_offer_value'],
            'status' => 'submitted',
        ]);

        TenderTimeline::create([
            'tender_id' => $tender->id,
            'action' => 'submitted',
            'description' => 'تم تقديم العرض بقيمة ' . number_format($validated['our_offer_value'], 2) . ' ' . $tender->currency,
            'performed_by' => Auth::id(),
        ]);

        return redirect()->route('tenders.show', $tender)
            ->with('success', 'تم تسجيل تقديم العرض بنجاح');
    }

    /**
     * Record tender result
     */
    public function result(Request $request, Tender $tender)
    {
        $validated = $request->validate([
            'status' => 'required|in:won,lost',
            'winner_name' => 'nullable|string|max:255',
            'winner_value' => 'nullable|numeric',
            'loss_reason' => 'nullable|string',
        ]);

        $tender->update($validated);

        $description = $validated['status'] === 'won' ? 'فوز في المناقصة' : 'خسارة في المناقصة';
        
        TenderTimeline::create([
            'tender_id' => $tender->id,
            'action' => 'result',
            'description' => $description,
            'performed_by' => Auth::id(),
        ]);

        return redirect()->route('tenders.show', $tender)
            ->with('success', 'تم تسجيل النتيجة بنجاح');
    }

    /**
     * Convert tender to project
     */
    public function convert(Request $request, Tender $tender)
    {
        if (!$tender->canConvertToProject()) {
            return redirect()->back()
                ->with('error', 'لا يمكن تحويل هذه المناقصة إلى مشروع');
        }

        $validated = $request->validate([
            'project_name' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            // Generate project number
            $year = date('Y');
            $lastProject = Project::whereYear('created_at', $year)->latest('id')->first();
            $nextNumber = $lastProject ? intval(substr($lastProject->project_number, -4)) + 1 : 1;
            $projectNumber = 'PRJ-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $project = Project::create([
                'project_number' => $projectNumber,
                'name' => $validated['project_name'] ?? $tender->name,
                'description' => $tender->description,
                'client_id' => $tender->client_id,
                'status' => 'planning',
                'start_date' => $validated['start_date'] ?? now(),
                'budget' => $tender->our_offer_value,
            ]);

            $tender->update([
                'project_id' => $project->id,
                'status' => 'converted',
            ]);

            TenderTimeline::create([
                'tender_id' => $tender->id,
                'action' => 'converted',
                'description' => 'تم تحويل المناقصة إلى مشروع رقم ' . $projectNumber,
                'performed_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('tenders.show', $tender)
                ->with('success', 'تم تحويل المناقصة إلى مشروع بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحويل المناقصة');
        }
    }

    /**
     * Show pipeline/kanban view
     */
    public function pipeline()
    {
        $statuses = [
            'identified' => 'تم اكتشافها',
            'studying' => 'قيد الدراسة',
            'go' => 'قرار المشاركة',
            'documents_purchased' => 'تم شراء الكراسة',
            'pricing' => 'قيد التسعير',
            'submitted' => 'تم التقديم',
            'opened' => 'تم فتح المظاريف',
            'negotiating' => 'قيد التفاوض',
        ];

        $tenders = Tender::with(['client', 'assignedTo'])
            ->whereIn('status', array_keys($statuses))
            ->get()
            ->groupBy('status');

        return view('tenders.pipeline', compact('tenders', 'statuses'));
    }

    /**
     * Show statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => Tender::count(),
            'active' => Tender::whereIn('status', ['identified', 'studying', 'go', 'documents_purchased', 'pricing', 'submitted', 'opened', 'negotiating'])->count(),
            'won' => Tender::where('status', 'won')->count(),
            'lost' => Tender::where('status', 'lost')->count(),
            'converted' => Tender::where('status', 'converted')->count(),
            'total_value_won' => Tender::where('status', 'won')->sum('our_offer_value'),
            'total_value_lost' => Tender::where('status', 'lost')->sum('our_offer_value'),
        ];

        $tendersByStatus = Tender::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $tendersByCategory = Tender::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get();

        return view('tenders.statistics', compact('stats', 'tendersByStatus', 'tendersByCategory'));
    }

    /**
     * Show calendar view
     */
    public function calendar()
    {
        $tenders = Tender::whereNotNull('submission_deadline')
            ->whereIn('status', ['identified', 'studying', 'go', 'documents_purchased', 'pricing'])
            ->get();

        return view('tenders.calendar', compact('tenders'));
    }

    /**
     * Show expiring tenders
     */
    public function expiring(Request $request)
    {
        $days = $request->get('days', 7);

        $tenders = Tender::where('submission_deadline', '>=', now())
            ->where('submission_deadline', '<=', now()->addDays($days))
            ->whereIn('status', ['identified', 'studying', 'go', 'documents_purchased', 'pricing'])
            ->with(['client', 'assignedTo'])
            ->orderBy('submission_deadline')
            ->get();

        return view('tenders.expiring', compact('tenders', 'days'));
    }
}
