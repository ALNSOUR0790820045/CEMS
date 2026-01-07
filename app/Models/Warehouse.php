<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'warehouse_code',
        'warehouse_name',
        'warehouse_type',
        'location',
        'address',
        'city',
        'country',
        'phone',
        'manager_id',
        'manager_name',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function manager(): BelongsTo { return $this->belongsTo(User::class, 'manager_id'); }
    public function grns(): HasMany { return $this->hasMany(GRN::class); }
    public function inventoryBalances(): HasMany { return $this->hasMany(InventoryBalance::class); }
    public function inventoryTransactions(): HasMany { return $this->hasMany(InventoryTransaction::class); }
    public function locations(): HasMany { return $this->hasMany(WarehouseLocation:: class); }
    public function stock(): HasMany { return $this->hasMany(WarehouseStock::class); }
}