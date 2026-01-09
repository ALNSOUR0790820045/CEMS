<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnforeseeableCondition extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'condition_number',
        'project_id',
        'contract_id',
        'title',
        'description',
        'location',
        'location_latitude',
        'location_longitude',
        'condition_type',
        'discovery_date',
        'notice_date',
        'inspection_date',
        'contractual_clause',
        'impact_description',
        'estimated_delay_days',
        'estimated_cost_impact',
        'currency',
        'tender_assumptions',
        'site_investigation_data',
        'actual_conditions',
        'difference_analysis',
        'immediate_measures',
        'proposed_solution',
        'status',
        'time_bar_event_id',
        'claim_id',
        'eot_id',
        'reported_by',
        'verified_by',
        'notes',
    ];

    protected $casts = [
        'discovery_date' => 'date',
        'notice_date' => 'date',
        'inspection_date' => 'date',
        'estimated_delay_days' => 'integer',
        'estimated_cost_impact' => 'decimal:2',
        'location_latitude' => 'decimal:8',
        'location_longitude' => 'decimal:8',
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

    public function timeBarEvent(): BelongsTo
    {
        return $this->belongsTo(TimeBarEvent::class);
    }

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    public function eotRequest(): BelongsTo
    {
        return $this->belongsTo(EotRequest::class, 'eot_id');
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function evidence(): HasMany
    {
        return $this->hasMany(UnforeseeableConditionEvidence::class, 'condition_id');
    }
}
