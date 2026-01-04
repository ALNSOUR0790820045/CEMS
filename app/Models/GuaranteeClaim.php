<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuaranteeClaim extends Model
{
    protected $fillable = [
        'guarantee_id',
        'claim_date',
        'claimed_amount',
        'claim_reason',
        'status',
        'resolution_date',
        'resolution_notes',
    ];

    protected $casts = [
        'claim_date' => 'date',
        'resolution_date' => 'date',
        'claimed_amount' => 'decimal:2',
    ];

    // Relationships
    public function guarantee()
    {
        return $this->belongsTo(Guarantee::class);
    }
}
