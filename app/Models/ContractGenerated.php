<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractGenerated extends Model
{
    protected $table = 'contract_generated';

    protected $fillable = [
        'template_id',
        'project_id',
        'tender_id',
        'contract_title',
        'parties',
        'filled_data',
        'modified_clauses',
        'added_special_conditions',
        'status',
        'generated_file',
        'generated_by',
    ];

    protected $casts = [
        'parties' => 'array',
        'filled_data' => 'array',
        'modified_clauses' => 'array',
        'added_special_conditions' => 'array',
    ];

    // Relationships
    public function template(): BelongsTo
    {
        return $this->belongsTo(ContractTemplate::class, 'template_id');
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
