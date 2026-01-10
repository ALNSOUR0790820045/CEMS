<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CashForecast extends Model
{
    protected $fillable = [
        'forecast_date',
        'forecast_type',
        'category',
        'expected_amount',
        'actual_amount',
        'variance',
        'reference_type',
        'reference_id',
        'probability_percentage',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'forecast_date' => 'date',
        'expected_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'variance' => 'decimal:2',
        'probability_percentage' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($forecast) {
            if ($forecast->actual_amount !== null) {
                $forecast->variance = $forecast->actual_amount - $forecast->expected_amount;
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeByType($query, $type)
    {
        return $query->where('forecast_type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('forecast_date', [$from, $to]);
    }

    public function scopeInflows($query)
    {
        return $query->where('forecast_type', 'inflow');
    }

    public function scopeOutflows($query)
    {
        return $query->where('forecast_type', 'outflow');
    }

    public function getWeightedAmountAttribute()
    {
        return $this->expected_amount * ($this->probability_percentage / 100);
    }
}
