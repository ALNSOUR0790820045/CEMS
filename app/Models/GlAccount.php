<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlAccount extends Model
{
    protected $fillable = [
        'account_code',
        'name',
        'account_type',
        'description',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function apInvoices()
    {
        return $this->hasMany(ApInvoice::class);
    }

    public function apInvoiceItems()
    {
        return $this->hasMany(ApInvoiceItem::class);
    }
}
