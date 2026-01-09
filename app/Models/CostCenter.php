<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostCenter extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'name_en',
        'description',
        'parent_id',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CostCenter::class, 'parent_id');
    }

    public function apInvoiceItems(): HasMany
    {
        return $this->hasMany(ApInvoiceItem::class);
    }

    public function arInvoiceItems(): HasMany
    {
        return $this->hasMany(ArInvoiceItem::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function glTransactions(): HasMany
    {
        return $this->hasMany(GLTransaction::class);
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
}
