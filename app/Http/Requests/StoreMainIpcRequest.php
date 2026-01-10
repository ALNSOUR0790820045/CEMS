<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMainIpcRequest extends FormRequest
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
        return [
            'project_id' => 'required|exists:projects,id',
            'ipc_number' => 'nullable|unique:main_ipcs,ipc_number',
            'ipc_sequence' => 'nullable|integer|min:1',
            'period_from' => 'required|date',
            'period_to' => 'required|date|after:period_from',
            'submission_date' => 'required|date',
            'previous_cumulative' => 'required|numeric|min:0',
            'current_period_work' => 'required|numeric|min:0',
            'approved_change_orders' => 'nullable|numeric|min:0',
            'retention_percent' => 'nullable|numeric|min:0|max:100',
            'advance_payment_deduction' => 'nullable|numeric|min:0',
            'other_deductions' => 'nullable|numeric|min:0',
            'deductions_notes' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'items' => 'nullable|array',
            'items.*.description' => 'required_with:items|string',
            'items.*.quantity' => 'required_with:items|numeric|min:0',
            'items.*.unit_rate' => 'required_with:items|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'project_id.required' => 'يجب اختيار المشروع',
            'project_id.exists' => 'المشروع المحدد غير موجود',
            'period_from.required' => 'يجب تحديد تاريخ بداية الفترة',
            'period_to.required' => 'يجب تحديد تاريخ نهاية الفترة',
            'period_to.after' => 'تاريخ نهاية الفترة يجب أن يكون بعد تاريخ البداية',
            'submission_date.required' => 'يجب تحديد تاريخ التقديم',
            'previous_cumulative.required' => 'يجب إدخال المبلغ التراكمي السابق',
            'current_period_work.required' => 'يجب إدخال قيمة الأعمال للفترة الحالية',
            'retention_percent.max' => 'نسبة الاستقطاع يجب ألا تتجاوز 100%',
            'tax_rate.max' => 'نسبة الضريبة يجب ألا تتجاوز 100%',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set defaults from config if not provided
        if (!$this->has('retention_percent')) {
            $this->merge([
                'retention_percent' => config('ipc.default_retention_percent', 10),
            ]);
        }

        if (!$this->has('tax_rate')) {
            $this->merge([
                'tax_rate' => config('ipc.default_tax_rate', 15),
            ]);
        }
    }
}
