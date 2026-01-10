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

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function parentAccount(): BelongsTo
    {
        return $this->belongsTo(GLAccount::class, 'parent_account_id');
    }

    public function childAccounts(): HasMany
    {
        return $this->hasMany(GLAccount::class, 'parent_account_id');
    }

    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(GLJournalEntryLine::class, 'account_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(GLTransaction::class, 'account_id');
    }

    public function revenueContracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'gl_revenue_account_id');
    }

    public function receivableContracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'gl_receivable_account_id');
    }

    public function arInvoices(): HasMany
    {
        return $this->hasMany(ARInvoice::class);
    }

    public function arInvoiceItems(): HasMany
    {
        return $this->hasMany(ARInvoiceItem::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('account_category', $category);
    }

    public function scopeAllowsPosting($query)
    {
        return $query->where('allow_posting', true);
    }

    public function scopeMainAccounts($query)
    {
        return $query->where('is_main_account', true);
    }

    public function scopeControlAccounts($query)
    {
        return $query->where('is_control_account', true);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('account_level', $level);
    }

    public function getFullAccountCodeAttribute()
    {
        if ($this->parentAccount) {
            return $this->parentAccount->full_account_code.'-'.$this->account_code;
        }

        return $this->account_code;
    }

    public function getBalanceAttribute()
    {
        return $this->current_balance ?? $this->opening_balance ?? 0;
    }
}
