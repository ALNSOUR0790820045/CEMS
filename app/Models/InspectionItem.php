<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionItem extends Model
{
    protected $fillable = [
        'inspection_id',
        'checklist_item_id',
        'item_description',
        'acceptance_criteria',
        'result',
        'score',
        'actual_value',
        'remarks',
        'photo_ids',
        'requires_action',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'requires_action' => 'boolean',
        'photo_ids' => 'array',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(TemplateItem::class, 'checklist_item_id');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(InspectionAction::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(InspectionPhoto::class);
    }
}
