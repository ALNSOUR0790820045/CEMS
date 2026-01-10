<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Relations\BelongsTo;
=======
>>>>>>> origin/main
use Illuminate\Database\Eloquent\SoftDeletes;

class Claim extends Model
{
    use SoftDeletes;

<<<<<<< HEAD
    protected $fillable = [
        'claim_number',
        'project_id',
        'contract_id',
        'title',
        'description',
        'claim_date',
        'claimed_amount',
        'approved_amount',
        'currency',
        'claim_type',
        'status',
        'submitted_by',
    ];

    protected $casts = [
        'claim_date' => 'date',
        'claimed_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
=======
    protected $fillable = ['claim_number', 'title'];
>>>>>>> origin/main
}
