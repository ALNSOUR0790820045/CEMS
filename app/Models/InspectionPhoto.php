<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionPhoto extends Model
{
    protected $fillable = [
        'inspection_id',
        'inspection_item_id',
        'photo_path',
        'thumbnail_path',
        'caption',
        'location',
        'category',
        'taken_at',
        'gps_latitude',
        'gps_longitude',
        'taken_by_id',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
        'gps_latitude' => 'decimal:8',
        'gps_longitude' => 'decimal:8',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function inspectionItem(): BelongsTo
    {
        return $this->belongsTo(InspectionItem::class);
    }

    public function takenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'taken_by_id');
    }
}
