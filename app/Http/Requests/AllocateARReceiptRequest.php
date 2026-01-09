<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AllocateARReceiptRequest extends FormRequest
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
            'allocations' => 'required|array|min:1',
            'allocations.*.a_r_invoice_id' => 'required|exists:a_r_invoices,id',
            'allocations.*.allocated_amount' => 'required|numeric|min:0',
        ];
    }
}
