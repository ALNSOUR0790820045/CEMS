<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractTemplate extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'type',
        'version',
        'year',
        'description',
        'file_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'year' => 'integer',
    ];

    // Relationships
    public function clauses(): HasMany
    {
        return $this->hasMany(ContractTemplateClause::class, 'template_id');
    }

    public function specialConditions(): HasMany
    {
        return $this->hasMany(ContractTemplateSpecialCondition::class, 'template_id');
    }

    public function variables(): HasMany
    {
        return $this->hasMany(ContractTemplateVariable::class, 'template_id');
    }

    public function generatedContracts(): HasMany
    {
        return $this->hasMany(ContractGenerated::class, 'template_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
