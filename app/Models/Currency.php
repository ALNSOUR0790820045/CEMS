<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'symbol',
        'symbol_position',
        'decimal_places',
        'thousands_separator',
        'decimal_separator',
        'is_base',
        'is_active',
        'exchange_rate',
        'last_updated',
        'company_id',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'is_base' => 'boolean',
        'is_active' => 'boolean',
        'decimal_places' => 'integer',
        'last_updated' => 'datetime',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBase($query)
    {
        return $query->where('is_base', true);
    }

    // Method to format exchange rate
    public function getFormattedExchangeRate()
    {
        return number_format($this->exchange_rate, 6);
    }

    // Method to format amount in this currency
    public function formatAmount($amount)
    {
        $formatted = number_format(
            $amount,
            $this->decimal_places ?? 2,
            $this->decimal_separator ?? '.',
            $this->thousands_separator ?? ','
        );

        if ($this->symbol_position === 'before') {
            return $this->symbol . ' ' . $formatted;
        }

        return $formatted . ' ' . $this->symbol;
    }

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function materialVendors()
    {
        return $this->hasMany(MaterialVendor::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function checks()
    {
        return $this->hasMany(Check::class);
    }

    public function promissoryNotes()
    {
        return $this->hasMany(PromissoryNote::class);
    }

    public function guarantees()
    {
        return $this->hasMany(Guarantee::class, 'currency_id');
    }

    public function exchangeRatesFrom()
    {
        return $this->hasMany(ExchangeRate::class, 'from_currency_id');
    }

    public function exchangeRatesTo()
    {
        return $this->hasMany(ExchangeRate::class, 'to_currency_id');
    }
}
