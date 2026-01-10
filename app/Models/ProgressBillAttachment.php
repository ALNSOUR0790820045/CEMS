<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressBillAttachment extends Model
{
    protected $fillable = [
        'progress_bill_id',
        'attachment_type',
        'file_name',
        'file_path',
        'file_size',
        'uploaded_by_id',
        'description',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    // Relationships
    public function progressBill(): BelongsTo
    {
        return $this->belongsTo(ProgressBill::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }

    // Accessors
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
