<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBranchRequest extends FormRequest
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
        $branchId = $this->route('branch');
        
        return [
            'company_id' => 'sometimes|required|exists:companies,id',
            'code' => 'sometimes|required|string|max:50|unique:branches,code,' . $branchId,
            'name' => 'sometimes|required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:2',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
            'is_headquarters' => 'boolean',
        ];
    }
}
