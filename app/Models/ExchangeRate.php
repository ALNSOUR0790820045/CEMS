<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExchangeRate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'from_currency_id',
        'to_currency_id',
        'rate',
        'date',
        'source',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'rate' => 'decimal:6',
        'date' => 'date',
    ];

    // Relationships
    public function fromCurrency()
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function toCurrency()
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->where('date', '<=', $date)
            ->orderBy('date', 'desc');
    }

    public function scopeForCurrencyPair($query, $fromCurrencyId, $toCurrencyId)
    {
        return $query->where('from_currency_id', $fromCurrencyId)
            ->where('to_currency_id', $toCurrencyId);
    }

    // Helper method to convert amount
    public static function convert($amount, $fromCurrencyId, $toCurrencyId, $date = null)
    {
        if ($fromCurrencyId == $toCurrencyId) {
            return $amount;
        }

        $date = $date ?? now()->toDateString();

        $rate = self::forCurrencyPair($fromCurrencyId, $toCurrencyId)
            ->forDate($date)
            ->first();

        if (!$rate) {
            // Try inverse rate
            $inverseRate = self::forCurrencyPair($toCurrencyId, $fromCurrencyId)
                ->forDate($date)
                ->first();

            if ($inverseRate) {
                return $amount / $inverseRate->rate;
            }

            throw new \Exception("No exchange rate found for the specified currency pair and date.");
        }

        return $amount * $rate->rate;
    }
}
