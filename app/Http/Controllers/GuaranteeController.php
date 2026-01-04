<?php

namespace App\Http\Controllers;

use App\Models\Guarantee;
use App\Models\Bank;
use App\Models\Project;
use App\Models\Tender;
use App\Models\Contract;
use App\Models\GuaranteeRenewal;
use App\Models\GuaranteeRelease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GuaranteeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Guarantee::with(['bank', 'project', 'tender', 'contract', 'creator']);
        
        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('bank_id')) {
            $query->where('bank_id', $request->bank_id);
        }
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('guarantee_number', 'like', '%' . $request->search . '%')
                  ->orWhere('beneficiary', 'like', '%' . $request->search . '%')
                  ->orWhere('bank_reference_number', 'like', '%' . $request->search . '%');
            });
        }
        
        $guarantees = $query->latest()->paginate(20);
        $banks = Bank::where('is_active', true)->get();
        
        return view('guarantees.index', compact('guarantees', 'banks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $banks = Bank::where('is_active', true)->get();
        $projects = Project::whereNotIn('status', ['cancelled'])->get();
        $tenders = Tender::whereNotIn('status', ['cancelled'])->get();
        $contracts = Contract::whereNotIn('status', ['terminated'])->get();
        
        return view('guarantees.create', compact('banks', 'projects', 'tenders', 'contracts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:bid,performance,advance_payment,maintenance,retention',
            'bank_id' => 'required|exists:banks,id',
            'project_id' => 'nullable|exists:projects,id',
            'tender_id' => 'nullable|exists:tenders,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'beneficiary' => 'required|string|max:255',
            'beneficiary_address' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'expected_release_date' => 'nullable|date',
            'bank_charges' => 'nullable|numeric|min:0',
            'bank_commission_rate' => 'nullable|numeric|min:0|max:100',
            'cash_margin' => 'nullable|numeric|min:0',
            'margin_percentage' => 'nullable|numeric|min:0|max:100',
            'bank_reference_number' => 'nullable|string|max:255',
            'purpose' => 'nullable|string',
            'notes' => 'nullable|string',
            'auto_renewal' => 'boolean',
            'renewal_period_days' => 'nullable|integer|min:1',
            'alert_days_before_expiry' => 'nullable|integer|min:1',
        ]);
        
        $validated['guarantee_number'] = Guarantee::generateGuaranteeNumber();
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'draft';
        
        $guarantee = Guarantee::create($validated);
        
        return redirect()->route('guarantees.show', $guarantee)
            ->with('success', 'تم إنشاء خطاب الضمان بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Guarantee $guarantee)
    {
        $guarantee->load(['bank', 'project', 'tender', 'contract', 'creator', 'approver', 'renewals.renewedBy', 'releases.releasedBy', 'claims']);
        
        return view('guarantees.show', compact('guarantee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guarantee $guarantee)
    {
        $banks = Bank::where('is_active', true)->get();
        $projects = Project::whereNotIn('status', ['cancelled'])->get();
        $tenders = Tender::whereNotIn('status', ['cancelled'])->get();
        $contracts = Contract::whereNotIn('status', ['terminated'])->get();
        
        return view('guarantees.edit', compact('guarantee', 'banks', 'projects', 'tenders', 'contracts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guarantee $guarantee)
    {
        $validated = $request->validate([
            'type' => 'required|in:bid,performance,advance_payment,maintenance,retention',
            'bank_id' => 'required|exists:banks,id',
            'project_id' => 'nullable|exists:projects,id',
            'tender_id' => 'nullable|exists:tenders,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'beneficiary' => 'required|string|max:255',
            'beneficiary_address' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'expected_release_date' => 'nullable|date',
            'bank_charges' => 'nullable|numeric|min:0',
            'bank_commission_rate' => 'nullable|numeric|min:0|max:100',
            'cash_margin' => 'nullable|numeric|min:0',
            'margin_percentage' => 'nullable|numeric|min:0|max:100',
            'bank_reference_number' => 'nullable|string|max:255',
            'purpose' => 'nullable|string',
            'notes' => 'nullable|string',
            'auto_renewal' => 'boolean',
            'renewal_period_days' => 'nullable|integer|min:1',
            'alert_days_before_expiry' => 'nullable|integer|min:1',
        ]);
        
        $guarantee->update($validated);
        
        return redirect()->route('guarantees.show', $guarantee)
            ->with('success', 'تم تحديث خطاب الضمان بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guarantee $guarantee)
    {
        $guarantee->delete();
        
        return redirect()->route('guarantees.index')
            ->with('success', 'تم حذف خطاب الضمان بنجاح');
    }
    
    /**
     * Approve a guarantee
     */
    public function approve(Guarantee $guarantee)
    {
        $guarantee->update([
            'status' => 'active',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        
        return redirect()->route('guarantees.show', $guarantee)
            ->with('success', 'تم اعتماد خطاب الضمان بنجاح');
    }
    
    /**
     * Show renewal form
     */
    public function showRenewForm(Guarantee $guarantee)
    {
        return view('guarantees.renew', compact('guarantee'));
    }
    
    /**
     * Renew a guarantee
     */
    public function renew(Request $request, Guarantee $guarantee)
    {
        $validated = $request->validate([
            'new_expiry_date' => 'required|date|after:' . $guarantee->expiry_date,
            'renewal_charges' => 'nullable|numeric|min:0',
            'new_amount' => 'nullable|numeric|min:0',
            'bank_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        
        GuaranteeRenewal::create([
            'guarantee_id' => $guarantee->id,
            'old_expiry_date' => $guarantee->expiry_date,
            'new_expiry_date' => $validated['new_expiry_date'],
            'renewal_charges' => $validated['renewal_charges'] ?? 0,
            'new_amount' => $validated['new_amount'] ?? null,
            'renewal_date' => now(),
            'bank_reference' => $validated['bank_reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'renewed_by' => Auth::id(),
        ]);
        
        $guarantee->update([
            'expiry_date' => $validated['new_expiry_date'],
            'amount' => $validated['new_amount'] ?? $guarantee->amount,
            'status' => 'renewed',
        ]);
        
        return redirect()->route('guarantees.show', $guarantee)
            ->with('success', 'تم تجديد خطاب الضمان بنجاح');
    }
    
    /**
     * Show release form
     */
    public function showReleaseForm(Guarantee $guarantee)
    {
        return view('guarantees.release', compact('guarantee'));
    }
    
    /**
     * Release a guarantee
     */
    public function release(Request $request, Guarantee $guarantee)
    {
        $validated = $request->validate([
            'release_type' => 'required|in:full,partial',
            'released_amount' => 'required|numeric|min:0|max:' . $guarantee->amount,
            'bank_confirmation_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        
        $remainingAmount = $validated['release_type'] === 'full' ? 0 : ($guarantee->amount - $validated['released_amount']);
        
        GuaranteeRelease::create([
            'guarantee_id' => $guarantee->id,
            'release_date' => now(),
            'released_amount' => $validated['released_amount'],
            'release_type' => $validated['release_type'],
            'remaining_amount' => $remainingAmount,
            'bank_confirmation_number' => $validated['bank_confirmation_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'released_by' => Auth::id(),
        ]);
        
        $guarantee->update([
            'status' => 'released',
        ]);
        
        return redirect()->route('guarantees.show', $guarantee)
            ->with('success', 'تم تحرير خطاب الضمان بنجاح');
    }
    
    /**
     * Show expiring guarantees
     */
    public function expiring(Request $request)
    {
        $days = $request->get('days', 30);
        $guarantees = Guarantee::expiring($days)
            ->with(['bank', 'project', 'tender', 'contract'])
            ->get();
        
        return view('guarantees.expiring', compact('guarantees', 'days'));
    }
    
    /**
     * Get statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => Guarantee::count(),
            'active' => Guarantee::where('status', 'active')->count(),
            'expiring_30' => Guarantee::expiring(30)->count(),
            'expiring_60' => Guarantee::expiring(60)->count(),
            'expiring_90' => Guarantee::expiring(90)->count(),
            'expired' => Guarantee::expired()->count(),
            'total_amount' => Guarantee::where('status', 'active')->sum('amount'),
            'by_type' => Guarantee::selectRaw('type, COUNT(*) as count, SUM(amount) as total_amount')
                ->where('status', 'active')
                ->groupBy('type')
                ->get(),
            'by_bank' => Guarantee::with('bank')
                ->selectRaw('bank_id, COUNT(*) as count, SUM(amount) as total_amount')
                ->where('status', 'active')
                ->groupBy('bank_id')
                ->get(),
        ];
        
        return view('guarantees.statistics', compact('stats'));
    }
    
    /**
     * Show reports
     */
    public function reports()
    {
        return view('guarantees.reports');
    }
}
