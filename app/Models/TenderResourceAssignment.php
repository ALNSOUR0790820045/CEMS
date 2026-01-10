<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderResourceAssignment extends Model
{
    protected $fillable = [
        'tender_resource_id',
        'tender_activity_id',
        'assigned_quantity',
        'assigned_hours',
        'utilization_percentage',
        'cost',
    ];

    protected $casts = [
        'assigned_quantity' => 'decimal:2',
        'assigned_hours' => 'decimal:2',
        'utilization_percentage' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    // Relationships
    public function resource()
    {
        return $this->belongsTo(TenderResource::class, 'tender_resource_id');
    }

    public function activity()
    {
        return $this->belongsTo(TenderActivity::class, 'tender_activity_id');
    }
}
