<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariationOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vo_number',
        'project_id',
        'contract_id',
        'title',
        'description',
        'issue_date',
        'value',
        'currency',
        'time_impact_days',
        'status',
        'issued_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'value' => 'decimal:2',
        'time_impact_days' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
