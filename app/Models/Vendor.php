<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_code',
        'name',
        'code',
        'email',
        'phone',
        'address',
        'contact_person',
        'tax_number',
        'payment_terms',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function purchaseOrders() { return $this->hasMany(PurchaseOrder::class); }
    public function grns() { return $this->hasMany(GRN:: class); }
    public function apInvoices() { return $this->hasMany(ApInvoice:: class); }
    public function apPayments() { return $this->hasMany(ApPayment::class); }
}