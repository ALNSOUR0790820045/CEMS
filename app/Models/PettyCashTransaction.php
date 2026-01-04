<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PettyCashTransaction extends Model
{
    protected $fillable = [
        'transaction_date',
        'petty_cash_account_id',
        'transaction_type',
        'amount',
        'expense_category',
        'description',
        'payee',
        'receipt_number',
        'attachment_path',
        'approved_by_id',
        'gl_journal_entry_id',
        'company_id',
        'created_by_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function pettyCashAccount()
    {
        return $this->belongsTo(PettyCashAccount::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
