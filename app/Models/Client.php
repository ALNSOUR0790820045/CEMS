<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_code',
        'name',
        'name_en',
        'client_type',
        'client_category',
        'type',
        'commercial_registration',
        'tax_number',
        'license_number',
        'country',
        'city',
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
        'contact_person',
        'payment_terms',
        'credit_limit',
        'currency',
        'rating',
        'gl_account',
        'notes',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit_limit' => 'decimal: 2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            if (empty($client->client_code)) {
                $client->client_code = static::generateClientCode();
            }
        });
    }

    public static function generateClientCode(): string
    {
        $year = date('Y');
        $prefix = "CLT-{$year}-";

        $lastClient = static::where('client_code', 'like', $prefix.'%')
            ->orderBy('client_code', 'desc')
            ->first();

        if ($lastClient) {
            $lastNumber = (int) substr($lastClient->client_code, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix.str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ClientContact::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(ClientBankAccount::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ClientDocument::class);
    }

    public function tenders(): HasMany
    {
        return $this->hasMany(Tender::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function arInvoices(): HasMany
    {
        return $this->hasMany(ARInvoice::class);
    }

    public function arReceipts(): HasMany
    {
        return $this->hasMany(ARReceipt::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('client_type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('client_category', $category);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeByCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('client_code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('name_en', 'like', "%{$search}%")
                ->orWhere('tax_number', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    public function getPrimaryContactAttribute()
    {
        return $this->contacts()->where('is_primary', true)->first();
    }

    public function getPrimaryBankAccountAttribute()
    {
        return $this->bankAccounts()->where('is_primary', true)->first();
    }
}
