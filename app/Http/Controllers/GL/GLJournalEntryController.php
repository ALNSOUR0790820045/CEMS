<?php

namespace App\Http\Controllers\GL;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGLJournalEntryRequest;
use App\Http\Requests\UpdateGLJournalEntryRequest;
use App\Models\GLJournalEntry;
use App\Models\GLJournalEntryLine;
use App\Models\GLAccount;
use App\Models\Currency;
use App\Models\Project;
use App\Models\Department;
use App\Models\CostCenter;
use App\Services\GL\JournalNumberGenerator;
use App\Services\GL\JournalEntryPostingService;
use App\Services\GL\JournalEntryReversalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GLJournalEntryController extends Controller
{
    protected JournalNumberGenerator $numberGenerator;
    protected JournalEntryPostingService $postingService;
    protected JournalEntryReversalService $reversalService;
    
    public function __construct(
        JournalNumberGenerator $numberGenerator,
        JournalEntryPostingService $postingService,
        JournalEntryReversalService $reversalService
    ) {
        $this->numberGenerator = $numberGenerator;
        $this->postingService = $postingService;
        $this->reversalService = $reversalService;
    }
    
    /**
     * Display a listing of journal entries.
     */
    public function index(Request $request)
    {
        $query = GLJournalEntry::with(['currency', 'createdBy', 'project', 'department'])
            ->where('company_id', Auth::user()->company_id);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('journal_type')) {
            $query->where('journal_type', $request->journal_type);
        }
        
        if ($request->filled('date_from')) {
            $query->where('entry_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('entry_date', '<=', $request->date_to);
        }
        
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('journal_number', 'LIKE', "%{$request->search}%")
                  ->orWhere('description', 'LIKE', "%{$request->search}%")
                  ->orWhere('reference_number', 'LIKE', "%{$request->search}%");
            });
        }
        
        $entries = $query->orderBy('entry_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('gl.journal-entries.index', compact('entries'));
    }

    /**
     * Show the form for creating a new journal entry.
     */
    public function create()
    {
        $accounts = GLAccount::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->where('allow_posting', true)
            ->orderBy('account_code')
            ->get();
            
        $currencies = Currency::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();
            
        $projects = Project::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();
            
        $departments = Department::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();
            
        $costCenters = CostCenter::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();
        
        return view('gl.journal-entries.create', compact(
            'accounts',
            'currencies',
            'projects',
            'departments',
            'costCenters'
        ));
    }

    /**
     * Store a newly created journal entry.
     */
    public function store(StoreGLJournalEntryRequest $request)
    {
        DB::beginTransaction();
        
        try {
            // Generate journal number
            $journalNumber = $this->numberGenerator->generate($request->entry_date);
            
            // Calculate totals
            $totalDebit = collect($request->lines)->sum('debit_amount');
            $totalCredit = collect($request->lines)->sum('credit_amount');
            
            // Create journal entry
            $entry = GLJournalEntry::create([
                'journal_number' => $journalNumber,
                'entry_date' => $request->entry_date,
                'journal_type' => $request->journal_type,
                'reference_type' => $request->reference_type,
                'reference_number' => $request->reference_number,
                'description' => $request->description,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'currency_id' => $request->currency_id,
                'exchange_rate' => $request->exchange_rate ?? 1.0000,
                'status' => 'draft',
                'created_by_id' => Auth::id(),
                'project_id' => $request->project_id,
                'department_id' => $request->department_id,
                'company_id' => Auth::user()->company_id,
            ]);
            
            // Create journal entry lines
            foreach ($request->lines as $index => $lineData) {
                GLJournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'line_number' => $index + 1,
                    'gl_account_id' => $lineData['gl_account_id'],
                    'debit_amount' => $lineData['debit_amount'] ?? 0,
                    'credit_amount' => $lineData['credit_amount'] ?? 0,
                    'description' => $lineData['description'] ?? null,
                    'cost_center_id' => $lineData['cost_center_id'] ?? null,
                    'project_id' => $lineData['project_id'] ?? null,
                    'currency_id' => $request->currency_id,
                    'exchange_rate' => $request->exchange_rate ?? 1.0000,
                    'base_currency_debit' => ($lineData['debit_amount'] ?? 0) * ($request->exchange_rate ?? 1.0000),
                    'base_currency_credit' => ($lineData['credit_amount'] ?? 0) * ($request->exchange_rate ?? 1.0000),
                    'company_id' => Auth::user()->company_id,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('gl.journal-entries.show', $entry)
                ->with('success', 'Journal entry created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create journal entry: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified journal entry.
     */
    public function show(GLJournalEntry $journalEntry)
    {
        $journalEntry->load([
            'lines.glAccount',
            'lines.costCenter',
            'lines.project',
            'currency',
            'createdBy',
            'approvedBy',
            'postedBy',
            'project',
            'department',
            'reversedFrom',
            'reversedBy'
        ]);
        
        return view('gl.journal-entries.show', compact('journalEntry'));
    }

    /**
     * Show the form for editing the specified journal entry.
     */
    public function edit(GLJournalEntry $journalEntry)
    {
        // Can only edit draft entries
        if ($journalEntry->status !== 'draft') {
            return back()->withErrors(['error' => 'Only draft journal entries can be edited.']);
        }
        
        $journalEntry->load('lines');
        
        $accounts = GLAccount::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->where('allow_posting', true)
            ->orderBy('account_code')
            ->get();
            
        $currencies = Currency::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();
            
        $projects = Project::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();
            
        $departments = Department::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();
            
        $costCenters = CostCenter::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();
        
        return view('gl.journal-entries.edit', compact(
            'journalEntry',
            'accounts',
            'currencies',
            'projects',
            'departments',
            'costCenters'
        ));
    }

    /**
     * Update the specified journal entry.
     */
    public function update(UpdateGLJournalEntryRequest $request, GLJournalEntry $journalEntry)
    {
        // Can only update draft entries
        if ($journalEntry->status !== 'draft') {
            return back()->withErrors(['error' => 'Only draft journal entries can be updated.']);
        }
        
        DB::beginTransaction();
        
        try {
            // Calculate totals
            $totalDebit = collect($request->lines)->sum('debit_amount');
            $totalCredit = collect($request->lines)->sum('credit_amount');
            
            // Update journal entry
            $journalEntry->update([
                'entry_date' => $request->entry_date,
                'journal_type' => $request->journal_type,
                'reference_type' => $request->reference_type,
                'reference_number' => $request->reference_number,
                'description' => $request->description,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'currency_id' => $request->currency_id,
                'exchange_rate' => $request->exchange_rate ?? 1.0000,
                'project_id' => $request->project_id,
                'department_id' => $request->department_id,
            ]);
            
            // Delete existing lines
            $journalEntry->lines()->delete();
            
            // Create new lines
            foreach ($request->lines as $index => $lineData) {
                GLJournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'line_number' => $index + 1,
                    'gl_account_id' => $lineData['gl_account_id'],
                    'debit_amount' => $lineData['debit_amount'] ?? 0,
                    'credit_amount' => $lineData['credit_amount'] ?? 0,
                    'description' => $lineData['description'] ?? null,
                    'cost_center_id' => $lineData['cost_center_id'] ?? null,
                    'project_id' => $lineData['project_id'] ?? null,
                    'currency_id' => $request->currency_id,
                    'exchange_rate' => $request->exchange_rate ?? 1.0000,
                    'base_currency_debit' => ($lineData['debit_amount'] ?? 0) * ($request->exchange_rate ?? 1.0000),
                    'base_currency_credit' => ($lineData['credit_amount'] ?? 0) * ($request->exchange_rate ?? 1.0000),
                    'company_id' => Auth::user()->company_id,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('gl.journal-entries.show', $journalEntry)
                ->with('success', 'Journal entry updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update journal entry: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified journal entry (soft delete).
     */
    public function destroy(GLJournalEntry $journalEntry)
    {
        // Can only delete draft entries
        if ($journalEntry->status !== 'draft') {
            return back()->withErrors(['error' => 'Only draft journal entries can be deleted.']);
        }
        
        $journalEntry->delete();
        
        return redirect()->route('gl.journal-entries.index')
            ->with('success', 'Journal entry deleted successfully.');
    }
    
    /**
     * Submit journal entry for approval.
     */
    public function submit(GLJournalEntry $journalEntry)
    {
        if ($journalEntry->status !== 'draft') {
            return back()->withErrors(['error' => 'Only draft journal entries can be submitted.']);
        }
        
        $journalEntry->update(['status' => 'pending_approval']);
        
        return back()->with('success', 'Journal entry submitted for approval.');
    }
    
    /**
     * Approve journal entry.
     */
    public function approve(GLJournalEntry $journalEntry)
    {
        if ($journalEntry->status !== 'pending_approval') {
            return back()->withErrors(['error' => 'Only pending approval entries can be approved.']);
        }
        
        $journalEntry->update([
            'status' => 'approved',
            'approved_by_id' => Auth::id(),
            'approved_at' => now(),
        ]);
        
        return back()->with('success', 'Journal entry approved successfully.');
    }
    
    /**
     * Reject journal entry.
     */
    public function reject(GLJournalEntry $journalEntry)
    {
        if ($journalEntry->status !== 'pending_approval') {
            return back()->withErrors(['error' => 'Only pending approval entries can be rejected.']);
        }
        
        $journalEntry->update(['status' => 'draft']);
        
        return back()->with('success', 'Journal entry rejected. Status set to draft.');
    }
    
    /**
     * Post journal entry to ledger.
     */
    public function post(GLJournalEntry $journalEntry)
    {
        try {
            $this->postingService->post($journalEntry, Auth::id());
            
            return back()->with('success', 'Journal entry posted to ledger successfully.');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Reverse posted journal entry.
     */
    public function reverse(GLJournalEntry $journalEntry, Request $request)
    {
        try {
            $reversalEntry = $this->reversalService->reverse(
                $journalEntry,
                Auth::id(),
                $request->reversal_date
            );
            
            return redirect()->route('gl.journal-entries.show', $reversalEntry)
                ->with('success', 'Journal entry reversed successfully.');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

