<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RetentionAccumulation extends Model
{
    protected $fillable = [
        'retention_id',
        'progress_bill_id',
        'ipc_id',
        'bill_date',
        'bill_amount',
        'retention_percentage',
        'retention_amount',
        'cumulative_retention',
        'remarks',
    ];

    protected $casts = [
        'bill_date' => 'date',
        'bill_amount' => 'decimal:2',
        'retention_percentage' => 'decimal:2',
        'retention_amount' => 'decimal:2',
        'cumulative_retention' => 'decimal:2',
    ];

    public function retention(): BelongsTo
    {
        return $this->belongsTo(Retention::class);
    }

    public function ipc(): BelongsTo
    {
        return $this->belongsTo(IPC::class);
    }
}
