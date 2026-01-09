<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreARInvoiceRequest extends FormRequest
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
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'ipc_id' => 'nullable|exists:i_p_c_s,id',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_terms' => 'required|in:cod,net_7,net_15,net_30,net_45,net_60',
            'gl_account_id' => 'nullable|exists:g_l_accounts,id',
            'attachment_path' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.gl_account_id' => 'nullable|exists:g_l_accounts,id',
        ];
    }
}
