<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuaranteeRenewal extends Model
{
    protected $fillable = [
        'guarantee_id',
        'old_expiry_date',
        'new_expiry_date',
        'renewal_charges',
        'new_amount',
        'renewal_date',
        'bank_reference',
        'notes',
        'renewed_by',
    ];

    protected $casts = [
        'old_expiry_date' => 'date',
        'new_expiry_date' => 'date',
        'renewal_date' => 'date',
        'renewal_charges' => 'decimal:2',
        'new_amount' => 'decimal:2',
    ];

    // Relationships
    public function guarantee()
    {
        return $this->belongsTo(Guarantee::class);
    }

    public function renewedBy()
    {
        return $this->belongsTo(User::class, 'renewed_by');
    }
}
