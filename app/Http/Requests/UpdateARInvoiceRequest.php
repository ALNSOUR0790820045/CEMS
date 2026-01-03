<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateARInvoiceRequest extends FormRequest
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
            'invoice_date' => 'sometimes|required|date',
            'due_date' => 'sometimes|required|date|after_or_equal:invoice_date',
            'client_id' => 'sometimes|required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'ipc_id' => 'nullable|exists:i_p_c_s,id',
            'currency_id' => 'sometimes|required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'subtotal' => 'sometimes|required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'status' => 'sometimes|in:draft,sent,overdue,partially_paid,paid,cancelled',
            'payment_terms' => 'sometimes|required|in:cod,net_7,net_15,net_30,net_45,net_60',
            'gl_account_id' => 'nullable|exists:g_l_accounts,id',
            'attachment_path' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'sometimes|array|min:1',
            'items.*.description' => 'required_with:items|string',
            'items.*.quantity' => 'required_with:items|numeric|min:0',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
            'items.*.gl_account_id' => 'nullable|exists:g_l_accounts,id',
        ];
    }
}
