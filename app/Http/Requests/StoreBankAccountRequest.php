<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBankAccountRequest extends FormRequest
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
            'account_number' => 'required|string|max:100|unique:bank_accounts,account_number',
            'account_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:50',
            'iban' => 'nullable|string|max:50',
            'currency_id' => 'required|exists:currencies,id',
            'current_balance' => 'nullable|numeric|min:0',
            'bank_balance' => 'nullable|numeric|min:0',
            'gl_account_id' => 'nullable|exists:gl_accounts,id',
            'is_active' => 'boolean',
            'company_id' => 'required|exists:companies,id',
        ];
    }
}
