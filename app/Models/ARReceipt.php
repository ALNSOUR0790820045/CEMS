<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ARReceipt extends Model
{
    protected $table = 'a_r_receipts';

    protected $fillable = [
        'receipt_number',
        'receipt_date',
        'client_id',
        'payment_method',
        'amount',
        'currency_id',
        'exchange_rate',
        'bank_account_id',
        'check_number',
        'reference_number',
        'status',
        'gl_journal_entry_id',
        'notes',
        'company_id',
        'created_by_id',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($receipt) {
            if (empty($receipt->receipt_number)) {
                $receipt->receipt_number = self::generateReceiptNumber();
            }
        });
    }

    public static function generateReceiptNumber()
    {
        $year = date('Y');
        $lastReceipt = self::where('receipt_number', 'like', "ARR-{$year}-%")
            ->orderBy('receipt_number', 'desc')
            ->first();

        if ($lastReceipt) {
            $lastNumber = intval(substr($lastReceipt->receipt_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "ARR-{$year}-{$newNumber}";
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function allocations()
    {
        return $this->hasMany(ARReceiptAllocation::class);
    }

    public function getUnallocatedAmountAttribute()
    {
        $allocated = $this->allocations()->sum('allocated_amount');

        return $this->amount - $allocated;
    }
}
