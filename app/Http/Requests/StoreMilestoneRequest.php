<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMilestoneRequest extends FormRequest
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
            'milestone_number' => 'required|integer|min:1',
            'milestone_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'planned_date' => 'required|date',
            'actual_date' => 'nullable|date',
            'payment_percentage' => 'nullable|numeric|min:0|max:100',
            'payment_amount' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:not_started,in_progress,completed,delayed,cancelled',
            'completion_percentage' => 'nullable|numeric|min:0|max:100',
            'responsible_person_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'milestone_number' => 'رقم المعلم',
            'milestone_name' => 'اسم المعلم',
            'description' => 'الوصف',
            'planned_date' => 'التاريخ المخطط',
            'actual_date' => 'التاريخ الفعلي',
            'payment_percentage' => 'نسبة الدفع',
            'payment_amount' => 'مبلغ الدفع',
            'status' => 'الحالة',
            'completion_percentage' => 'نسبة الإنجاز',
            'responsible_person_id' => 'الشخص المسؤول',
        ];
    }
}
