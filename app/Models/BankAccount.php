<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'account_number',
        'account_name',
        'bank_name',
        'branch',
        'swift_code',
        'iban',
        'currency_id',
        'current_balance',
        'book_balance',
        'gl_account_id',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
        'book_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function glAccount()
    {
        return $this->belongsTo(GlAccount::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function bankStatements()
    {
        return $this->hasMany(BankStatement::class);
    }
}
