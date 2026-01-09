<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashTransfer extends Model
{
    protected $fillable = [
        'transfer_number',
        'transfer_date',
        'from_account_id',
        'to_account_id',
        'amount',
        'from_currency_id',
        'to_currency_id',
        'exchange_rate',
        'fees',
        'status',
        'requested_by_id',
        'approved_by_id',
        'approved_at',
        'completed_at',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'fees' => 'decimal:2',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transfer) {
            if (empty($transfer->transfer_number)) {
                $transfer->transfer_number = static::generateTransferNumber();
            }
        });
    }

    public static function generateTransferNumber(): string
    {
        $year = date('Y');
        $prefix = "TRF-{$year}-";
        
        $lastTransfer = static::where('transfer_number', 'like', $prefix . '%')
            ->orderBy('transfer_number', 'desc')
            ->first();
        
        if ($lastTransfer) {
            $lastNumber = (int) substr($lastTransfer->transfer_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class, 'to_account_id');
    }

    public function fromCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function toCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('transfer_date', [$from, $to]);
    }
}
