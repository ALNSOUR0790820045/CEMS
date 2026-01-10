<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TenderProcurementPackage extends Model
{
    protected $fillable = [
        'tender_id',
        'package_code',
        'package_name',
        'description',
        'procurement_type',
        'category',
        'scope_of_work',
        'quantities',
        'estimated_value',
        'required_by_date',
        'lead_time_days',
        'procurement_start',
        'strategy',
        'requires_technical_specs',
        'requires_samples',
        'requires_warranty',
        'warranty_months',
        'status',
        'responsible_id',
    ];

    protected $casts = [
        'quantities' => 'array',
        'estimated_value' => 'decimal:2',
        'required_by_date' => 'date',
        'procurement_start' => 'date',
        'requires_technical_specs' => 'boolean',
        'requires_samples' => 'boolean',
        'requires_warranty' => 'boolean',
    ];

    // Relationships
    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'tender_procurement_suppliers')
            ->withPivot(['quoted_price', 'delivery_days', 'payment_terms', 'technical_compliance', 'score', 'is_recommended'])
            ->withTimestamps();
    }

    public function procurementSuppliers(): HasMany
    {
        return $this->hasMany(TenderProcurementSupplier::class);
    }

    public function longLeadItems(): HasMany
    {
        return $this->hasMany(TenderLongLeadItem::class);
    }

    // Accessors & Helpers
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'planned' => 'مخطط',
            'rfq_prepared' => 'تم إعداد الطلب',
            'quotations_received' => 'تم استلام العروض',
            'evaluated' => 'تم التقييم',
            'approved' => 'معتمد',
            default => $this->status,
        };
    }

    public function getProcurementTypeLabel(): string
    {
        return match($this->procurement_type) {
            'materials' => 'مواد',
            'equipment' => 'معدات',
            'subcontract' => 'مقاولة فرعية',
            'services' => 'خدمات',
            'rental' => 'إيجار',
            default => $this->procurement_type,
        };
    }

    public function getCategoryLabel(): string
    {
        return match($this->category) {
            'civil' => 'مدني',
            'structural' => 'إنشائي',
            'architectural' => 'معماري',
            'electrical' => 'كهربائي',
            'mechanical' => 'ميكانيكي',
            'plumbing' => 'صحي',
            'finishing' => 'تشطيبات',
            'other' => 'أخرى',
            default => $this->category ?? 'غير محدد',
        };
    }

    public function getStrategyLabel(): string
    {
        return match($this->strategy) {
            'competitive_bidding' => 'منافسة',
            'direct_purchase' => 'شراء مباشر',
            'framework_agreement' => 'اتفاقية إطار',
            'preferred_supplier' => 'مورد مفضل',
            default => $this->strategy,
        };
    }
}
