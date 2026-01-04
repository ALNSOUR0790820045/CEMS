<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tender extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tender_number',
        'reference_number',
        'name',
        'name_en',
        'description',
        'client_id',
        'client_name',
        'client_contact',
        'client_phone',
        'client_email',
        'type',
        'category',
        'sector',
        'location',
        'city',
        'country',
        'announcement_date',
        'documents_deadline',
        'questions_deadline',
        'submission_deadline',
        'submission_time',
        'opening_date',
        'expected_award_date',
        'estimated_value',
        'our_offer_value',
        'winning_value',
        'currency',
        'documents_cost',
        'bid_bond_amount',
        'bid_bond_percentage',
        'status',
        'priority',
        'go_decision',
        'go_decision_notes',
        'go_decided_by',
        'go_decided_at',
        'winner_name',
        'winner_value',
        'loss_reason',
        'project_id',
        'bid_bond_id',
        'assigned_to',
        'estimator_id',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'announcement_date' => 'date',
        'documents_deadline' => 'date',
        'questions_deadline' => 'date',
        'submission_deadline' => 'date',
        'opening_date' => 'date',
        'expected_award_date' => 'date',
        'estimated_value' => 'decimal:2',
        'our_offer_value' => 'decimal:2',
        'winning_value' => 'decimal:2',
        'winner_value' => 'decimal:2',
        'documents_cost' => 'decimal:2',
        'bid_bond_amount' => 'decimal:2',
        'bid_bond_percentage' => 'decimal:2',
        'go_decision' => 'boolean',
        'go_decided_at' => 'datetime',
    ];

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function bidBond(): BelongsTo
    {
        return $this->belongsTo(Guarantee::class, 'bid_bond_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function estimator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'estimator_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function goDecidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'go_decided_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TenderDocument::class);
    }

    public function competitors(): HasMany
    {
        return $this->hasMany(TenderCompetitor::class);
    }

    public function timeline(): HasMany
    {
        return $this->hasMany(TenderTimeline::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(TenderQuestion::class);
    }

    // Helper methods
    public function isExpiringSoon(int $days = 7): bool
    {
        if (! $this->submission_deadline) {
            return false;
        }

        return $this->submission_deadline->diffInDays(now()) <= $days &&
               $this->submission_deadline >= now();
    }

    public function canConvertToProject(): bool
    {
        return $this->status === 'won' && $this->project_id === null;
    }

    public function getDaysUntilDeadline(): ?int
    {
        if (! $this->submission_deadline) {
            return null;
        }

        $diff = $this->submission_deadline->diffInDays(now(), false);

        return (int) $diff;
    }
}
