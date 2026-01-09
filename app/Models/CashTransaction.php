<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'cash_account_id',
        'transaction_type',
        'amount',
        'currency_id',
        'exchange_rate',
        'reference_type',
        'reference_id',
        'counterparty_type',
        'counterparty_id',
        'counterparty_name',
        'description',
        'status',
        'posted_by_id',
        'posted_at',
        'gl_journal_entry_id',
        'company_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'posted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_number)) {
                $transaction->transaction_number = static::generateTransactionNumber();
            }
        });
    }

    public static function generateTransactionNumber(): string
    {
        $year = date('Y');
        $prefix = "CT-{$year}-";
        
        $lastTransaction = static::where('transaction_number', 'like', $prefix . '%')
            ->orderBy('transaction_number', 'desc')
            ->first();
        
        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->transaction_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function cashAccount(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function counterparty(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeByAccount($query, $accountId)
    {
        return $query->where('cash_account_id', $accountId);
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('transaction_date', [$from, $to]);
    }

    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}
