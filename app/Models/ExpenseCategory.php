<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'gl_account_id',
        'spending_limit',
        'requires_receipt',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'spending_limit' => 'decimal:2',
        'requires_receipt' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function glAccount(): BelongsTo
    {
        return $this->belongsTo(GLAccount::class);
    }

    public function pettyCashTransactions(): HasMany
    {
        return $this->hasMany(PettyCashTransaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
