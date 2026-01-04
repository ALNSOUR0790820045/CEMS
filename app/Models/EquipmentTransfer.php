<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentTransfer extends Model
{
    protected $fillable = [
        'equipment_id',
        'from_project_id',
        'to_project_id',
        'transfer_date',
        'reason',
        'transport_method',
        'transport_cost',
        'approved_by',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'transport_cost' => 'decimal:2',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function fromProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'from_project_id');
    }

    public function toProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'to_project_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
