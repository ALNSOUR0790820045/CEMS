<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GRN extends Model
{
    use SoftDeletes;

    protected $table = 'grns';

    protected $fillable = [
        'grn_number',
        'grn_date',
        'purchase_order_id',
        'vendor_id',
        'warehouse_id',
        'project_id',
        'delivery_note_number',
        'vehicle_number',
        'driver_name',
        'status',
        'total_value',
        'received_by_id',
        'inspected_by_id',
        'inspection_notes',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'grn_date' => 'date',
        'total_value' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($grn) {
            if (!$grn->grn_number) {
                $grn->grn_number = static::generateGRNNumber();
            }
        });
    }

    public static function generateGRNNumber()
    {
        $year = date('Y');
        $lastGRN = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastGRN ? intval(substr($lastGRN->grn_number, -4)) + 1 : 1;

        return 'GRN-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by_id');
    }

    public function inspectedBy()
    {
        return $this->belongsTo(User::class, 'inspected_by_id');
    }

    public function items()
    {
        return $this->hasMany(GRNItem::class);
    }

    public function calculateTotalValue()
    {
        $this->total_value = $this->items()->sum('total_amount');
        $this->save();
    }
}
