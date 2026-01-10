<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryBalance extends Model
{
    protected $fillable = [
        'material_id',
        'warehouse_id',
        'quantity_on_hand',
        'quantity_reserved',
        'last_cost',
        'average_cost',
        'last_transaction_date',
        'company_id',
    ];

    protected $casts = [
        'quantity_on_hand' => 'decimal:2',
        'quantity_reserved' => 'decimal:2',
        'quantity_available' => 'decimal:2',
        'last_cost' => 'decimal:2',
        'average_cost' => 'decimal:2',
        'total_value' => 'decimal:2',
        'last_transaction_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
