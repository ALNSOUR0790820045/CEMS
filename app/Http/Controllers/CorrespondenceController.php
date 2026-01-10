<?php

namespace App\Http\Controllers;

use App\Models\Correspondence;
use App\Models\CorrespondenceAction;
use App\Models\CorrespondenceDistribution;
use App\Models\CorrespondenceRegister;
use App\Models\CorrespondenceTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CorrespondenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $correspondences = Correspondence::with(['creator', 'assignedUser'])
            ->latest()
            ->paginate(20);
        
        return view('correspondence.index', compact('correspondences'));
    }

    /**
     * Show the correspondence dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'total' => Correspondence::count(),
            'incoming' => Correspondence::incoming()->count(),
            'outgoing' => Correspondence::outgoing()->count(),
            'pending' => Correspondence::pending()->count(),
            'overdue' => Correspondence::overdue()->count(),
            'draft' => Correspondence::where('status', 'draft')->count(),
        ];

        $recentCorrespondence = Correspondence::with(['creator', 'assignedUser'])
            ->latest()
            ->limit(10)
            ->get();

        $overdueItems = Correspondence::overdue()
            ->with(['creator', 'assignedUser'])
            ->limit(5)
            ->get();

        return view('correspondence.dashboard', compact('stats', 'recentCorrespondence', 'overdueItems'));
    }

    /**
     * Display incoming correspondence.
     */
    public function incoming()
    {
        $correspondences = Correspondence::incoming()
            ->with(['creator', 'assignedUser'])
            ->latest()
            ->paginate(20);
        
        return view('correspondence.incoming.index', compact('correspondences'));
    }

    /**
     * Display outgoing correspondence.
     */
    public function outgoing()
    {
        $correspondences = Correspondence::outgoing()
            ->with(['creator', 'assignedUser'])
            ->latest()
            ->paginate(20);
        
        return view('correspondence.outgoing.index', compact('correspondences'));
    }

    /**
     * Display pending correspondence.
     */
    public function pending()
    {
        $correspondences = Correspondence::pending()
            ->with(['creator', 'assignedUser'])
            ->latest()
            ->paginate(20);
        
        return view('correspondence.pending', compact('correspondences'));
    }

    /**
     * Display overdue correspondence.
     */
    public function overdue()
    {
        $correspondences = Correspondence::overdue()
            ->with(['creator', 'assignedUser'])
            ->latest()
            ->paginate(20);
        
        return view('correspondence.overdue', compact('correspondences'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $templates = CorrespondenceTemplate::active()->get();
        return view('correspondence.create', compact('templates'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:incoming,outgoing',
            'category' => 'required|string',
            'priority' => 'required|in:normal,urgent,very_urgent,confidential',
            'subject' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'from_entity' => 'required|string|max:255',
            'from_person' => 'nullable|string|max:255',
            'from_position' => 'nullable|string|max:255',
            'to_entity' => 'required|string|max:255',
            'to_person' => 'nullable|string|max:255',
            'to_position' => 'nullable|string|max:255',
            'document_date' => 'required|date',
            'received_date' => 'nullable|date',
            'sent_date' => 'nullable|date',
            'response_required_date' => 'nullable|date',
            'their_reference' => 'nullable|string|max:255',
            'requires_response' => 'boolean',
            'is_confidential' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Generate reference number
        $register = CorrespondenceRegister::active()
            ->where('type', $validated['type'])
            ->where('year', now()->year)
            ->first();

        if (!$register) {
            // Create default register for this year
            $prefix = $validated['type'] === 'incoming' ? 'IN' : 'OUT';
            $register = CorrespondenceRegister::create([
                'register_number' => "{$prefix}-" . now()->year,
                'name' => "سجل " . ($validated['type'] === 'incoming' ? 'الوارد' : 'الصادر') . " " . now()->year,
                'type' => $validated['type'],
                'year' => now()->year,
                'prefix' => $prefix,
                'is_active' => true,
            ]);
        }

        $validated['reference_number'] = $register->generateReferenceNumber();
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'draft';

        $correspondence = Correspondence::create($validated);

        return redirect()->route('correspondence.show', $correspondence)
            ->with('success', 'تم إنشاء المراسلة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Correspondence $correspondence)
    {
        $correspondence->load([
            'creator',
            'approver',
            'assignedUser',
            'attachments.uploader',
            'distributions.user',
            'actions.user',
            'replyTo',
            'parent',
            'replies',
            'children'
        ]);

        return view('correspondence.show', compact('correspondence'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Correspondence $correspondence)
    {
        if (!$correspondence->canBeEdited()) {
            return redirect()->route('correspondence.show', $correspondence)
                ->with('error', 'لا يمكن تعديل هذه المراسلة');
        }

        $templates = CorrespondenceTemplate::active()->get();
        return view('correspondence.edit', compact('correspondence', 'templates'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Correspondence $correspondence)
    {
        if (!$correspondence->canBeEdited()) {
            return redirect()->route('correspondence.show', $correspondence)
                ->with('error', 'لا يمكن تعديل هذه المراسلة');
        }

        $validated = $request->validate([
            'category' => 'required|string',
            'priority' => 'required|in:normal,urgent,very_urgent,confidential',
            'subject' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'from_entity' => 'required|string|max:255',
            'from_person' => 'nullable|string|max:255',
            'from_position' => 'nullable|string|max:255',
            'to_entity' => 'required|string|max:255',
            'to_person' => 'nullable|string|max:255',
            'to_position' => 'nullable|string|max:255',
            'document_date' => 'required|date',
            'received_date' => 'nullable|date',
            'sent_date' => 'nullable|date',
            'response_required_date' => 'nullable|date',
            'their_reference' => 'nullable|string|max:255',
            'requires_response' => 'boolean',
            'is_confidential' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $correspondence->update($validated);

        return redirect()->route('correspondence.show', $correspondence)
            ->with('success', 'تم تحديث المراسلة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Correspondence $correspondence)
    {
        if (!$correspondence->canBeEdited()) {
            return redirect()->route('correspondence.index')
                ->with('error', 'لا يمكن حذف هذه المراسلة');
        }

        $correspondence->delete();
        
        return redirect()->route('correspondence.index')
            ->with('success', 'تم حذف المراسلة بنجاح');
    }

    /**
     * Send correspondence.
     */
    public function send(Correspondence $correspondence)
    {
        if (!$correspondence->canBeSent()) {
            return back()->with('error', 'لا يمكن إرسال هذه المراسلة');
        }

        $correspondence->update([
            'status' => 'sent',
            'sent_date' => now(),
        ]);

        CorrespondenceAction::create([
            'correspondence_id' => $correspondence->id,
            'user_id' => Auth::id(),
            'action' => 'sent',
        ]);

        return back()->with('success', 'تم إرسال المراسلة بنجاح');
    }

    /**
     * Approve correspondence.
     */
    public function approve(Correspondence $correspondence)
    {
        if ($correspondence->status !== 'pending_approval') {
            return back()->with('error', 'لا يمكن اعتماد هذه المراسلة');
        }

        $correspondence->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        CorrespondenceAction::create([
            'correspondence_id' => $correspondence->id,
            'user_id' => Auth::id(),
            'action' => 'approved',
        ]);

        return back()->with('success', 'تم اعتماد المراسلة بنجاح');
    }

    /**
     * Forward correspondence.
     */
    public function forward(Request $request, Correspondence $correspondence)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'comments' => 'nullable|string',
        ]);

        $correspondence->update([
            'assigned_to' => $validated['user_id'],
        ]);

        CorrespondenceAction::create([
            'correspondence_id' => $correspondence->id,
            'user_id' => Auth::id(),
            'action' => 'forwarded',
            'forwarded_to' => $validated['user_id'],
            'comments' => $validated['comments'] ?? null,
        ]);

        CorrespondenceDistribution::create([
            'correspondence_id' => $correspondence->id,
            'user_id' => $validated['user_id'],
            'action_type' => 'for_action',
        ]);

        return back()->with('success', 'تم تحويل المراسلة بنجاح');
    }

    /**
     * Reply to correspondence.
     */
    public function reply(Request $request, Correspondence $correspondence)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Generate reference number for reply
        $register = CorrespondenceRegister::active()
            ->where('type', 'outgoing')
            ->where('year', now()->year)
            ->first();

        if (!$register) {
            $register = CorrespondenceRegister::create([
                'register_number' => "OUT-" . now()->year,
                'name' => "سجل الصادر " . now()->year,
                'type' => 'outgoing',
                'year' => now()->year,
                'prefix' => 'OUT',
                'is_active' => true,
            ]);
        }

        $reply = Correspondence::create([
            'reference_number' => $register->generateReferenceNumber(),
            'type' => 'outgoing',
            'category' => $correspondence->category,
            'priority' => $correspondence->priority,
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'from_entity' => $correspondence->to_entity,
            'to_entity' => $correspondence->from_entity,
            'document_date' => now(),
            'reply_to_id' => $correspondence->id,
            'parent_id' => $correspondence->parent_id ?? $correspondence->id,
            'status' => 'draft',
            'created_by' => Auth::id(),
        ]);

        $correspondence->update([
            'status' => 'responded',
            'response_date' => now(),
        ]);

        CorrespondenceAction::create([
            'correspondence_id' => $correspondence->id,
            'user_id' => Auth::id(),
            'action' => 'replied',
        ]);

        return redirect()->route('correspondence.show', $reply)
            ->with('success', 'تم إنشاء الرد بنجاح');
    }

    /**
     * Advanced search.
     */
    public function search(Request $request)
    {
        $query = Correspondence::query();

        if ($request->filled('reference_number')) {
            $query->where('reference_number', 'like', '%' . $request->reference_number . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_entity')) {
            $query->where('from_entity', 'like', '%' . $request->from_entity . '%');
        }

        if ($request->filled('to_entity')) {
            $query->where('to_entity', 'like', '%' . $request->to_entity . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('document_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('document_date', '<=', $request->date_to);
        }

        if ($request->filled('subject')) {
            $query->where('subject', 'like', '%' . $request->subject . '%');
        }

        $correspondences = $query->with(['creator', 'assignedUser'])
            ->latest()
            ->paginate(20);

        return view('correspondence.search', compact('correspondences'));
    }

    /**
     * Get statistics.
     */
    public function statistics()
    {
        $stats = [
            'total' => Correspondence::count(),
            'incoming' => Correspondence::incoming()->count(),
            'outgoing' => Correspondence::outgoing()->count(),
            'by_status' => Correspondence::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'by_category' => Correspondence::select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->pluck('count', 'category')
                ->toArray(),
            'by_priority' => Correspondence::select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray(),
            'pending' => Correspondence::pending()->count(),
            'overdue' => Correspondence::overdue()->count(),
        ];

        return view('correspondence.statistics', compact('stats'));
    }

    /**
     * Show correspondence thread.
     */
    public function thread(Correspondence $correspondence)
    {
        $rootId = $correspondence->parent_id ?? $correspondence->id;
        
        $thread = Correspondence::where('id', $rootId)
            ->orWhere('parent_id', $rootId)
            ->with(['creator', 'assignedUser', 'replyTo'])
            ->orderBy('created_at')
            ->get();

        return view('correspondence.thread', compact('thread', 'correspondence'));
    }

    /**
     * Display registers.
     */
    public function registers()
    {
        $registers = CorrespondenceRegister::latest()->get();
        return view('correspondence.registers.index', compact('registers'));
    }

    /**
     * Display templates.
     */
    public function templates()
    {
        $templates = CorrespondenceTemplate::latest()->get();
        return view('correspondence.templates.index', compact('templates'));
    }
}
