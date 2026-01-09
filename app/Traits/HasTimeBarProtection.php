<?php

namespace App\Traits;

use App\Models\TimeBarProtectionSetting;
use Illuminate\Support\Facades\Auth;

trait HasTimeBarProtection
{
    /**
     * Check if this record is protected by time bar rules.
     *
     * @return bool
     */
    public function isTimeBarProtected(): bool
    {
        $entityType = $this->getEntityType();
        $companyId = $this->getCompanyId();

        $setting = TimeBarProtectionSetting::query()
            ->where('entity_type', $entityType)
            ->where('is_active', true)
            ->where(function ($query) use ($companyId) {
                $query->whereNull('company_id')
                    ->orWhere('company_id', $companyId);
            })
            ->orderByDesc('company_id') // Prioritize company-specific settings
            ->first();

        if (!$setting) {
            return false;
        }

        return $setting->isProtected($this->created_at);
    }

    /**
     * Check if the current user can bypass time bar protection.
     *
     * @return bool
     */
    public function canBypassTimeBarProtection(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        $entityType = $this->getEntityType();
        $companyId = $this->getCompanyId();

        $setting = TimeBarProtectionSetting::query()
            ->where('entity_type', $entityType)
            ->where('is_active', true)
            ->where(function ($query) use ($companyId) {
                $query->whereNull('company_id')
                    ->orWhere('company_id', $companyId);
            })
            ->orderByDesc('company_id')
            ->first();

        if (!$setting || !$setting->excluded_roles) {
            return false;
        }

        // Check if user has any of the excluded roles
        foreach ($setting->excluded_roles as $role) {
            if ($user->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if this record can be edited.
     *
     * @return bool
     */
    public function canEdit(): bool
    {
        if (!$this->isTimeBarProtected()) {
            return true;
        }

        if ($this->canBypassTimeBarProtection()) {
            return true;
        }

        $entityType = $this->getEntityType();
        $companyId = $this->getCompanyId();

        $setting = TimeBarProtectionSetting::query()
            ->where('entity_type', $entityType)
            ->where('is_active', true)
            ->where(function ($query) use ($companyId) {
                $query->whereNull('company_id')
                    ->orWhere('company_id', $companyId);
            })
            ->orderByDesc('company_id')
            ->first();

        // If protection type is 'view_only' or 'full_lock', cannot edit
        if ($setting && in_array($setting->protection_type, ['view_only', 'full_lock'])) {
            return false;
        }

        // If protection type is 'approval_required', return false
        // (actual approval logic would be implemented separately)
        return false;
    }

    /**
     * Get the protection type for this record.
     *
     * @return string|null
     */
    public function getProtectionType(): ?string
    {
        if (!$this->isTimeBarProtected()) {
            return null;
        }

        $entityType = $this->getEntityType();
        $companyId = $this->getCompanyId();

        $setting = TimeBarProtectionSetting::query()
            ->where('entity_type', $entityType)
            ->where('is_active', true)
            ->where(function ($query) use ($companyId) {
                $query->whereNull('company_id')
                    ->orWhere('company_id', $companyId);
            })
            ->orderByDesc('company_id')
            ->first();

        return $setting?->protection_type;
    }

    /**
     * Get the entity type for time bar protection.
     * Override this method in your model if needed.
     *
     * @return string
     */
    protected function getEntityType(): string
    {
        return strtolower(class_basename($this));
    }

    /**
     * Get the company ID for time bar protection.
     * Override this method in your model if needed.
     *
     * @return int|null
     */
    protected function getCompanyId(): ?int
    {
        if (property_exists($this, 'company_id')) {
            return $this->company_id;
        }

        if (method_exists($this, 'company') && $this->company) {
            return $this->company->id;
        }

        return null;
    }
}
