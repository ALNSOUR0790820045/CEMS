<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhotoReport extends Model
{
    protected $fillable = [
        'report_number',
        'project_id',
        'name',
        'description',
        'report_type',
        'period_from',
        'period_to',
        'cover_page_text',
        'status',
        'generated_path',
        'created_by_id',
        'company_id',
    ];

    protected $casts = [
        'period_from' => 'date',
        'period_to' => 'date',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PhotoReportItem::class);
    }

    // Auto-generate report number
    public static function generateReportNumber($year = null): string
    {
        $year = $year ?? date('Y');
        $lastReport = self::where('report_number', 'like', "PR-{$year}-%")
            ->orderBy('report_number', 'desc')
            ->first();

        if ($lastReport) {
            $lastNumber = (int) substr($lastReport->report_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('PR-%s-%04d', $year, $newNumber);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($report) {
            if (empty($report->report_number)) {
                $report->report_number = self::generateReportNumber();
            }
        });
    }
}
