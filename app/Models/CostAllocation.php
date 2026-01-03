<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostAllocation extends Model
{
    protected $fillable = [
        'transaction_date',
        'source_type',
        'source_id',
        'cost_center_id',
        'gl_account_id',
        'amount',
        'currency_id',
        'description',
        'company_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function glAccount()
    {
        return $this->belongsTo(GlAccount::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function source()
    {
        return $this->morphTo();
    }
}
