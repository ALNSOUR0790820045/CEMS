<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Backup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'filename',
        'path',
        'size',
        'status',
        'error_message',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'size' => 'integer',
    ];

    /**
     * Get the user who created this backup
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute()
    {
        if (!$this->size) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = $this->size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    /**
     * Get backup duration
     */
    public function getDurationAttribute()
    {
        if (!$this->completed_at) {
            return 'N/A';
        }

        $duration = $this->created_at->diffInSeconds($this->completed_at);

        if ($duration < 60) {
            return $duration . ' ثانية';
        } elseif ($duration < 3600) {
            return round($duration / 60, 1) . ' دقيقة';
        } else {
            return round($duration / 3600, 1) . ' ساعة';
        }
    }

    /**
     * Download backup file
     */
    public function download()
    {
        $fullPath = storage_path('app/' . $this->path);

        if (!file_exists($fullPath)) {
            throw new \Exception('الملف غير موجود');
        }

        return response()->download($fullPath, $this->filename);
    }

    /**
     * Override delete to remove physical file
     */
    public function delete()
    {
        $fullPath = storage_path('app/' . $this->path);

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        return parent::delete();
    }

    /**
     * Scope to get only database backups
     */
    public function scopeDatabase($query)
    {
        return $query->where('type', 'database');
    }

    /**
     * Scope to get only files backups
     */
    public function scopeFiles($query)
    {
        return $query->where('type', 'files');
    }

    /**
     * Scope to get only full backups
     */
    public function scopeFull($query)
    {
        return $query->where('type', 'full');
    }

    /**
     * Scope to get only completed backups
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get only failed backups
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
