<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
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
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|in:contract,drawing,specification,certificate,report,correspondence,other',
            'category' => 'nullable|string|max:255',
            'related_entity_type' => 'nullable|string|max:255',
            'related_entity_id' => 'nullable|integer',
            'file' => 'required|file|max:51200', // 50MB max
            'description' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'is_confidential' => 'nullable|boolean',
            'status' => 'nullable|in:draft,review,approved,archived,obsolete',
            'expiry_date' => 'nullable|date',
        ];
    }
}
