<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InspectGRNRequest extends FormRequest
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
            'inspection_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.grn_item_id' => 'required|exists:grn_items,id',
            'items.*.accepted_quantity' => 'required|numeric|min:0',
            'items.*.rejected_quantity' => 'required|numeric|min:0',
            'items.*.inspection_status' => 'required|in:pending,passed,failed,partial',
            'items.*.rejection_reason' => 'required_if:items.*.inspection_status,failed|nullable|string',
        ];
    }
}
