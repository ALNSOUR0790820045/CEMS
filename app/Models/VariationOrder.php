<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariationOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vo_number',
        'project_id',
        'contract_id',
        'sequence_number',
        'title',
        'description',
        'justification',
        'type',
        'source',
        'estimated_value',
        'quoted_value',
        'approved_value',
        'executed_value',
        'currency',
        'time_impact_days',
        'extension_approved',
        'approved_extension_days',
        'identification_date',
        'submission_date',
        'client_response_date',
        'approval_date',
        'execution_start_date',
        'execution_end_date',
        'status',
        'priority',
        'requested_by',
        'prepared_by',
        'approved_by',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'quoted_value' => 'decimal:2',
        'approved_value' => 'decimal:2',
        'executed_value' => 'decimal:2',
        'extension_approved' => 'boolean',
        'identification_date' => 'date',
        'submission_date' => 'date',
        'client_response_date' => 'date',
        'approval_date' => 'date',
        'execution_start_date' => 'date',
        'execution_end_date' => 'date',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function preparedBy()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(VariationOrderItem::class);
    }

    public function attachments()
    {
        return $this->hasMany(VariationOrderAttachment::class);
    }

    public function timeline()
    {
        return $this->hasMany(VariationOrderTimeline::class);
    }

    // Helper methods
    public function generateVoNumber()
    {
        if (! $this->project) {
            throw new \Exception('Project relationship is required to generate VO number');
        }

        $project = $this->project;
        $sequence = $this->sequence_number ?? 1;

        return sprintf('VO-%s-%03d', $project->code, $sequence);
    }

    public function addTimelineEntry($action, $fromStatus = null, $toStatus = null, $notes = null, $performedBy = null)
    {
        return $this->timeline()->create([
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'notes' => $notes,
            'performed_by' => $performedBy ?? auth()->id() ?? $this->requested_by,
        ]);
    }
}
