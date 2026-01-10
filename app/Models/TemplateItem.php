<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateItem extends Model
{
    protected $fillable = [
        'inspection_template_id',
        'section',
        'item_number',
        'description',
        'description_en',
        'acceptance_criteria',
        'inspection_method',
        'reference_standard',
        'is_mandatory',
        'weight',
        'sort_order',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'weight' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(InspectionTemplate::class, 'inspection_template_id');
    }
}
