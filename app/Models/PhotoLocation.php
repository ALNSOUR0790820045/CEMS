<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhotoLocation extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'name_en',
        'description',
        'gps_latitude',
        'gps_longitude',
        'radius_meters',
        'photos_count',
    ];

    protected $casts = [
        'gps_latitude' => 'decimal:8',
        'gps_longitude' => 'decimal:8',
        'radius_meters' => 'integer',
        'photos_count' => 'integer',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
