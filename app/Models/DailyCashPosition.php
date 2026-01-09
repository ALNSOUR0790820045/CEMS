<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyCashPosition extends Model
{
    protected $fillable = [
        'position_date',
        'cash_account_id',
        'opening_balance',
        'total_receipts',
        'total_payments',
        'closing_balance',
        'is_reconciled',
        'reconciled_by_id',
        'reconciled_at',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'position_date' => 'date',
        'opening_balance' => 'decimal:2',
        'total_receipts' => 'decimal:2',
        'total_payments' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'is_reconciled' => 'boolean',
        'reconciled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($position) {
            $position->closing_balance = $position->opening_balance + $position->total_receipts - $position->total_payments;
        });
    }

    public function cashAccount(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class);
    }

    public function reconciledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeReconciled($query)
    {
        return $query->where('is_reconciled', true);
    }

    public function scopeUnreconciled($query)
    {
        return $query->where('is_reconciled', false);
    }

    public function scopeByAccount($query, $accountId)
    {
        return $query->where('cash_account_id', $accountId);
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('position_date', [$from, $to]);
    }
}
