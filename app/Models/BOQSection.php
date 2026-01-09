<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BOQSection extends Model
{
    protected $fillable = [
        'boq_header_id',
        'code',
        'name',
        'name_en',
        'description',
        'sort_order',
        'total_amount',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function boqHeader(): BelongsTo
    {
        return $this->belongsTo(BOQHeader::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BOQItem::class);
    }

    public function recalculateTotal(): void
    {
        $this->total_amount = $this->items()->sum('amount');
        $this->save();
    }
}
