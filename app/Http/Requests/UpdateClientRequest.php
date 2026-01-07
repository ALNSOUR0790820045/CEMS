<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'commercial_registration' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'city_id' => 'nullable|exists:cities,id',
            'country_id' => 'nullable|exists:countries,id',
            'client_type' => 'required|in:government,semi_government,private,individual',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}
