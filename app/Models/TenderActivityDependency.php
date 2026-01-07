<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderActivityDependency extends Model
{
    protected $fillable = [
        'predecessor_id',
        'successor_id',
        'type',
        'lag_days',
    ];

    protected $casts = [
        'lag_days' => 'integer',
    ];

    // Relationships
    public function predecessor(): BelongsTo
    {
        return $this->belongsTo(TenderActivity::class, 'predecessor_id');
    }

    public function successor(): BelongsTo
    {
        return $this->belongsTo(TenderActivity::class, 'successor_id');
    }
}
