<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBankAccountRequest extends FormRequest
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
        $bankAccountId = $this->route('bank_account');
        
        return [
            'account_number' => 'sometimes|required|string|max:100|unique:bank_accounts,account_number,' . $bankAccountId,
            'account_name' => 'sometimes|required|string|max:255',
            'bank_name' => 'sometimes|required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:50',
            'iban' => 'nullable|string|max:50',
            'currency_id' => 'sometimes|required|exists:currencies,id',
            'current_balance' => 'nullable|numeric',
            'bank_balance' => 'nullable|numeric',
            'gl_account_id' => 'nullable|exists:gl_accounts,id',
            'is_active' => 'boolean',
        ];
    }
}
