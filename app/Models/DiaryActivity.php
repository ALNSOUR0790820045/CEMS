<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiaryActivity extends Model
{
    protected $fillable = [
        'site_diary_id',
        'location',
        'description',
        'description_en',
        'quantity_today',
        'unit_id',
        'cumulative_quantity',
        'percentage_complete',
        'start_time',
        'end_time',
        'status',
        'remarks',
    ];

    protected $casts = [
        'quantity_today' => 'decimal:2',
        'cumulative_quantity' => 'decimal:2',
        'percentage_complete' => 'decimal:2',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // Relationships
    public function siteDiary(): BelongsTo
    {
        return $this->belongsTo(SiteDiary::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
