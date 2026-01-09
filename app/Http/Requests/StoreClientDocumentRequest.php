<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientDocumentRequest extends FormRequest
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
            'document_type' => 'required|in:commercial_registration,tax_certificate,license,contract,id_copy,other',
            'document_name' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // 10MB max
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'document_type.required' => 'نوع المستند مطلوب',
            'document_name.required' => 'اسم المستند مطلوب',
            'file.required' => 'الملف مطلوب',
            'file.max' => 'حجم الملف يجب أن لا يتجاوز 10 ميجابايت',
            'expiry_date.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد أو يساوي تاريخ الإصدار',
        ];
    }
}
