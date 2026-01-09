<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankReconciliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reconciliation_number',
        'bank_account_id',
        'reconciliation_date',
        'period_from',
        'period_to',
        'book_balance',
        'bank_balance',
        'adjusted_book_balance',
        'adjusted_bank_balance',
        'difference',
        'status',
        'prepared_by_id',
        'approved_by_id',
        'approved_at',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'reconciliation_date' => 'date',
        'period_from' => 'date',
        'period_to' => 'date',
        'book_balance' => 'decimal:2',
        'bank_balance' => 'decimal:2',
        'adjusted_book_balance' => 'decimal:2',
        'adjusted_bank_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reconciliation) {
            if (!$reconciliation->reconciliation_number) {
                $reconciliation->reconciliation_number = static::generateReconciliationNumber();
            }
        });
    }

    public static function generateReconciliationNumber(): string
    {
        $year = date('Y');
        $lastReconciliation = static::where('reconciliation_number', 'like', "BR-{$year}-%")
            ->latest('id')
            ->first();

        if ($lastReconciliation) {
            $lastNumber = (int)substr($lastReconciliation->reconciliation_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "BR-{$year}-{$newNumber}";
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReconciliationItem::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByBankAccount($query, $bankAccountId)
    {
        return $query->where('bank_account_id', $bankAccountId);
    }
}
