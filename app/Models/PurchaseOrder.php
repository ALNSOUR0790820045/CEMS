<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'po_number',
        'po_date',
        'vendor_id',
        'project_id',
        'status',
        'total_amount',
        'expected_delivery_date',
        'terms',
        'notes',
        'company_id',
        'created_by',
    ];

    protected $casts = [
        'po_date' => 'date',
        'expected_delivery_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function vendor() { return $this->belongsTo(Vendor::class); }
    public function project() { return $this->belongsTo(Project::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function items() { return $this->hasMany(PurchaseOrderItem:: class); }
    public function grns() { return $this->hasMany(GRN::class); }
    public function apInvoices() { return $this->hasMany(ApInvoice::class); }
}