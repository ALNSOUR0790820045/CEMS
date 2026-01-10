<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractTemplateClause extends Model
{
    protected $fillable = [
        'template_id',
        'clause_number',
        'title',
        'title_en',
        'content',
        'content_en',
        'parent_id',
        'sort_order',
        'has_time_bar',
        'time_bar_days',
        'time_bar_description',
        'category',
        'is_mandatory',
        'is_modifiable',
    ];

    protected $casts = [
        'has_time_bar' => 'boolean',
        'is_mandatory' => 'boolean',
        'is_modifiable' => 'boolean',
        'sort_order' => 'integer',
        'time_bar_days' => 'integer',
    ];

    // Relationships
    public function template(): BelongsTo
    {
        return $this->belongsTo(ContractTemplate::class, 'template_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ContractTemplateClause::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ContractTemplateClause::class, 'parent_id');
    }

    // Scopes
    public function scopeMainClauses($query)
    {
        return $query->whereNull('parent_id')->orderBy('sort_order');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeWithTimeBar($query)
    {
        return $query->where('has_time_bar', true);
    }
}
