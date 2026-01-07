<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'transaction_type',
        'material_id',
        'warehouse_id',
        'quantity',
        'unit_cost',
        'unit_price',
        'batch_number',
        'expiry_date',
        'reference_type',
        'reference_id',
        'from_warehouse_id',
        'to_warehouse_id',
        'project_id',
        'notes',
        'company_id',
        'created_by',
        'created_by_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'expiry_date' => 'date',
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'unit_price' => 'decimal: 2',
        'total_value' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent:: boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_number)) {
                $transaction->transaction_number = static::generateTransactionNumber();
            }
        });
    }

    public static function generateTransactionNumber(): string
    {
        $year = date('Y');
        $lastTransaction = static::where('transaction_number', 'like', "INV-{$year}-%")
            ->orderBy('transaction_number', 'desc')
            ->first();

        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->transaction_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "INV-{$year}-{$newNumber}";
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company:: class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}