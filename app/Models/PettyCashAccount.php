<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PettyCashAccount extends Model
{
    protected $fillable = [
        'account_name',
        'custodian_id',
        'fund_limit',
        'gl_account_id',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'fund_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function custodian()
    {
        return $this->belongsTo(User::class, 'custodian_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function transactions()
    {
        return $this->hasMany(PettyCashTransaction::class);
    }
}
