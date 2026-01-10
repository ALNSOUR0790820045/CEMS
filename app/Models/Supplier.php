<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'tax_number',
        'commercial_registration',
        'rating',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function procurementPackages()
    {
        return $this->belongsToMany(TenderProcurementPackage::class, 'tender_procurement_suppliers')
            ->withPivot(['quoted_price', 'delivery_days', 'payment_terms', 'technical_compliance', 'score', 'is_recommended'])
            ->withTimestamps();
    }
}
