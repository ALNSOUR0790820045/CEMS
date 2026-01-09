<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RiskCategory extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'parent_id',
        'description',
        'default_probability',
        'default_impact',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'default_probability' => 'integer',
        'default_impact' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(RiskCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(RiskCategory::class, 'parent_id');
    }

    // Helper methods
    public function isParent(): bool
    {
        return $this->children()->count() > 0;
    }

    public function hasParent(): bool
    {
        return $this->parent_id !== null;
    }
}
