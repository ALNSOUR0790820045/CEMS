<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApInvoiceItem extends Model
{
    protected $fillable = [
        'ap_invoice_id',
        'description',
        'quantity',
        'unit_price',
        'gl_account_id',
        'project_id',
        'cost_center_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    // Computed attributes
    protected $appends = ['amount'];

    public function getAmountAttribute()
    {
        return $this->attributes['amount'] ?? ($this->quantity * $this->unit_price);
    }

    // Relationships
    public function apInvoice()
    {
        return $this->belongsTo(ApInvoice::class);
    }

    public function glAccount()
    {
        return $this->belongsTo(GlAccount::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }
}
