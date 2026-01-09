<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhotoReportItem extends Model
{
    protected $fillable = [
        'photo_report_id',
        'photo_id',
        'caption',
        'description',
        'page_number',
        'sort_order',
    ];

    protected $casts = [
        'page_number' => 'integer',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function photoReport(): BelongsTo
    {
        return $this->belongsTo(PhotoReport::class);
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }
}
