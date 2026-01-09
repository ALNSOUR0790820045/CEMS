<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBankStatementRequest extends FormRequest
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
            'statement_date' => 'required|date',
            'period_from' => 'required|date',
            'period_to' => 'required|date|after_or_equal:period_from',
            'opening_balance' => 'required|numeric',
            'closing_balance' => 'required|numeric',
            'total_deposits' => 'nullable|numeric|min:0',
            'total_withdrawals' => 'nullable|numeric|min:0',
            'company_id' => 'required|exists:companies,id',
            'lines' => 'nullable|array',
            'lines.*.transaction_date' => 'required|date',
            'lines.*.value_date' => 'nullable|date',
            'lines.*.description' => 'required|string|max:500',
            'lines.*.reference_number' => 'nullable|string|max:100',
            'lines.*.debit_amount' => 'nullable|numeric|min:0',
            'lines.*.credit_amount' => 'nullable|numeric|min:0',
            'lines.*.balance' => 'nullable|numeric',
            'lines.*.notes' => 'nullable|string',
        ];
    }
}
