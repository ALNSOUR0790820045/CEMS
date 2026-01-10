<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnforeseeableConditionEvidence extends Model
{
    protected $table = 'unforeseeable_conditions_evidence';

    protected $fillable = [
        'condition_id',
        'evidence_type',
        'title',
        'description',
        'file_path',
        'file_name',
        'evidence_date',
        'latitude',
        'longitude',
        'capture_timestamp',
        'uploaded_by',
    ];

    protected $casts = [
        'evidence_date' => 'date',
        'capture_timestamp' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Relationships
    public function condition(): BelongsTo
    {
        return $this->belongsTo(UnforeseeableCondition::class, 'condition_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
