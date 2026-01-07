<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBankReconciliationRequest extends FormRequest
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
            'reconciliation_date' => 'sometimes|required|date',
            'period_from' => 'sometimes|required|date',
            'period_to' => 'sometimes|required|date|after_or_equal:period_from',
            'book_balance' => 'sometimes|required|numeric',
            'bank_balance' => 'sometimes|required|numeric',
            'adjusted_book_balance' => 'nullable|numeric',
            'adjusted_bank_balance' => 'nullable|numeric',
            'difference' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ];
    }
}
