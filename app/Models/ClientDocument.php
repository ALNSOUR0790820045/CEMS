<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ClientDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'document_type',
        'document_name',
        'file_path',
        'file_size',
        'mime_type',
        'issue_date',
        'expiry_date',
        'notes',
        'uploaded_by_id',
        'company_id',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays($days))
            ->where('expiry_date', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    // Accessors
    public function getIsExpiredAttribute(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        return $this->expiry_date->isPast();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        return $this->expiry_date->isFuture() && $this->expiry_date->diffInDays(now()) <= 30;
    }

    /**
     * Delete file when document is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($document) {
            if ($document->file_path && Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }
        });
    }
}
