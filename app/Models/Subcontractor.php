<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subcontractor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subcontractor_code',
        'name',
        'name_en',
        'subcontractor_type',
        'trade_category',
        'commercial_registration',
        'tax_number',
        'license_number',
        'license_expiry',
        'country_id',
        'city_id',
        'address',
        'po_box',
        'postal_code',
        'phone',
        'mobile',
        'fax',
        'email',
        'website',
        'contact_person_name',
        'contact_person_title',
        'contact_person_phone',
        'contact_person_email',
        'payment_terms',
        'credit_limit',
        'currency_id',
        'retention_percentage',
        'insurance_certificate_number',
        'insurance_expiry',
        'insurance_value',
        'rating',
        'quality_rating',
        'time_performance_rating',
        'safety_rating',
        'gl_account_id',
        'is_active',
        'is_approved',
        'is_blacklisted',
        'blacklist_reason',
        'approved_by_id',
        'approved_at',
        'notes',
        'company_id',
        'created_by_id',
    ];

    protected $casts = [
        'license_expiry' => 'date',
        'insurance_expiry' => 'date',
        'credit_limit' => 'decimal:2',
        'retention_percentage' => 'decimal:2',
        'insurance_value' => 'decimal:2',
        'quality_rating' => 'integer',
        'time_performance_rating' => 'integer',
        'safety_rating' => 'integer',
        'is_active' => 'boolean',
        'is_approved' => 'boolean',
        'is_blacklisted' => 'boolean',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subcontractor) {
            if (empty($subcontractor->subcontractor_code)) {
                $subcontractor->subcontractor_code = self::generateCode();
            }
        });
    }

    public static function generateCode(): string
    {
        $year = date('Y');
        $lastCode = self::where('subcontractor_code', 'like', "SUB-{$year}-%")
            ->orderBy('subcontractor_code', 'desc')
            ->first();

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode->subcontractor_code, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "SUB-{$year}-{$newNumber}";
    }

    // Relationships
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function glAccount(): BelongsTo
    {
        return $this->belongsTo(GlAccount::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(SubcontractorContact::class);
    }

    public function agreements(): HasMany
    {
        return $this->hasMany(SubcontractorAgreement::class);
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(SubcontractorWorkOrder::class);
    }

    public function ipcs(): HasMany
    {
        return $this->hasMany(SubcontractorIpc::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(SubcontractorEvaluation::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeNotBlacklisted($query)
    {
        return $query->where('is_blacklisted', false);
    }
}
