<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreARReceiptRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receipt_date' => 'required|date',
            'client_id' => 'required|exists:clients,id',
            'payment_method' => 'required|in:cash,check,bank_transfer,credit_card',
            'amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'check_number' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }
}
