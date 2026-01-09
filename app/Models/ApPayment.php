<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApPayment extends Model
{
    protected $fillable = [
        'payment_number',
        'payment_date',
        'vendor_id',
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
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
    ];

    // Boot method for auto-numbering
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_number)) {
                $year = date('Y');
                $lastPayment = static::where('payment_number', 'like', "APP-{$year}-%")
                    ->orderBy('id', 'desc')
                    ->first();

                if ($lastPayment) {
                    $lastNumber = intval(substr($lastPayment->payment_number, -4));
                    $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $newNumber = '0001';
                }

                $payment->payment_number = "APP-{$year}-{$newNumber}";
            }
        });
    }

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function allocations()
    {
        return $this->hasMany(ApPaymentAllocation::class);
    }
}
