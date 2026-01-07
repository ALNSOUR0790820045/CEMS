<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBankReconciliationRequest extends FormRequest
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
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'reconciliation_date' => 'required|date',
            'period_from' => 'required|date',
            'period_to' => 'required|date|after_or_equal:period_from',
            'book_balance' => 'required|numeric',
            'bank_balance' => 'required|numeric',
            'notes' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
        ];
    }
}
