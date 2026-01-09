<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGLJournalEntryRequest extends FormRequest
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
        // Same validation rules as store, since we can only update draft entries
        return [
            'entry_date' => ['required', 'date'],
            'journal_type' => ['required', 'in:general,opening_balance,closing,adjustment,reversal,recurring'],
            'reference_type' => ['nullable', 'in:manual,invoice,payment,purchase_order,ipc,payroll,other'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'currency_id' => ['required', 'exists:currencies,id'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            
            // Journal lines validation
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.gl_account_id' => ['required', 'exists:gl_accounts,id'],
            'lines.*.debit_amount' => ['required', 'numeric', 'min:0'],
            'lines.*.credit_amount' => ['required', 'numeric', 'min:0'],
            'lines.*.description' => ['nullable', 'string'],
            'lines.*.cost_center_id' => ['nullable', 'exists:cost_centers,id'],
            'lines.*.project_id' => ['nullable', 'exists:projects,id'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'lines.required' => 'At least one journal entry line is required.',
            'lines.min' => 'At least two journal entry lines are required.',
            'lines.*.gl_account_id.required' => 'GL Account is required for each line.',
            'lines.*.gl_account_id.exists' => 'Selected GL Account is invalid.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $lines = $this->input('lines', []);
            
            // Validate that debit and credit totals are equal
            $totalDebit = collect($lines)->sum('debit_amount');
            $totalCredit = collect($lines)->sum('credit_amount');
            
            if (bccomp($totalDebit, $totalCredit, 2) !== 0) {
                $validator->errors()->add('lines', 'Total debit must equal total credit.');
            }
            
            // Validate that each line has either debit or credit, not both
            foreach ($lines as $index => $line) {
                $debit = floatval($line['debit_amount'] ?? 0);
                $credit = floatval($line['credit_amount'] ?? 0);
                
                if ($debit > 0 && $credit > 0) {
                    $validator->errors()->add("lines.$index", 'A line cannot have both debit and credit amounts.');
                }
                
                if ($debit == 0 && $credit == 0) {
                    $validator->errors()->add("lines.$index", 'A line must have either a debit or credit amount.');
                }
            }
        });
    }
}
