<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorrespondenceAttachment extends Model
{
    protected $fillable = [
        'correspondence_id',
        'name',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'is_main_document',
        'uploaded_by',
    ];

    protected $casts = [
        'is_main_document' => 'boolean',
    ];

    public function correspondence(): BelongsTo
    {
        return $this->belongsTo(Correspondence::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
