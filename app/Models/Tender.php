<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Tender extends Model
{
    protected $fillable = [
        'tender_number',
        'reference_number',
        'tender_name',
        'tender_name_en',
        'description',
        'description_en',
        'owner_name',
        'owner_contact',
        'owner_email',
        'owner_phone',
        'country_id',
        'city_id',
        'project_location',
        'tender_type',
        'contract_type',
        'estimated_value',
        'currency_id',
        'estimated_duration_months',
        'announcement_date',
        'document_sale_start',
        'document_sale_end',
        'document_price',
        'site_visit_date',
        'site_visit_time',
        'questions_deadline',
        'submission_deadline',
        'submission_time',
        'opening_date',
        'opening_time',
        'requires_bid_bond',
        'bid_bond_percentage',
        'bid_bond_amount',
        'bid_bond_validity_days',
        'prequalification_requirements',
        'eligibility_criteria',
        'status',
        'participate',
        'participation_decision_notes',
        'decided_by',
        'decision_date',
        'tender_documents',
        'our_documents',
        'notes',
        'assigned_to',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'document_price' => 'decimal:2',
        'bid_bond_percentage' => 'decimal:2',
        'bid_bond_amount' => 'decimal:2',
        'announcement_date' => 'date',
        'document_sale_start' => 'date',
        'document_sale_end' => 'date',
        'site_visit_date' => 'date',
        'questions_deadline' => 'date',
        'submission_deadline' => 'date',
        'opening_date' => 'date',
        'decision_date' => 'date',
        'prequalification_requirements' => 'array',
        'tender_documents' => 'array',
        'our_documents' => 'array',
        'requires_bid_bond' => 'boolean',
        'participate' => 'boolean',
    ];

    // Relationships
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function siteVisits(): HasMany
    {
        return $this->hasMany(TenderSiteVisit::class);
    }

    public function clarifications(): HasMany
    {
        return $this->hasMany(TenderClarification::class);
    }

    public function competitors(): HasMany
    {
        return $this->hasMany(TenderCompetitor::class);
    }

    public function committeeDecisions(): HasMany
    {
        return $this->hasMany(TenderCommitteeDecision::class);
    }

    // Helper methods
    public function getDaysUntilSubmission(): int
    {
        return Carbon::now()->diffInDays($this->submission_deadline, false);
    }

    public function getDeadlineUrgency(): string
    {
        $days = $this->getDaysUntilSubmission();
        
        if ($days < 0) return 'expired';
        if ($days <= 15) return 'critical';
        if ($days <= 30) return 'warning';
        return 'safe';
    }

    public function getDeadlineColor(): string
    {
        return match($this->getDeadlineUrgency()) {
            'critical' => 'red',
            'warning' => 'yellow',
            'safe' => 'green',
            default => 'gray',
        };
    }

    // Auto-generate tender number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tender) {
            if (!$tender->tender_number) {
                $year = date('Y');
                $lastTender = static::whereYear('created_at', $year)
                    ->orderBy('id', 'desc')
                    ->first();
                
                $number = $lastTender ? (int) substr($lastTender->tender_number, -3) + 1 : 1;
                $tender->tender_number = sprintf('TND-%s-%03d', $year, $number);
            }
        });
    }
}
