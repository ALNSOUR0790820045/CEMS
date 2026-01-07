<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CertificationRenewal extends Model
{
    protected $fillable = [
        'renewal_number',
        'certification_id',
        'old_expiry_date',
        'new_expiry_date',
        'renewal_cost',
        'renewal_date',
        'processed_by_id',
        'notes',
    ];

    protected $casts = [
        'old_expiry_date' => 'date',
        'new_expiry_date' => 'date',
        'renewal_date' => 'date',
        'renewal_cost' => 'decimal:2',
    ];

    // Relationships
    public function certification(): BelongsTo
    {
        return $this->belongsTo(Certification::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_id');
    }

    // Methods
    public static function generateRenewalNumber()
    {
        return DB::transaction(function () {
            $year = Carbon::now()->year;
            $lastRenewal = self::whereYear('created_at', $year)
                ->latest('id')
                ->lockForUpdate()
                ->first();
            
            $number = $lastRenewal ? ((int) substr($lastRenewal->renewal_number, -4)) + 1 : 1;
            
            return sprintf('RN-%d-%04d', $year, $number);
        });
    }
}
