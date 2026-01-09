<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAmendmentRequest extends FormRequest
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
            'amendment_date' => 'required|date',
            'effective_date' => 'required|date|after_or_equal:amendment_date',
            'previous_contract_value' => 'required|numeric|min:0',
            'new_contract_value' => 'required|numeric|min:0',
            'previous_completion_date' => 'nullable|date',
            'new_completion_date' => 'nullable|date',
            'days_extended' => 'nullable|integer',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
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
            'amendment_date' => 'تاريخ التعديل',
            'effective_date' => 'تاريخ السريان',
            'previous_contract_value' => 'قيمة العقد السابقة',
            'new_contract_value' => 'قيمة العقد الجديدة',
            'previous_completion_date' => 'تاريخ الإنتهاء السابق',
            'new_completion_date' => 'تاريخ الإنتهاء الجديد',
            'days_extended' => 'عدد أيام التمديد',
        ];
    }
}
