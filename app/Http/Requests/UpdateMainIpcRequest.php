<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMainIpcRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $ipcId = $this->route('mainIpc') ?? $this->route('id');
        
        return [
            'period_from' => 'sometimes|required|date',
            'period_to' => 'sometimes|required|date|after:period_from',
            'submission_date' => 'sometimes|required|date',
            'previous_cumulative' => 'sometimes|required|numeric|min:0',
            'current_period_work' => 'sometimes|required|numeric|min:0',
            'approved_change_orders' => 'nullable|numeric|min:0',
            'retention_percent' => 'nullable|numeric|min:0|max:100',
            'advance_payment_deduction' => 'nullable|numeric|min:0',
            'other_deductions' => 'nullable|numeric|min:0',
            'deductions_notes' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'sometimes|in:draft,pending_pm,pending_technical,pending_consultant,pending_client,pending_finance,approved_for_payment,paid,rejected,on_hold',
            'pm_notes' => 'nullable|string',
            'technical_decision' => 'nullable|in:pending,approved,rejected,revision_required',
            'technical_comments' => 'nullable|string',
            'consultant_decision' => 'nullable|in:pending,approved,rejected,revision_required',
            'consultant_approved_amount' => 'nullable|numeric|min:0',
            'consultant_comments' => 'nullable|string',
            'client_decision' => 'nullable|in:pending,approved,rejected,revision_required',
            'client_approved_amount' => 'nullable|numeric|min:0',
            'client_comments' => 'nullable|string',
            'finance_decision' => 'nullable|in:pending,approved,on_hold',
            'finance_comments' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'period_to.after' => 'تاريخ نهاية الفترة يجب أن يكون بعد تاريخ البداية',
            'retention_percent.max' => 'نسبة الاستقطاع يجب ألا تتجاوز 100%',
            'tax_rate.max' => 'نسبة الضريبة يجب ألا تتجاوز 100%',
            'status.in' => 'حالة المستخلص غير صالحة',
        ];
    }
}
