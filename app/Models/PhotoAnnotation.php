<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhotoAnnotation extends Model
{
    protected $fillable = [
        'photo_id',
        'annotation_type',
        'coordinates',
        'color',
        'text',
        'created_by_id',
    ];

    protected $casts = [
        'coordinates' => 'array',
    ];

    // Relationships
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
