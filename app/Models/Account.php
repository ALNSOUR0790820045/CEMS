<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;

    // Account Type Constants
    const TYPE_ASSET = 'asset';

    const TYPE_LIABILITY = 'liability';

    const TYPE_EQUITY = 'equity';

    const TYPE_REVENUE = 'revenue';

    const TYPE_EXPENSE = 'expense';

    // Account Category Constants
    const CATEGORY_CURRENT = 'current';

    const CATEGORY_NON_CURRENT = 'non_current';

    const CATEGORY_OPERATING = 'operating';

    const CATEGORY_NON_OPERATING = 'non_operating';

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'name_en',
        'type',
        'category',
        'parent_id',
        'balance',
        'currency',
        'department_id',
        'is_active',
        'description',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
