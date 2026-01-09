<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractTemplateVariable extends Model
{
    protected $fillable = [
        'template_id',
        'variable_key',
        'variable_label',
        'variable_label_en',
        'data_type',
        'is_required',
        'default_value',
        'description',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    // Relationships
    public function template(): BelongsTo
    {
        return $this->belongsTo(ContractTemplate::class, 'template_id');
    }

    // Scopes
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }
}
