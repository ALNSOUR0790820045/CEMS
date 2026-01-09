<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiaryVisitor extends Model
{
    protected $fillable = [
        'site_diary_id',
        'visitor_name',
        'organization',
        'designation',
        'purpose',
        'time_in',
        'time_out',
        'escorted_by',
        'remarks',
    ];

    protected $casts = [
        'time_in' => 'datetime',
        'time_out' => 'datetime',
    ];

    // Relationships
    public function siteDiary(): BelongsTo
    {
        return $this->belongsTo(SiteDiary::class);
    }

    // Accessors
    public function getDurationAttribute(): ?int
    {
        if ($this->time_in && $this->time_out) {
            return $this->time_out->diffInMinutes($this->time_in);
        }
        return null;
    }
}
