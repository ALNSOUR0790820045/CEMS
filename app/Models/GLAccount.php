<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GLAccount extends Model
{
    protected $table = 'g_l_accounts';

    protected $fillable = [
        'account_number',
        'account_name',
        'account_type',
        'parent_id',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

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

    public function arInvoices()
    {
        return $this->hasMany(ARInvoice::class);
    }

    public function arInvoiceItems()
    {
        return $this->hasMany(ARInvoiceItem::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }
}
