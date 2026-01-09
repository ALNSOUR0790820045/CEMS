<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGLAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization will be handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'account_code' => ['required', 'string', 'max:255', 'unique:gl_accounts,account_code'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_name_en' => ['nullable', 'string', 'max:255'],
            'account_type' => ['required', 'in:asset,liability,equity,revenue,expense'],
            'account_category' => [
                'nullable',
                'in:current_asset,fixed_asset,current_liability,long_term_liability,equity,operating_revenue,other_revenue,operating_expense,other_expense'
            ],
            'parent_account_id' => ['nullable', 'exists:gl_accounts,id'],
            'account_level' => ['nullable', 'integer', 'min:1'],
            'is_main_account' => ['nullable', 'boolean'],
            'is_control_account' => ['nullable', 'boolean'],
            'allow_posting' => ['nullable', 'boolean'],
            'currency_id' => ['nullable', 'exists:currencies,id'],
            'is_multi_currency' => ['nullable', 'boolean'],
            'opening_balance' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'account_code' => 'account code',
            'account_name' => 'account name',
            'account_type' => 'account type',
            'parent_account_id' => 'parent account',
        ];
    }
}
