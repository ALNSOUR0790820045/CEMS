<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_en',
        'code',
        'email',
        'phone',
        'mobile',
        'address',
        'city',
        'country',
        'tax_number',
        'commercial_registration',
        'type',
        'payment_terms',
        'credit_limit',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function siteReceipts(): HasMany
    {
        return $this->hasMany(SiteReceipt::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
