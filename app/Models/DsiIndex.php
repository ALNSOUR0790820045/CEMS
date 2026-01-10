<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DsiIndex extends Model
{
    protected $fillable = [
        'index_date',
        'year',
        'month',
        'materials_index',
        'labor_index',
        'general_index',
        'materials_change_percent',
        'labor_change_percent',
        'source',
        'reference_url',
    ];

    protected $casts = [
        'index_date' => 'date',
        'year' => 'integer',
        'month' => 'integer',
        'materials_index' => 'decimal:4',
        'labor_index' => 'decimal:4',
        'general_index' => 'decimal:4',
        'materials_change_percent' => 'decimal:2',
        'labor_change_percent' => 'decimal:2',
    ];

    // Relationships
    public function priceEscalationCalculations(): HasMany
    {
        return $this->hasMany(PriceEscalationCalculation::class, 'current_materials_index');
    }

    /**
     * Calculate change percentage from previous month
     */
    public function calculateChangePercent(): void
    {
        $previousIndex = self::where('year', $this->month == 1 ? $this->year - 1 : $this->year)
            ->where('month', $this->month == 1 ? 12 : $this->month - 1)
            ->first();

        if ($previousIndex) {
            $this->materials_change_percent = (($this->materials_index - $previousIndex->materials_index) / $previousIndex->materials_index) * 100;
            $this->labor_change_percent = (($this->labor_index - $previousIndex->labor_index) / $previousIndex->labor_index) * 100;
            $this->save();
        }
    }

    /**
     * Get index for specific date
     */
    public static function getIndexForDate($date): ?self
    {
        $carbonDate = \Carbon\Carbon::parse($date);
        return self::where('year', $carbonDate->year)
            ->where('month', $carbonDate->month)
            ->first();
    }
}
