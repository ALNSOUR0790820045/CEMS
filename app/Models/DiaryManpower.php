<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiaryManpower extends Model
{
    protected $table = 'diary_manpower';

    protected $fillable = [
        'site_diary_id',
        'trade',
        'own_count',
        'subcontractor_count',
        'subcontractor_id',
        'hours_worked',
        'overtime_hours',
        'notes',
    ];

    protected $casts = [
        'own_count' => 'integer',
        'subcontractor_count' => 'integer',
        'hours_worked' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
    ];

    // Relationships
    public function siteDiary(): BelongsTo
    {
        return $this->belongsTo(SiteDiary::class);
    }

    public function subcontractor(): BelongsTo
    {
        return $this->belongsTo(Subcontractor::class);
    }

    // Accessors
    public function getTotalCountAttribute(): int
    {
        return $this->own_count + $this->subcontractor_count;
    }
}
