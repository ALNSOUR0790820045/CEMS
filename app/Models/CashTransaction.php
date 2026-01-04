<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'transaction_type',
        'cash_account_id',
        'amount',
        'payment_method',
        'reference_number',
        'payee_payer',
        'description',
        'related_document_type',
        'related_document_id',
        'gl_journal_entry_id',
        'status',
        'company_id',
        'created_by_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function cashAccount()
    {
        return $this->belongsTo(CashAccount::class);
    }

    public function glJournalEntry()
    {
        return $this->belongsTo(GLJournalEntry::class, 'gl_journal_entry_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    // Generate transaction number
    public static function generateTransactionNumber()
    {
        $year = date('Y');
        $lastTransaction = self::where('transaction_number', 'like', "CT-{$year}-%")
            ->orderBy('transaction_number', 'desc')
            ->first();

        if ($lastTransaction) {
            $lastNumber = intval(substr($lastTransaction->transaction_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "CT-{$year}-{$newNumber}";
    }

    // Boot method to auto-generate transaction number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_number)) {
                $transaction->transaction_number = self::generateTransactionNumber();
            }
        });
    }
}
