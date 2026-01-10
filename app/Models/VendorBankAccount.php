<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorBankAccount extends Model
{
    protected $fillable = [
        'vendor_id',
        'bank_id',
        'bank_name',
        'branch_name',
        'account_number',
        'account_name',
        'iban',
        'swift_code',
        'currency_id',
        'is_primary',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
