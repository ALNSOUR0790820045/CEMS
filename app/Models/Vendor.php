<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vendor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vendor_code',
        'name',
        'name_en',
        'vendor_type',
        'vendor_category',
        'commercial_registration',
        'tax_number',
        'license_number',
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
        'primary_contact_person',
        'primary_contact_title',
        'primary_contact_phone',
        'primary_contact_email',
        'payment_terms',
        'credit_limit',
        'currency_id',
        'rating',
        'quality_rating',
        'delivery_rating',
        'service_rating',
        'gl_account_id',
        'notes',
        'is_active',
        'is_approved',
        'approved_by_id',
        'approved_at',
        'company_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'credit_limit' => 'decimal:2',
        'quality_rating' => 'integer',
        'delivery_rating' => 'integer',
        'service_rating' => 'integer',
    ];

    // Relationships
    public function contacts(): HasMany
    {
        return $this->hasMany(VendorContact::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(VendorBankAccount::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VendorDocument::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(VendorMaterial::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(VendorEvaluation::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
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

    public function scopeByType($query, $type)
    {
        return $query->where('vendor_type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('vendor_category', $category);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    // Accessors
    public function getAverageRatingAttribute()
    {
        $ratings = array_filter([
            $this->quality_rating,
            $this->delivery_rating,
            $this->service_rating,
        ]);

        return count($ratings) > 0 ? round(array_sum($ratings) / count($ratings), 2) : null;
    }

    // Generate vendor code
    public static function generateVendorCode($year = null)
    {
        $year = $year ?? date('Y');
        $lastVendor = static::where('vendor_code', 'like', "VND-{$year}-%")
            ->orderBy('vendor_code', 'desc')
            ->first();

        if ($lastVendor) {
            $lastNumber = intval(substr($lastVendor->vendor_code, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('VND-%s-%04d', $year, $newNumber);
    }
}
