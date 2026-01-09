<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuaranteeRelease extends Model
{
    protected $fillable = [
        'guarantee_id',
        'release_date',
        'released_amount',
        'release_type',
        'remaining_amount',
        'release_document',
        'bank_confirmation_number',
        'notes',
        'released_by',
    ];

    protected $casts = [
        'release_date' => 'date',
        'released_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    // Relationships
    public function guarantee()
    {
        return $this->belongsTo(Guarantee::class);
    }

    public function releasedBy()
    {
        return $this->belongsTo(User::class, 'released_by');
    }
}
