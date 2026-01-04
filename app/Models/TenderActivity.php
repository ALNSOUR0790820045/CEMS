<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderActivity extends Model
{
    protected $fillable = [
        'tender_id',
        'activity_code',
        'activity_name',
        'description',
        'quantity',
        'unit_id',
        'unit_price',
        'wbs_id',
        'parent_activity_id',
        'sequence_order',
        'status',
        'start_date',
        'end_date',
        'completion_percentage',
        'company_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'completion_percentage' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'sequence_order' => 'integer',
    ];

    protected $appends = ['total_amount'];

    public function getTotalAmountAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function wbs()
    {
        return $this->belongsTo(TenderWbs::class, 'wbs_id');
    }

    public function parent()
    {
        return $this->belongsTo(TenderActivity::class, 'parent_activity_id');
    }

    public function children()
    {
        return $this->hasMany(TenderActivity::class, 'parent_activity_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
