<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_code',
        'account_name',
        'account_name_en',
        'account_type',
        'currency_id',
        'opening_balance',
        'current_balance',
        'gl_account_id',
        'custodian_id',
        'branch_id',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($account) {
            if (empty($account->account_code)) {
                $account->account_code = static::generateAccountCode();
            }
        });
    }

    public static function generateAccountCode(): string
    {
        $year = date('Y');
        $prefix = "CA-{$year}-";
        
        $lastAccount = static::where('account_code', 'like', $prefix . '%')
            ->orderBy('account_code', 'desc')
            ->first();
        
        if ($lastAccount) {
            $lastNumber = (int) substr($lastAccount->account_code, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function glAccount(): BelongsTo
    {
        return $this->belongsTo(GLAccount::class);
    }

    public function custodian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'custodian_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function transfersFrom(): HasMany
    {
        return $this->hasMany(CashTransfer::class, 'from_account_id');
    }

    public function transfersTo(): HasMany
    {
        return $this->hasMany(CashTransfer::class, 'to_account_id');
    }

    public function dailyPositions(): HasMany
    {
        return $this->hasMany(DailyCashPosition::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByCurrency($query, $currencyId)
    {
        return $query->where('currency_id', $currencyId);
    }

    public function getAvailableBalanceAttribute()
    {
        return $this->current_balance;
    }
}
