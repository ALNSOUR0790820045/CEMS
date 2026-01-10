<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionAction extends Model
{
    protected $fillable = [
        'inspection_id',
        'inspection_item_id',
        'action_type',
        'description',
        'assigned_to_id',
        'due_date',
        'completed_date',
        'status',
        'verification_date',
        'verified_by_id',
        'remarks',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_date' => 'date',
        'verification_date' => 'date',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function inspectionItem(): BelongsTo
    {
        return $this->belongsTo(InspectionItem::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_id');
    }
}
