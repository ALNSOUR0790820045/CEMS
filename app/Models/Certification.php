<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Certification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'certification_number',
        'name',
        'name_en',
        'type',
        'category',
        'issuing_authority',
        'issue_date',
        'expiry_date',
        'renewal_date',
        'status',
        'reference_type',
        'reference_id',
        'cost',
        'currency_id',
        'attachment_path',
        'reminder_days',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'renewal_date' => 'date',
        'cost' => 'decimal:2',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(CertificationRenewal::class);
    }

    // Polymorphic reference (to project, employee, equipment, etc.)
    public function reference()
    {
        if ($this->reference_type === 'project') {
            return $this->belongsTo(Project::class, 'reference_id');
        } elseif ($this->reference_type === 'employee') {
            return $this->belongsTo(Employee::class, 'reference_id');
        }
        
        return null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
            ->orWhere(function ($q) {
                $q->where('expiry_date', '<', Carbon::now())
                  ->where('status', '!=', 'cancelled');
            });
    }

    public function scopeExpiring($query, $days = 30)
    {
        return $query->where('status', 'active')
            ->where('expiry_date', '<=', Carbon::now()->addDays($days))
            ->where('expiry_date', '>=', Carbon::now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByReference($query, $type, $id)
    {
        return $query->where('reference_type', $type)
            ->where('reference_id', $id);
    }

    // Accessors
    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expiry_date) {
            return null;
        }
        return Carbon::now()->diffInDays($this->expiry_date, false);
    }

    public function getIsExpiringAttribute()
    {
        $days = $this->days_until_expiry;
        return $days !== null && $days <= $this->reminder_days && $days > 0;
    }

    public function getIsExpiredAttribute()
    {
        $days = $this->days_until_expiry;
        return $days !== null && $days < 0;
    }

    public function getTypeNameAttribute()
    {
        $types = [
            'license' => 'رخصة',
            'permit' => 'تصريح',
            'certificate' => 'شهادة',
            'registration' => 'تسجيل',
            'insurance' => 'تأمين',
        ];
        
        return $types[$this->type] ?? $this->type;
    }

    public function getCategoryNameAttribute()
    {
        $categories = [
            'company' => 'الشركة',
            'project' => 'المشروع',
            'employee' => 'الموظف',
            'equipment' => 'المعدات',
            'safety' => 'السلامة',
        ];
        
        return $categories[$this->category] ?? $this->category;
    }

    public function getStatusNameAttribute()
    {
        $statuses = [
            'active' => 'نشط',
            'expired' => 'منتهي',
            'pending_renewal' => 'قيد التجديد',
            'suspended' => 'معلق',
            'cancelled' => 'ملغي',
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }

    // Methods
    public static function generateCertificationNumber()
    {
        return DB::transaction(function () {
            $year = Carbon::now()->year;
            $lastCertification = self::whereYear('created_at', $year)
                ->latest('id')
                ->lockForUpdate()
                ->first();
            
            $number = $lastCertification ? ((int) substr($lastCertification->certification_number, -4)) + 1 : 1;
            
            return sprintf('CERT-%d-%04d', $year, $number);
        });
    }

    public function renew($newExpiryDate, $cost = null, $processedBy = null, $notes = null)
    {
        return DB::transaction(function () use ($newExpiryDate, $cost, $processedBy, $notes) {
            // Create renewal record
            $renewal = CertificationRenewal::create([
                'renewal_number' => CertificationRenewal::generateRenewalNumber(),
                'certification_id' => $this->id,
                'old_expiry_date' => $this->expiry_date,
                'new_expiry_date' => $newExpiryDate,
                'renewal_cost' => $cost,
                'renewal_date' => Carbon::now(),
                'processed_by_id' => $processedBy,
                'notes' => $notes,
            ]);

            // Update certification
            $this->update([
                'expiry_date' => $newExpiryDate,
                'renewal_date' => Carbon::now(),
                'status' => 'active',
            ]);

            return $renewal;
        });
    }
}
