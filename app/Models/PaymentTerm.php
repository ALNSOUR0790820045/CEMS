<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTerm extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'days',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'days' => 'integer',
    ];

    // Scope for active payment terms
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Display name accessor
    public function getDisplayNameAttribute()
    {
        if ($this->days == 0) {
            return 'نقدي';
        }
        return "آجل {$this->days} يوم";
    }
}
