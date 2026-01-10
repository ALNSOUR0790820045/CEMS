<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderResourceHistogram extends Model
{
    protected $table = 'tender_resource_histogram';

    protected $fillable = [
        'tender_id',
        'tender_resource_id',
        'period_date',
        'required_units',
        'cost',
    ];

    protected $casts = [
        'period_date' => 'date',
        'required_units' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    // Relationships
    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function resource()
    {
        return $this->belongsTo(TenderResource::class, 'tender_resource_id');
    }
}
