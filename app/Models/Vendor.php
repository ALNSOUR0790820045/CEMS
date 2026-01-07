<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vendor_code',
        'name',
        'email',
        'phone',
        'address',
        'tax_number',
        'contact_person',
        'payment_terms',
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

    public function apPayments()
    {
        return $this->hasMany(ApPayment::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
