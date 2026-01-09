<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EotClaim extends Model
{
    protected $fillable = [
        'project_id',
        'time_bar_claim_id',
        'eot_number',
        'claim_date',
        'event_start_date',
        'event_end_date',
        'event_duration_days',
        'requested_days',
        'requested_new_completion_date',
        'approved_days',
        'approved_new_completion_date',
        'rejected_days',
        'cause_category',
        'event_description',
        'impact_description',
        'justification',
        'fidic_clause_reference',
        'has_prolongation_costs',
        'site_overheads',
        'head_office_overheads',
        'equipment_costs',
        'financing_costs',
        'other_costs',
        'total_prolongation_cost',
        'status',
        'prepared_by',
        'submitted_at',
        'consultant_reviewed_by',
        'consultant_reviewed_at',
        'consultant_comments',
        'client_approved_by',
        'client_approved_at',
        'client_comments',
        'supporting_documents',
        'original_completion_date',
        'current_completion_date',
        'affects_critical_path',
    ];

    protected $casts = [
        'claim_date' => 'date',
        'event_start_date' => 'date',
        'event_end_date' => 'date',
        'requested_new_completion_date' => 'date',
        'approved_new_completion_date' => 'date',
        'original_completion_date' => 'date',
        'current_completion_date' => 'date',
        'submitted_at' => 'datetime',
        'consultant_reviewed_at' => 'datetime',
        'client_approved_at' => 'datetime',
        'has_prolongation_costs' => 'boolean',
        'affects_critical_path' => 'boolean',
        'supporting_documents' => 'array',
        'site_overheads' => 'decimal:2',
        'head_office_overheads' => 'decimal:2',
        'equipment_costs' => 'decimal:2',
        'financing_costs' => 'decimal:2',
        'other_costs' => 'decimal:2',
        'total_prolongation_cost' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function timeBarClaim(): BelongsTo
    {
        return $this->belongsTo(TimeBarClaim::class);
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function consultantReviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consultant_reviewed_by');
    }

    public function clientApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_approved_by');
    }

    public function prolongationCostItems(): HasMany
    {
        return $this->hasMany(ProlongationCostItem::class);
    }

    public function affectedActivities(): HasMany
    {
        return $this->hasMany(EotAffectedActivity::class);
    }

    // Helper method to calculate total prolongation cost
    public function calculateTotalProlongationCost(): float
    {
        return $this->prolongationCostItems()->sum('total_cost');
    }

    // Helper method to get status badge
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft' => 'مسودة',
            'submitted' => 'مقدم',
            'under_review_consultant' => 'قيد مراجعة الاستشاري',
            'under_review_client' => 'قيد مراجعة العميل',
            'partially_approved' => 'موافقة جزئية',
            'approved' => 'معتمد',
            'rejected' => 'مرفوض',
            'disputed' => 'متنازع عليه',
            default => $this->status,
        };
    }

    // Helper method to get cause category label
    public function getCauseCategoryLabelAttribute(): string
    {
        return match($this->cause_category) {
            'client_delay' => 'تأخير المالك',
            'consultant_delay' => 'تأخير الاستشاري',
            'variations' => 'أوامر تغييرية',
            'unforeseeable_conditions' => 'ظروف غير منظورة',
            'force_majeure' => 'قوة قاهرة',
            'weather' => 'طقس استثنائي',
            'delays_by_others' => 'تأخير الآخرين',
            'suspension' => 'إيقاف الأعمال',
            'late_drawings' => 'تأخر المخططات',
            'late_approvals' => 'تأخر الموافقات',
            'other' => 'أخرى',
            default => $this->cause_category,
        };
    }
}
