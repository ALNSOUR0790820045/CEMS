<?php

namespace App\Http\Controllers;

use App\Models\PromissoryNote;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\Project;
use App\Models\PaymentTemplate;
use App\Services\AmountToWordsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PromissoryNoteController extends Controller
{
    /**
     * Display a listing of promissory notes
     */
    public function index(Request $request)
    {
        $query = PromissoryNote::with(['currency', 'branch', 'project', 'creator']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('currency_id')) {
            $query->where('currency_id', $request->currency_id);
        }

        if ($request->filled('from_date')) {
            $query->where('issue_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('issue_date', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('note_number', 'like', '%' . $search . '%')
                  ->orWhere('issuer_name', 'like', '%' . $search . '%')
                  ->orWhere('payee_name', 'like', '%' . $search . '%');
            });
        }

        $notes = $query->latest()->paginate(20);
        
        // Get filter options
        $branches = Branch::active()->get();
        $currencies = Currency::active()->get();

        // Statistics
        $stats = [
            'total' => PromissoryNote::count(),
            'issued' => PromissoryNote::where('status', PromissoryNote::STATUS_ISSUED)->count(),
            'pending' => PromissoryNote::where('status', PromissoryNote::STATUS_PENDING)->count(),
            'paid' => PromissoryNote::where('status', PromissoryNote::STATUS_PAID)->count(),
            'total_amount' => PromissoryNote::whereIn('status', [PromissoryNote::STATUS_ISSUED, PromissoryNote::STATUS_PENDING])
                ->sum('amount_in_base_currency'),
        ];

        return view('promissory-notes.index', compact('notes', 'branches', 'currencies', 'stats'));
    }

    /**
     * Show the form for creating a new promissory note
     */
    public function create()
    {
        $branches = Branch::active()->get();
        $currencies = Currency::active()->get();
        $projects = Project::whereNotIn('status', ['cancelled', 'completed'])->get();
        $templates = PaymentTemplate::active()
            ->where('type', PaymentTemplate::TYPE_PROMISSORY_NOTE)
            ->get();

        return view('promissory-notes.create', compact('branches', 'currencies', 'projects', 'templates'));
    }

    /**
     * Store a newly created promissory note
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'issue_date' => 'required|date',
            'maturity_date' => 'required|date|after:issue_date',
            'amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'issuer_name' => 'required|string|max:255',
            'issuer_cr' => 'nullable|string|max:255',
            'issuer_address' => 'nullable|string',
            'payee_name' => 'required|string|max:255',
            'payee_address' => 'nullable|string',
            'place_of_issue' => 'nullable|string|max:255',
            'purpose' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
            'template_id' => 'nullable|exists:payment_templates,id',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Generate note number
            $validated['note_number'] = PromissoryNote::generateNoteNumber();
            
            // Get currency and exchange rate
            $currency = Currency::find($validated['currency_id']);
            $validated['exchange_rate'] = $currency->exchange_rate;
            
            // Calculate amount in base currency
            $baseCurrency = Currency::where('is_base', true)->first();
            if ($currency->id == $baseCurrency->id) {
                $validated['amount_in_base_currency'] = $validated['amount'];
            } else {
                $validated['amount_in_base_currency'] = $validated['amount'] * $currency->exchange_rate;
            }

            // Convert amount to words
            $validated['amount_words'] = AmountToWordsService::convertToArabic(
                $validated['amount'],
                $currency->code,
                $currency->decimal_places ?? 2
            );
            $validated['amount_words_en'] = AmountToWordsService::convertToEnglish(
                $validated['amount'],
                $currency->code,
                $currency->decimal_places ?? 2
            );

            // Set initial status
            $validated['status'] = PromissoryNote::STATUS_ISSUED;
            $validated['created_by'] = Auth::id();

            $note = PromissoryNote::create($validated);

            DB::commit();

            return redirect()->route('promissory-notes.show', $note)
                ->with('success', 'تم إنشاء الكمبيالة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الكمبيالة: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified promissory note
     */
    public function show(PromissoryNote $promissoryNote)
    {
        $promissoryNote->load(['currency', 'branch', 'project', 'template', 'creator', 'approver']);
        
        return view('promissory-notes.show', compact('promissoryNote'));
    }

    /**
     * Show the form for editing the specified promissory note
     */
    public function edit(PromissoryNote $promissoryNote)
    {
        if (!$promissoryNote->canBeModified()) {
            return redirect()->route('promissory-notes.show', $promissoryNote)
                ->with('error', 'لا يمكن تعديل هذه الكمبيالة');
        }

        $branches = Branch::active()->get();
        $currencies = Currency::active()->get();
        $projects = Project::whereNotIn('status', ['cancelled', 'completed'])->get();
        $templates = PaymentTemplate::active()
            ->where('type', PaymentTemplate::TYPE_PROMISSORY_NOTE)
            ->get();

        return view('promissory-notes.edit', compact('promissoryNote', 'branches', 'currencies', 'projects', 'templates'));
    }

    /**
     * Update the specified promissory note
     */
    public function update(Request $request, PromissoryNote $promissoryNote)
    {
        if (!$promissoryNote->canBeModified()) {
            return redirect()->route('promissory-notes.show', $promissoryNote)
                ->with('error', 'لا يمكن تعديل هذه الكمبيالة');
        }

        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'issue_date' => 'required|date',
            'maturity_date' => 'required|date|after:issue_date',
            'amount' => 'required|numeric|min:0',
            'issuer_name' => 'required|string|max:255',
            'issuer_cr' => 'nullable|string|max:255',
            'issuer_address' => 'nullable|string',
            'payee_name' => 'required|string|max:255',
            'payee_address' => 'nullable|string',
            'place_of_issue' => 'nullable|string|max:255',
            'purpose' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'template_id' => 'nullable|exists:payment_templates,id',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Recalculate amount in words if amount changed
            if ($validated['amount'] != $promissoryNote->amount) {
                $validated['amount_words'] = AmountToWordsService::convertToArabic(
                    $validated['amount'],
                    $promissoryNote->currency->code,
                    $promissoryNote->currency->decimal_places ?? 2
                );
                $validated['amount_words_en'] = AmountToWordsService::convertToEnglish(
                    $validated['amount'],
                    $promissoryNote->currency->code,
                    $promissoryNote->currency->decimal_places ?? 2
                );

                // Recalculate base currency amount
                if ($promissoryNote->currency->is_base) {
                    $validated['amount_in_base_currency'] = $validated['amount'];
                } else {
                    $validated['amount_in_base_currency'] = $validated['amount'] * $promissoryNote->exchange_rate;
                }
            }

            $promissoryNote->update($validated);

            DB::commit();

            return redirect()->route('promissory-notes.show', $promissoryNote)
                ->with('success', 'تم تحديث الكمبيالة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الكمبيالة: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified promissory note
     */
    public function destroy(PromissoryNote $promissoryNote)
    {
        if (!$promissoryNote->canBeDeleted()) {
            return redirect()->route('promissory-notes.index')
                ->with('error', 'لا يمكن حذف هذه الكمبيالة');
        }

        $promissoryNote->delete();

        return redirect()->route('promissory-notes.index')
            ->with('success', 'تم حذف الكمبيالة بنجاح');
    }

    /**
     * Mark note as paid
     */
    public function markAsPaid(Request $request, PromissoryNote $promissoryNote)
    {
        if (!in_array($promissoryNote->status, [PromissoryNote::STATUS_ISSUED, PromissoryNote::STATUS_PENDING])) {
            return redirect()->route('promissory-notes.show', $promissoryNote)
                ->with('error', 'لا يمكن تحديث حالة هذه الكمبيالة');
        }

        $validated = $request->validate([
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $promissoryNote->update([
            'status' => PromissoryNote::STATUS_PAID,
            'payment_date' => $validated['payment_date'],
            'notes' => $validated['notes'] ?? $promissoryNote->notes,
        ]);

        return redirect()->route('promissory-notes.show', $promissoryNote)
            ->with('success', 'تم تحديث حالة الكمبيالة إلى "مدفوع"');
    }

    /**
     * Mark note as dishonored
     */
    public function markAsDishonored(Request $request, PromissoryNote $promissoryNote)
    {
        if (!in_array($promissoryNote->status, [PromissoryNote::STATUS_ISSUED, PromissoryNote::STATUS_PENDING])) {
            return redirect()->route('promissory-notes.show', $promissoryNote)
                ->with('error', 'لا يمكن تحديث حالة هذه الكمبيالة');
        }

        $validated = $request->validate([
            'notes' => 'required|string',
        ]);

        $promissoryNote->update([
            'status' => PromissoryNote::STATUS_DISHONORED,
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('promissory-notes.show', $promissoryNote)
            ->with('success', 'تم تحديث حالة الكمبيالة إلى "مرفوض"');
    }

    /**
     * Get notes due soon
     */
    public function dueSoon(Request $request)
    {
        $days = $request->get('days', 7);
        $notes = PromissoryNote::dueSoon($days)
            ->with(['currency', 'branch', 'project'])
            ->get();

        return view('promissory-notes.due-soon', compact('notes', 'days'));
    }

    /**
     * Get overdue notes
     */
    public function overdue()
    {
        $notes = PromissoryNote::overdue()
            ->with(['currency', 'branch', 'project'])
            ->get();

        return view('promissory-notes.overdue', compact('notes'));
    }

    /**
     * Print promissory note
     */
    public function print(PromissoryNote $promissoryNote)
    {
        $promissoryNote->load(['currency', 'branch', 'project', 'template']);

        return view('promissory-notes.print', compact('promissoryNote'));
    }

    /**
     * Export promissory note as PDF
     */
    public function pdf(PromissoryNote $promissoryNote)
    {
        $promissoryNote->load(['currency', 'branch', 'project', 'template']);

        $pdf = \PDF::loadView('promissory-notes.pdf', compact('promissoryNote'));
        
        return $pdf->download('promissory-note-' . $promissoryNote->note_number . '.pdf');
    }
}
