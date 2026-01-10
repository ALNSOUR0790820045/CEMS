<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vendor_code' => 'nullable|string|max:255|unique:vendors,vendor_code',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'vendor_type' => 'required|in:materials_supplier,equipment_supplier,services_provider,subcontractor,consultant',
            'vendor_category' => 'nullable|in:strategic,preferred,regular,blacklisted',
            'commercial_registration' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:100|unique:vendors,tax_number',
            'license_number' => 'nullable|string|max:100',
            'country_id' => 'nullable|integer',
            'city_id' => 'nullable|integer',
            'address' => 'nullable|string',
            'po_box' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'fax' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'primary_contact_person' => 'nullable|string|max:255',
            'primary_contact_title' => 'nullable|string|max:255',
            'primary_contact_phone' => 'nullable|string|max:20',
            'primary_contact_email' => 'nullable|email|max:255',
            'payment_terms' => 'nullable|in:cod,7_days,15_days,30_days,45_days,60_days,90_days,custom',
            'credit_limit' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|integer',
            'rating' => 'nullable|in:excellent,good,average,poor',
            'quality_rating' => 'nullable|integer|min:1|max:5',
            'delivery_rating' => 'nullable|integer|min:1|max:5',
            'service_rating' => 'nullable|integer|min:1|max:5',
            'gl_account_id' => 'nullable|integer',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_approved' => 'nullable|boolean',
        ];
    }
}
