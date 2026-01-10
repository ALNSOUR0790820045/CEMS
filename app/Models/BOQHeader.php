<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BOQHeader extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'boq_number',
        'name',
        'description',
        'boqable_id',
        'boqable_type',
        'type',
        'status',
        'version',
        'currency',
        'total_amount',
        'markup_percentage',
        'discount_percentage',
        'final_amount',
        'created_by',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'markup_percentage' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function boqable(): MorphTo
    {
        return $this->morphTo();
    }

    public function sections(): HasMany
    {
        return $this->hasMany(BOQSection::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BOQItem::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(BOQRevision::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function recalculateTotals(): void
    {
        $this->total_amount = $this->items()->sum('amount');
        $markupAmount = $this->total_amount * ($this->markup_percentage / 100);
        $discountAmount = $this->total_amount * ($this->discount_percentage / 100);
        $this->final_amount = $this->total_amount + $markupAmount - $discountAmount;
        $this->save();
    }
}
