<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaborProductivity extends Model
{
    protected $table = 'labor_productivity';

    protected $fillable = [
        'project_id',
        'boq_item_id',
        'date',
        'activity_description',
        'quantity_achieved',
        'unit',
        'labor_count',
        'total_hours',
        'productivity_rate',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'quantity_achieved' => 'decimal:4',
        'total_hours' => 'decimal:2',
        'productivity_rate' => 'decimal:4',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function boqItem()
    {
        return $this->belongsTo(BoqItem::class);
    }

    public function recordedByUser()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
