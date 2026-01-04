<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankStatementLine extends Model
{
    protected $fillable = [
        'bank_statement_id',
        'transaction_date',
        'value_date',
        'description',
        'reference_number',
        'debit_amount',
        'credit_amount',
        'balance',
        'is_reconciled',
        'matched_transaction_type',
        'matched_transaction_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'value_date' => 'date',
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'is_reconciled' => 'boolean',
    ];

    // Relationships
    public function bankStatement()
    {
        return $this->belongsTo(BankStatement::class);
    }

    public function matchedTransaction()
    {
        return $this->morphTo('matched_transaction', 'matched_transaction_type', 'matched_transaction_id');
    }

    // Scopes
    public function scopeUnreconciled($query)
    {
        return $query->where('is_reconciled', false);
    }

    public function scopeReconciled($query)
    {
        return $query->where('is_reconciled', true);
    }

    // Accessors
    public function getAmountAttribute()
    {
        return $this->credit_amount - $this->debit_amount;
    }
}
