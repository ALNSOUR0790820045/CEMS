<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankStatementLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_statement_id',
        'transaction_date',
        'value_date',
        'description',
        'reference_number',
        'debit_amount',
        'credit_amount',
        'balance',
        'is_matched',
        'matched_transaction_type',
        'matched_transaction_id',
        'match_date',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'value_date' => 'date',
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'is_matched' => 'boolean',
        'match_date' => 'datetime',
    ];

    public function bankStatement(): BelongsTo
    {
        return $this->belongsTo(BankStatement::class);
    }

    public function scopeMatched($query)
    {
        return $query->where('is_matched', true);
    }

    public function scopeUnmatched($query)
    {
        return $query->where('is_matched', false);
    }

    public function getAmountAttribute()
    {
        return $this->credit_amount - $this->debit_amount;
    }
}
