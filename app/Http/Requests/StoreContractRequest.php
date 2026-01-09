<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'contract_number' => 'required|string|max:255',
            'contract_title' => 'required|string|max:255',
            'contract_title_en' => 'nullable|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'contract_type' => 'required|in:lump_sum,unit_price,cost_plus,design_build,epc,bot',
            'contract_category' => 'required|in:main_contract,subcontract,supply,service',
            'contract_value' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'signing_date' => 'required|date',
            'commencement_date' => 'required|date|after_or_equal:signing_date',
            'completion_date' => 'required|date|after:commencement_date',
            'defects_liability_period' => 'nullable|integer|min:0',
            'retention_percentage' => 'nullable|numeric|min:0|max:100',
            'advance_payment_percentage' => 'nullable|numeric|min:0|max:100',
            'payment_terms' => 'nullable|string',
            'penalty_clause' => 'nullable|string',
            'scope_of_work' => 'nullable|string',
            'special_conditions' => 'nullable|string',
            'contract_status' => 'nullable|in:draft,under_negotiation,signed,active,on_hold,completed,terminated,closed',
            'contract_manager_id' => 'required|exists:users,id',
            'project_manager_id' => 'nullable|exists:users,id',
            'gl_revenue_account_id' => 'nullable|exists:g_l_accounts,id',
            'gl_receivable_account_id' => 'nullable|exists:g_l_accounts,id',
            'parent_contract_id' => 'nullable|exists:contracts,id',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'contract_number' => 'رقم العقد',
            'contract_title' => 'عنوان العقد',
            'contract_title_en' => 'عنوان العقد (EN)',
            'client_id' => 'العميل',
            'contract_type' => 'نوع العقد',
            'contract_category' => 'فئة العقد',
            'contract_value' => 'قيمة العقد',
            'currency_id' => 'العملة',
            'signing_date' => 'تاريخ التوقيع',
            'commencement_date' => 'تاريخ البدء',
            'completion_date' => 'تاريخ الإنتهاء',
            'defects_liability_period' => 'فترة ضمان العيوب',
            'retention_percentage' => 'نسبة الاستبقاء',
            'advance_payment_percentage' => 'نسبة الدفعة المقدمة',
            'contract_manager_id' => 'مدير العقد',
            'project_manager_id' => 'مدير المشروع',
        ];
    }
}
