<?php

namespace App\Http\Controllers;

use App\Models\Check;
use App\Models\BankAccount;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\Project;
use App\Models\PaymentTemplate;
use App\Services\AmountToWordsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckController extends Controller
{
    /**
     * Display a listing of checks
     */
    public function index(Request $request)
    {
        $query = Check::with(['currency', 'bankAccount', 'branch', 'project', 'creator']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('check_type')) {
            $query->where('check_type', $request->check_type);
        }

        if ($request->filled('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
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
                $q->where('check_number', 'like', '%' . $search . '%')
                  ->orWhere('beneficiary', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $checks = $query->latest()->paginate(20);
        
        // Get filter options
        $bankAccounts = BankAccount::active()->get();
        $branches = Branch::active()->get();
        $currencies = Currency::active()->get();

        // Statistics
        $stats = [
            'total' => Check::count(),
            'issued' => Check::where('status', Check::STATUS_ISSUED)->count(),
            'pending' => Check::where('status', Check::STATUS_PENDING)->count(),
            'due' => Check::where('status', Check::STATUS_DUE)->count(),
            'cleared' => Check::where('status', Check::STATUS_CLEARED)->count(),
            'bounced' => Check::where('status', Check::STATUS_BOUNCED)->count(),
            'total_amount' => Check::whereIn('status', [Check::STATUS_ISSUED, Check::STATUS_PENDING, Check::STATUS_DUE])
                ->sum('amount_in_base_currency'),
        ];

        return view('checks.index', compact('checks', 'bankAccounts', 'branches', 'currencies', 'stats'));
    }

    /**
     * Show the form for creating a new check
     */
    public function create()
    {
        $bankAccounts = BankAccount::active()->get();
        $branches = Branch::active()->get();
        $currencies = Currency::active()->get();
        $projects = Project::whereNotIn('status', ['cancelled', 'completed'])->get();
        $templates = PaymentTemplate::active()
            ->where('type', PaymentTemplate::TYPE_CHECK)
            ->get();

        return view('checks.create', compact('bankAccounts', 'branches', 'currencies', 'projects', 'templates'));
    }

    /**
     * Store a newly created check
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'branch_id' => 'nullable|exists:branches,id',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:issue_date',
            'check_type' => 'required|in:current,post_dated,deferred',
            'amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'beneficiary' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
            'template_id' => 'nullable|exists:payment_templates,id',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Generate check number
            $validated['check_number'] = Check::generateCheckNumber($validated['bank_account_id']);
            
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

            // Set status based on check type and due date
            if ($validated['check_type'] == Check::TYPE_CURRENT) {
                $validated['status'] = Check::STATUS_ISSUED;
            } elseif ($validated['due_date'] && Carbon::parse($validated['due_date'])->isPast()) {
                $validated['status'] = Check::STATUS_DUE;
            } else {
                $validated['status'] = Check::STATUS_PENDING;
            }

            $validated['created_by'] = Auth::id();

            $check = Check::create($validated);

            DB::commit();

            return redirect()->route('checks.show', $check)
                ->with('success', 'تم إنشاء الشيك بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الشيك: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified check
     */
    public function show(Check $check)
    {
        $check->load(['currency', 'bankAccount', 'branch', 'project', 'template', 'creator', 'approver', 'canceller']);
        
        return view('checks.show', compact('check'));
    }

    /**
     * Show the form for editing the specified check
     */
    public function edit(Check $check)
    {
        if (!$check->canBeModified()) {
            return redirect()->route('checks.show', $check)
                ->with('error', 'لا يمكن تعديل هذا الشيك');
        }

        $bankAccounts = BankAccount::active()->get();
        $branches = Branch::active()->get();
        $currencies = Currency::active()->get();
        $projects = Project::whereNotIn('status', ['cancelled', 'completed'])->get();
        $templates = PaymentTemplate::active()
            ->where('type', PaymentTemplate::TYPE_CHECK)
            ->get();

        return view('checks.edit', compact('check', 'bankAccounts', 'branches', 'currencies', 'projects', 'templates'));
    }

    /**
     * Update the specified check
     */
    public function update(Request $request, Check $check)
    {
        if (!$check->canBeModified()) {
            return redirect()->route('checks.show', $check)
                ->with('error', 'لا يمكن تعديل هذا الشيك');
        }

        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:issue_date',
            'check_type' => 'required|in:current,post_dated,deferred',
            'amount' => 'required|numeric|min:0',
            'beneficiary' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'template_id' => 'nullable|exists:payment_templates,id',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Recalculate amount in words if amount changed
            if ($validated['amount'] != $check->amount) {
                $validated['amount_words'] = AmountToWordsService::convertToArabic(
                    $validated['amount'],
                    $check->currency->code,
                    $check->currency->decimal_places ?? 2
                );
                $validated['amount_words_en'] = AmountToWordsService::convertToEnglish(
                    $validated['amount'],
                    $check->currency->code,
                    $check->currency->decimal_places ?? 2
                );

                // Recalculate base currency amount
                if ($check->currency->is_base) {
                    $validated['amount_in_base_currency'] = $validated['amount'];
                } else {
                    $validated['amount_in_base_currency'] = $validated['amount'] * $check->exchange_rate;
                }
            }

            $check->update($validated);

            DB::commit();

            return redirect()->route('checks.show', $check)
                ->with('success', 'تم تحديث الشيك بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الشيك: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified check
     */
    public function destroy(Check $check)
    {
        if (!$check->canBeDeleted()) {
            return redirect()->route('checks.index')
                ->with('error', 'لا يمكن حذف هذا الشيك');
        }

        $check->delete();

        return redirect()->route('checks.index')
            ->with('success', 'تم حذف الشيك بنجاح');
    }

    /**
     * Mark check as cleared
     */
    public function clear(Request $request, Check $check)
    {
        if (!in_array($check->status, [Check::STATUS_ISSUED, Check::STATUS_PENDING, Check::STATUS_DUE])) {
            return redirect()->route('checks.show', $check)
                ->with('error', 'لا يمكن صرف هذا الشيك');
        }

        $validated = $request->validate([
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $check->update([
            'status' => Check::STATUS_CLEARED,
            'payment_date' => $validated['payment_date'],
            'notes' => $validated['notes'] ?? $check->notes,
        ]);

        return redirect()->route('checks.show', $check)
            ->with('success', 'تم تحديث حالة الشيك إلى "تم الصرف"');
    }

    /**
     * Mark check as bounced
     */
    public function bounce(Request $request, Check $check)
    {
        if (!in_array($check->status, [Check::STATUS_ISSUED, Check::STATUS_PENDING, Check::STATUS_DUE])) {
            return redirect()->route('checks.show', $check)
                ->with('error', 'لا يمكن تحديث حالة هذا الشيك');
        }

        $validated = $request->validate([
            'notes' => 'required|string',
        ]);

        $check->update([
            'status' => Check::STATUS_BOUNCED,
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('checks.show', $check)
            ->with('success', 'تم تحديث حالة الشيك إلى "مرتد"');
    }

    /**
     * Cancel a check
     */
    public function cancel(Request $request, Check $check)
    {
        if ($check->status == Check::STATUS_CLEARED) {
            return redirect()->route('checks.show', $check)
                ->with('error', 'لا يمكن إلغاء شيك تم صرفه');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string',
        ]);

        $check->update([
            'status' => Check::STATUS_CANCELLED,
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_by' => Auth::id(),
            'cancelled_at' => now(),
        ]);

        return redirect()->route('checks.show', $check)
            ->with('success', 'تم إلغاء الشيك بنجاح');
    }

    /**
     * Get checks due soon
     */
    public function dueSoon(Request $request)
    {
        $days = $request->get('days', 7);
        $checks = Check::dueSoon($days)
            ->with(['currency', 'bankAccount', 'branch', 'project'])
            ->get();

        return view('checks.due-soon', compact('checks', 'days'));
    }

    /**
     * Get overdue checks
     */
    public function overdue()
    {
        $checks = Check::overdue()
            ->with(['currency', 'bankAccount', 'branch', 'project'])
            ->get();

        return view('checks.overdue', compact('checks'));
    }

    /**
     * Print check
     */
    public function print(Check $check)
    {
        $check->load(['currency', 'bankAccount', 'branch', 'project', 'template']);

        return view('checks.print', compact('check'));
    }

    /**
     * Export check as PDF
     */
    public function pdf(Check $check)
    {
        $check->load(['currency', 'bankAccount', 'branch', 'project', 'template']);

        $pdf = \PDF::loadView('checks.pdf', compact('check'));
        
        return $pdf->download('check-' . $check->check_number . '.pdf');
    }
}
