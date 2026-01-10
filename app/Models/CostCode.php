<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CostCode extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'parent_id',
        'level',
        'cost_type',
        'cost_category',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'level' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CostCode::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CostCode::class, 'parent_id');
    }

    public function projectBudgetItems(): HasMany
    {
        return $this->hasMany(ProjectBudgetItem::class);
    }

    public function actualCosts(): HasMany
    {
        return $this->hasMany(ActualCost::class);
    }

    public function committedCosts(): HasMany
    {
        return $this->hasMany(CommittedCost::class);
    }

    public function costForecasts(): HasMany
    {
        return $this->hasMany(CostForecast::class);
    }

    public function varianceAnalyses(): HasMany
    {
        return $this->hasMany(VarianceAnalysis::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('cost_type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('cost_category', $category);
    }

    // Helper methods
    public function getFullCode(): string
    {
        if ($this->parent) {
            return $this->parent->getFullCode() . '.' . $this->code;
        }
        return $this->code;
    }

    public function getAllChildren()
    {
        $children = collect([]);
        
        foreach ($this->children as $child) {
            $children->push($child);
            $children = $children->merge($child->getAllChildren());
        }
        
        return $children;
    }
}
