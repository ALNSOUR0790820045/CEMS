<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Correspondence extends Model
{
    use SoftDeletes;

    protected $table = 'correspondence';

    protected $fillable = [
        'reference_number',
        'type',
        'category',
        'priority',
        'subject',
        'summary',
        'content',
        'from_entity',
        'from_person',
        'from_position',
        'to_entity',
        'to_person',
        'to_position',
        'project_id',
        'contract_id',
        'tender_id',
        'client_id',
        'vendor_id',
        'document_date',
        'received_date',
        'sent_date',
        'response_required_date',
        'response_date',
        'their_reference',
        'reply_to_id',
        'parent_id',
        'status',
        'requires_response',
        'is_confidential',
        'created_by',
        'approved_by',
        'assigned_to',
        'notes',
    ];

    protected $casts = [
        'document_date' => 'date',
        'received_date' => 'date',
        'sent_date' => 'date',
        'response_required_date' => 'date',
        'response_date' => 'date',
        'requires_response' => 'boolean',
        'is_confidential' => 'boolean',
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(CorrespondenceAttachment::class);
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(CorrespondenceDistribution::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(CorrespondenceAction::class);
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Correspondence::class, 'reply_to_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Correspondence::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Correspondence::class, 'reply_to_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Correspondence::class, 'parent_id');
    }

    // Scopes
    public function scopeIncoming($query)
    {
        return $query->where('type', 'incoming');
    }

    public function scopeOutgoing($query)
    {
        return $query->where('type', 'outgoing');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending_response')
                     ->where('requires_response', true);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending_response')
                     ->where('requires_response', true)
                     ->whereNotNull('response_required_date')
                     ->whereDate('response_required_date', '<', now());
    }

    // Helper methods
    public function isOverdue(): bool
    {
        return $this->status === 'pending_response' 
               && $this->requires_response 
               && $this->response_required_date 
               && $this->response_required_date->isPast();
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'pending_approval']);
    }

    public function canBeSent(): bool
    {
        return $this->status === 'approved' && $this->type === 'outgoing';
    }
}
