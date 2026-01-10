<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'inspection_number',
        'inspection_request_id',
        'project_id',
        'inspection_type_id',
        'inspection_date',
        'inspection_time',
        'location',
        'work_area',
        'description',
        'inspector_id',
        'witness_id',
        'contractor_rep',
        'consultant_rep',
        'result',
        'overall_score',
        'defects_found',
        'follow_up_required',
        'follow_up_date',
        'reinspection_of_id',
        'status',
        'approved_by_id',
        'approved_at',
        'remarks',
        'company_id',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'follow_up_date' => 'date',
        'approved_at' => 'datetime',
        'follow_up_required' => 'boolean',
        'overall_score' => 'decimal:2',
        'defects_found' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function inspectionType(): BelongsTo
    {
        return $this->belongsTo(InspectionType::class);
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(InspectionRequest::class, 'inspection_request_id');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function witness(): BelongsTo
    {
        return $this->belongsTo(User::class, 'witness_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function reinspectionOf(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'reinspection_of_id');
    }

    public function reinspections(): HasMany
    {
        return $this->hasMany(Inspection::class, 'reinspection_of_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InspectionItem::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(InspectionAction::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(InspectionPhoto::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->inspection_number)) {
                $model->inspection_number = static::generateInspectionNumber();
            }
        });
    }

    public static function generateInspectionNumber(): string
    {
        $year = date('Y');
        $lastInspection = static::where('inspection_number', 'like', "INS-{$year}-%")
            ->latest('id')
            ->first();

        if ($lastInspection) {
            $lastNumber = (int) substr($lastInspection->inspection_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "INS-{$year}-{$newNumber}";
    }
}
