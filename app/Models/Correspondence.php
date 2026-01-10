<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Correspondence extends Model
{
    use SoftDeletes;

    protected $table = 'correspondence';

    protected $fillable = [
        'reference_number',
        'project_id',
        'contract_id',
        'subject',
        'content',
        'type',
        'priority',
        'correspondence_date',
        'from_user_id',
        'to_user_id',
        'status',
    ];

    protected $casts = [
        'correspondence_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
