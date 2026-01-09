<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChangeOrderRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'reason' => 'required|string',
            'change_type' => 'required|in:addition,deduction,modification,time_extension',
            'financial_impact' => 'required|in:increase,decrease,no_change',
            'value_change' => 'required|numeric',
            'time_impact' => 'required|in:extension,reduction,no_change',
            'days_change' => 'required|integer',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'العنوان',
            'description' => 'الوصف',
            'reason' => 'السبب',
            'change_type' => 'نوع التغيير',
            'financial_impact' => 'الأثر المالي',
            'value_change' => 'قيمة التغيير',
            'time_impact' => 'الأثر الزمني',
            'days_change' => 'عدد الأيام',
        ];
    }
}
