<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClauseRequest extends FormRequest
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
            'clause_number' => 'required|string|max:50',
            'clause_title' => 'required|string|max:255',
            'clause_content' => 'required|string',
            'clause_category' => 'required|in:payment,penalties,warranties,termination,scope,time,quality,safety,other',
            'is_critical' => 'nullable|boolean',
            'display_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'clause_number' => 'رقم البند',
            'clause_title' => 'عنوان البند',
            'clause_content' => 'محتوى البند',
            'clause_category' => 'فئة البند',
            'is_critical' => 'بند حرج',
            'display_order' => 'ترتيب العرض',
        ];
    }
}
