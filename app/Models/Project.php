<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_number',
        'name',
        'description',
        'client_id',
        'status',
        'start_date',
        'end_date',
        'budget',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
    ];

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }
}
