<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GLAccount extends Model
{
    use SoftDeletes;

    protected $table = 'gl_accounts';

    protected $fillable = [
        'account_code',
        'account_name',
        'account_name_en',
        'account_type',
        'account_category',
        'parent_account_id',
        'account_level',
        'is_main_account',
        'is_control_account',
        'allow_posting',
        'currency_id',
        'is_multi_currency',
        'opening_balance',
        'current_balance',
        'description',
        'notes',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'account_level' => 'integer',
        'is_main_account' => 'boolean',
        'is_control_account' => 'boolean',
        'allow_posting' => 'boolean',
        'is_multi_currency' => 'boolean',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the company that owns the account.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the currency for the account.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the parent account.
     */
    public function parentAccount(): BelongsTo
    {
        return $this->belongsTo(GLAccount::class, 'parent_account_id');
    }

    /**
     * Get the child accounts.
     */
    public function childAccounts(): HasMany
    {
        return $this->hasMany(GLAccount::class, 'parent_account_id');
    }

    /**
     * Get the journal entry lines for this account.
     */
    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(GLJournalEntryLine::class);
    }

    /**
     * Scope to filter by account type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    /**
     * Scope to filter active accounts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter accounts that allow posting.
     */
    public function scopeAllowsPosting($query)
    {
        return $query->where('allow_posting', true);
    }
}
