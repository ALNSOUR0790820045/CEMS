<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RetentionGuarantee extends Model
{
    protected $fillable = [
        'retention_id',
        'guarantee_type',
        'guarantee_number',
        'issuing_bank_id',
        'issue_date',
        'expiry_date',
        'amount',
        'currency_id',
        'in_lieu_of_retention',
        'replacement_date',
        'status',
        'document_path',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'replacement_date' => 'date',
        'amount' => 'decimal:2',
        'in_lieu_of_retention' => 'boolean',
    ];

    public function retention(): BelongsTo
    {
        return $this->belongsTo(Retention::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
