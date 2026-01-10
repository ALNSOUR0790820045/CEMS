<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiaryMaterial extends Model
{
    protected $fillable = [
        'site_diary_id',
        'material_id',
        'quantity_received',
        'quantity_used',
        'unit_id',
        'supplier_id',
        'delivery_note_number',
        'location_used',
        'notes',
    ];

    protected $casts = [
        'quantity_received' => 'decimal:2',
        'quantity_used' => 'decimal:2',
    ];

    // Relationships
    public function siteDiary(): BelongsTo
    {
        return $this->belongsTo(SiteDiary::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
