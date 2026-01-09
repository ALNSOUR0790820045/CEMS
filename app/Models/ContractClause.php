<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractClause extends Model
{
    protected $fillable = [
        'contract_id',
        'clause_number',
        'clause_title',
        'clause_content',
        'clause_category',
        'is_critical',
        'display_order',
        'company_id',
    ];

    protected $casts = [
        'is_critical' => 'boolean',
    ];

    // Relationships
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes
    public function scopeByCategory($query, $category)
    {
        return $query->where('clause_category', $category);
    }

    public function scopeCritical($query)
    {
        return $query->where('is_critical', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }
}
