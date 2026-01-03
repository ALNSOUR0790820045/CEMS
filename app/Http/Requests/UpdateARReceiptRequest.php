<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateARReceiptRequest extends FormRequest
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
            'receipt_date' => 'sometimes|required|date',
            'client_id' => 'sometimes|required|exists:clients,id',
            'payment_method' => 'sometimes|required|in:cash,check,bank_transfer,credit_card',
            'amount' => 'sometimes|required|numeric|min:0',
            'currency_id' => 'sometimes|required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'check_number' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'status' => 'sometimes|in:pending,cleared,bounced,cancelled',
            'notes' => 'nullable|string',
        ];
    }
}
