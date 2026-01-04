<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashAccount extends Model
{
    protected $fillable = [
        'account_code',
        'account_name',
        'account_type',
        'currency_id',
        'current_balance',
        'gl_account_id',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function glAccount()
    {
        return $this->belongsTo(GLAccount::class, 'gl_account_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function transactions()
    {
        return $this->hasMany(CashTransaction::class);
    }
}
