<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GLAccount extends Model
{
    protected $table = 'gl_accounts';

    protected $fillable = [
        'account_code',
        'account_name',
        'account_type',
        'parent_id',
        'balance',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function parent()
    {
        return $this->belongsTo(GLAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(GLAccount::class, 'parent_id');
    }

    public function cashAccounts()
    {
        return $this->hasMany(CashAccount::class, 'gl_account_id');
    }
}
