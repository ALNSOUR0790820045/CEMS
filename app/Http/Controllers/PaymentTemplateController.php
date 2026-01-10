<?php

namespace App\Http\Controllers;

use App\Models\PaymentTemplate;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentTemplateController extends Controller
{
    /**
     * Display a listing of templates
     */
    public function index(Request $request)
    {
        $query = PaymentTemplate::with(['company', 'branch', 'bank', 'creator']);

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('name_en', 'like', '%' . $search . '%');
            });
        }

        $templates = $query->latest()->paginate(20);

        return view('payment-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new template
     */
    public function create()
    {
        $companies = Company::all();
        $branches = Branch::active()->get();
        $banks = Bank::where('is_active', true)->get();

        return view('payment-templates.create', compact('companies', 'branches', 'banks'));
    }

    /**
     * Store a newly created template
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'type' => 'required|in:check,promissory_note,guarantee,receipt',
            'category' => 'nullable|string',
            'content' => 'required|string',
            'styles' => 'nullable|string',
            'variables' => 'nullable|array',
            'company_id' => 'nullable|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'bank_id' => 'nullable|exists:banks,id',
            'language' => 'required|in:ar,en,both',
            'paper_size' => 'required|string',
            'orientation' => 'required|in:portrait,landscape',
            'margins' => 'nullable|array',
            'is_default' => 'boolean',
            'status' => 'required|in:active,inactive,draft',
        ]);

        // If this template is marked as default, unset other defaults of the same type
        if ($request->has('is_default') && $request->is_default) {
            PaymentTemplate::where('type', $validated['type'])
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $validated['created_by'] = Auth::id();
        $template = PaymentTemplate::create($validated);

        return redirect()->route('payment-templates.show', $template)
            ->with('success', 'تم إنشاء القالب بنجاح');
    }

    /**
     * Display the specified template
     */
    public function show(PaymentTemplate $paymentTemplate)
    {
        $paymentTemplate->load(['company', 'branch', 'bank', 'creator', 'updater']);
        
        return view('payment-templates.show', compact('paymentTemplate'));
    }

    /**
     * Show the form for editing the specified template
     */
    public function edit(PaymentTemplate $paymentTemplate)
    {
        $companies = Company::all();
        $branches = Branch::active()->get();
        $banks = Bank::where('is_active', true)->get();

        return view('payment-templates.edit', compact('paymentTemplate', 'companies', 'branches', 'banks'));
    }

    /**
     * Update the specified template
     */
    public function update(Request $request, PaymentTemplate $paymentTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'type' => 'required|in:check,promissory_note,guarantee,receipt',
            'category' => 'nullable|string',
            'content' => 'required|string',
            'styles' => 'nullable|string',
            'variables' => 'nullable|array',
            'company_id' => 'nullable|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'bank_id' => 'nullable|exists:banks,id',
            'language' => 'required|in:ar,en,both',
            'paper_size' => 'required|string',
            'orientation' => 'required|in:portrait,landscape',
            'margins' => 'nullable|array',
            'is_default' => 'boolean',
            'status' => 'required|in:active,inactive,draft',
        ]);

        // If this template is marked as default, unset other defaults of the same type
        if ($request->has('is_default') && $request->is_default) {
            PaymentTemplate::where('type', $validated['type'])
                ->where('is_default', true)
                ->where('id', '!=', $paymentTemplate->id)
                ->update(['is_default' => false]);
        }

        $validated['updated_by'] = Auth::id();
        $paymentTemplate->update($validated);

        return redirect()->route('payment-templates.show', $paymentTemplate)
            ->with('success', 'تم تحديث القالب بنجاح');
    }

    /**
     * Remove the specified template
     */
    public function destroy(PaymentTemplate $paymentTemplate)
    {
        // Check if template is in use
        $inUse = false;
        
        if ($paymentTemplate->type == PaymentTemplate::TYPE_CHECK && $paymentTemplate->checks()->count() > 0) {
            $inUse = true;
        } elseif ($paymentTemplate->type == PaymentTemplate::TYPE_PROMISSORY_NOTE && $paymentTemplate->promissoryNotes()->count() > 0) {
            $inUse = true;
        } elseif ($paymentTemplate->type == PaymentTemplate::TYPE_GUARANTEE && $paymentTemplate->guarantees()->count() > 0) {
            $inUse = true;
        }

        if ($inUse) {
            return redirect()->route('payment-templates.index')
                ->with('error', 'لا يمكن حذف قالب مستخدم');
        }

        $paymentTemplate->delete();

        return redirect()->route('payment-templates.index')
            ->with('success', 'تم حذف القالب بنجاح');
    }

    /**
     * Preview template with sample data
     */
    public function preview(Request $request, PaymentTemplate $paymentTemplate)
    {
        $sampleData = $this->getSampleData($paymentTemplate->type);
        $content = $paymentTemplate->render($sampleData);

        return view('payment-templates.preview', compact('paymentTemplate', 'content'));
    }

    /**
     * Duplicate a template
     */
    public function duplicate(PaymentTemplate $paymentTemplate)
    {
        $newTemplate = $paymentTemplate->duplicate();

        return redirect()->route('payment-templates.edit', $newTemplate)
            ->with('success', 'تم نسخ القالب بنجاح');
    }

    /**
     * Get sample data for template preview
     */
    private function getSampleData($type)
    {
        $common = [
            'company_name' => 'شركة المثال للمقاولات',
            'company_name_en' => 'Example Construction Company',
            'company_address' => 'عمان، الأردن',
            'company_cr' => '123456',
            'company_vat' => '987654321',
            'company_phone' => '+962 6 1234567',
            'company_email' => 'info@example.com',
            'date' => date('Y-m-d'),
            'date_hijri' => '1445-06-15',
            'branch_name' => 'الفرع الرئيسي',
            'branch_address' => 'عمان',
        ];

        if ($type == PaymentTemplate::TYPE_CHECK) {
            return array_merge($common, [
                'check_number' => 'CHK-2026-000123',
                'check_date' => date('Y-m-d'),
                'due_date' => date('Y-m-d', strtotime('+30 days')),
                'amount' => '15,000.000',
                'amount_words' => 'خمسة عشر ألف دينار أردني فقط لا غير',
                'amount_words_en' => 'Fifteen Thousand Jordanian Dinars Only',
                'currency_symbol' => 'د.أ',
                'currency_code' => 'JOD',
                'currency_name' => 'دينار أردني',
                'beneficiary' => 'شركة المقاولات الوطنية',
                'bank_name' => 'البنك الأهلي الأردني',
                'account_number' => '1234567890',
                'iban' => 'JO94CBJO0010000000000131000302',
                'description' => 'صرف دفعة مقدمة',
                'project_name' => 'مشروع الإسكان',
                'reference_number' => 'PO-2026-001',
            ]);
        } elseif ($type == PaymentTemplate::TYPE_PROMISSORY_NOTE) {
            return array_merge($common, [
                'note_number' => 'PN-2026-000456',
                'issue_date' => date('Y-m-d'),
                'maturity_date' => date('Y-m-d', strtotime('+90 days')),
                'amount' => '50,000.00',
                'amount_words' => 'خمسون ألف ريال سعودي فقط لا غير',
                'amount_words_en' => 'Fifty Thousand Saudi Riyals Only',
                'issuer_name' => 'شركة المثال للمقاولات',
                'issuer_cr' => '123456',
                'issuer_address' => 'عمان، الأردن',
                'payee_name' => 'شركة التوريدات الحديثة',
                'payee_address' => 'الرياض، السعودية',
                'place_of_issue' => 'عمان',
                'purpose' => 'قيمة مواد بناء',
            ]);
        } elseif ($type == PaymentTemplate::TYPE_GUARANTEE) {
            return array_merge($common, [
                'guarantee_number' => 'LG-2026-000789',
                'guarantee_type' => 'performance',
                'guarantee_type_name' => 'ضمان حسن تنفيذ',
                'contractor_name' => 'شركة المثال للمقاولات',
                'contractor_cr' => '123456',
                'contractor_address' => 'عمان، الأردن',
                'project_name' => 'مشروع الإسكان',
                'contract_number' => 'CNT-2026-001',
                'amount' => '100,000.000',
                'amount_words' => 'مئة ألف دينار أردني فقط لا غير',
                'amount_words_en' => 'One Hundred Thousand Jordanian Dinars Only',
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+1 year')),
                'validity_period' => '12 شهراً',
                'bank_name' => 'البنك الأهلي الأردني',
                'lg_number' => 'LG/2026/12345',
                'purpose' => 'ضمان حسن تنفيذ مشروع الإسكان',
                'description' => 'يغطي فترة التنفيذ الكاملة للمشروع',
            ]);
        }

        return $common;
    }
}
