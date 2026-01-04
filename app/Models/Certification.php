<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    protected $fillable = [
        'certification_code',
        'certification_name',
        'certification_type',
        'entity_type',
        'entity_id',
        'issuing_authority',
        'certificate_number',
        'issue_date',
        'expiry_date',
        'is_renewable',
        'renewal_period_days',
        'status',
        'certificate_file_path',
        'alert_before_days',
        'last_alert_sent',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'last_alert_sent' => 'date',
        'is_renewable' => 'boolean',
        'alert_before_days' => 'integer',
        'renewal_period_days' => 'integer',
    ];

    // Auto-generate certification code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($certification) {
            if (empty($certification->certification_code)) {
                $certification->certification_code = self::generateCertificationCode();
            }
        });
    }

    /**
     * Generate unique certification code in format CERT-YYYY-XXXX
     */
    public static function generateCertificationCode(): string
    {
        $year = date('Y');
        $lastCertification = self::where('certification_code', 'like', "CERT-{$year}-%")
            ->orderBy('certification_code', 'desc')
            ->first();

        if ($lastCertification) {
            $lastNumber = (int) substr($lastCertification->certification_code, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "CERT-{$year}-{$newNumber}";
    }

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function entity()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiring($query, $days = 30)
    {
        return $query->where('status', 'active')
            ->whereDate('expiry_date', '<=', now()->addDays($days))
            ->whereDate('expiry_date', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
            ->orWhere(function ($q) {
                $q->where('status', 'active')
                    ->whereDate('expiry_date', '<', now());
            });
    }

    // Accessors & Mutators
    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date < now();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        $alertDate = now()->addDays($this->alert_before_days);

        return $this->expiry_date <= $alertDate && $this->expiry_date >= now();
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        return now()->diffInDays($this->expiry_date, false);
    }
}
