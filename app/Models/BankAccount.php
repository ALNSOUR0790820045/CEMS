<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'account_name',
        'account_number',
        'bank_name',
        'branch',
        'iban',
        'swift_code',
        'currency_id',
        'balance',
        'is_active',
        'gl_account_id',
        'company_id',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function glAccount()
    {
        return $this->belongsTo(GLAccount::class);
    }

    public function arReceipts()
    {
        return $this->hasMany(ARReceipt::class);
    }
}
