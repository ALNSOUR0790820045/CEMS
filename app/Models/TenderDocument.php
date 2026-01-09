<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderDocument extends Model
{
    protected $fillable = [
        'tender_id',
        'name',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'source',
        'notes',
        'uploaded_by',
    ];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
