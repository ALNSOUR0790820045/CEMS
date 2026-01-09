<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
            'client_id' => 'required|exists:clients,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'project_type' => 'required|in:lump_sum,unit_price,cost_plus,design_build',
            'project_status' => 'required|in:tendering,awarded,mobilization,execution,on_hold,completed,closed',
            'contract_value' => 'required|numeric|min:0',
            'contract_currency_id' => 'required|exists:currencies,id',
            'contract_start_date' => 'required|date',
            'contract_end_date' => 'required|date|after:contract_start_date',
            'actual_start_date' => 'nullable|date',
            'actual_end_date' => 'nullable|date|after:actual_start_date',
            'location' => 'nullable|string|max:255',
            'city_id' => 'nullable|exists:cities,id',
            'country_id' => 'nullable|exists:countries,id',
            'site_address' => 'nullable|string',
            'gps_latitude' => 'nullable|numeric|between:-90,90',
            'gps_longitude' => 'nullable|numeric|between:-180,180',
            'project_manager_id' => 'required|exists:users,id',
            'site_engineer_id' => 'nullable|exists:users,id',
            'contract_manager_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}
