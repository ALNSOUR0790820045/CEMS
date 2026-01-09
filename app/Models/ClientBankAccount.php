<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientBankAccount extends Model
{
    protected $fillable = [
        'client_id',
        'bank_name',
        'branch_name',
        'account_number',
        'iban',
        'swift_code',
        'currency',
        'is_primary',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // When setting a bank account as primary, unset other primary accounts
        static::saving(function ($account) {
            if ($account->is_primary) {
                static::where('client_id', $account->client_id)
                    ->where('id', '!=', $account->id)
                    ->update(['is_primary' => false]);
            }
        });
    }

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
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

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
