<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $clientId = $this->route('client')->id ??  $this->route('client');
        
        return [
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'client_type' => 'required|in:government,semi_government,private_sector,private,individual',
            'client_category' => 'nullable|in:strategic,preferred,regular,one_time',
            'commercial_registration' => 'nullable|string|max:255',
            'tax_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('clients', 'tax_number')->ignore($clientId),
            ],
            'license_number' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
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
            'payment_terms' => 'nullable|in:immediate,7_days,15_days,30_days,45_days,60_days,90_days,custom',
            'credit_limit' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'rating' => 'nullable|in:excellent,good,average,poor',
            'gl_account' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name. required' => 'اسم العميل مطلوب',
            'client_type. required' => 'نوع العميل مطلوب',
            'client_category. required' => 'فئة العميل مطلوبة',
            'tax_number.unique' => 'الرقم الضريبي مستخدم من قبل',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'website.url' => 'رابط الموقع غير صحيح',
            'credit_limit.numeric' => 'الحد الائتماني يجب أن يكون رقماً',
            'credit_limit.min' => 'الحد الائتماني يجب أن يكون أكبر من أو يساوي 0',
            'city_id.exists' => 'المدينة المحددة غير موجودة',
            'country_id. exists' => 'الدولة المحددة غير موجودة',
        ];
    }
}