<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Claim extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'claim_number',
        'project_id',
        'contract_id',
        'sequence_number',
        'title',
        'description',
        'contractual_basis',
        'facts',
        'type',
        'cause',
        'claimed_amount',
        'claimed_days',
        'assessed_amount',
        'assessed_days',
        'approved_amount',
        'approved_days',
        'currency',
        'event_start_date',
        'event_end_date',
        'notice_date',
        'submission_date',
        'response_due_date',
        'response_date',
        'resolution_date',
        'status',
        'priority',
        'prepared_by',
        'reviewed_by',
        'client_response',
        'resolution_notes',
        'lessons_learned',
    ];

    protected $casts = [
        'claimed_amount' => 'decimal:2',
        'assessed_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'claimed_days' => 'integer',
        'assessed_days' => 'integer',
        'approved_days' => 'integer',
        'sequence_number' => 'integer',
        'event_start_date' => 'date',
        'event_end_date' => 'date',
        'notice_date' => 'date',
        'submission_date' => 'date',
        'response_due_date' => 'date',
        'response_date' => 'date',
        'resolution_date' => 'date',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(ClaimEvent::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ClaimDocument::class);
    }

    public function timeline(): HasMany
    {
        return $this->hasMany(ClaimTimeline::class);
    }

    public function correspondence(): HasMany
    {
        return $this->hasMany(ClaimCorrespondence::class);
    }

    // Helper methods
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'identified' => 'تم تحديده',
            'notice_sent' => 'تم إرسال الإشعار',
            'documenting' => 'قيد التوثيق',
            'submitted' => 'مقدم',
            'under_review' => 'قيد المراجعة',
            'negotiating' => 'قيد التفاوض',
            'approved' => 'معتمد',
            'partially_approved' => 'معتمد جزئياً',
            'rejected' => 'مرفوض',
            'withdrawn' => 'مسحوب',
            'arbitration' => 'تحكيم',
            'litigation' => 'تقاضي',
            'settled' => 'تمت التسوية',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getTypeLabelAttribute(): string
    {
        $labels = [
            'time_extension' => 'تمديد وقت',
            'cost_compensation' => 'تعويض مالي',
            'time_and_cost' => 'وقت ومال',
            'acceleration' => 'تسريع',
            'disruption' => 'إعاقة',
            'prolongation' => 'إطالة',
            'loss_of_productivity' => 'فقدان الإنتاجية',
        ];

        return $labels[$this->type] ?? $this->type;
    }

    public function getCauseLabelAttribute(): string
    {
        $labels = [
            'client_delay' => 'تأخير العميل',
            'design_changes' => 'تغييرات التصميم',
            'differing_conditions' => 'ظروف مختلفة',
            'force_majeure' => 'قوة قاهرة',
            'suspension' => 'إيقاف',
            'late_payment' => 'تأخر الدفع',
            'acceleration_order' => 'أمر بالتسريع',
            'other' => 'أخرى',
        ];

        return $labels[$this->cause] ?? $this->cause;
    }
}
