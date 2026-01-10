<?php

namespace App\Services;

use App\Models\TimeBarProtectionSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TimeBarProtectionService
{
    /**
     * Get the protection setting for a specific entity type and company.
     *
     * @param string $entityType
     * @param int|null $companyId
     * @return TimeBarProtectionSetting|null
     */
    public function getSetting(string $entityType, ?int $companyId = null): ?TimeBarProtectionSetting
    {
        return TimeBarProtectionSetting::query()
            ->where('entity_type', $entityType)
            ->where('is_active', true)
            ->where(function ($query) use ($companyId) {
                $query->whereNull('company_id')
                    ->orWhere('company_id', $companyId);
            })
            ->orderByDesc('company_id') // Prioritize company-specific settings
            ->first();
    }

    /**
     * Check if a record is protected based on entity type, creation date, and company.
     *
     * @param string $entityType
     * @param Carbon $createdAt
     * @param int|null $companyId
     * @return bool
     */
    public function isProtected(string $entityType, Carbon $createdAt, ?int $companyId = null): bool
    {
        $setting = $this->getSetting($entityType, $companyId);

        if (!$setting) {
            return false;
        }

        return $setting->isProtected($createdAt);
    }

    /**
     * Check if a user can bypass protection for a specific entity type and company.
     *
     * @param string $entityType
     * @param int|null $companyId
     * @param \App\Models\User|null $user
     * @return bool
     */
    public function canBypass(string $entityType, ?int $companyId = null, $user = null): bool
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return false;
        }

        $setting = $this->getSetting($entityType, $companyId);

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
     * Check if a model instance can be edited.
     *
     * @param Model $model
     * @param string $entityType
     * @return bool
     */
    public function canEdit(Model $model, string $entityType): bool
    {
        $companyId = $this->getCompanyIdFromModel($model);

        if (!$this->isProtected($entityType, $model->created_at, $companyId)) {
            return true;
        }

        if ($this->canBypass($entityType, $companyId)) {
            return true;
        }

        $setting = $this->getSetting($entityType, $companyId);

        if (!$setting) {
            return true;
        }

        // If protection type is 'view_only' or 'full_lock', cannot edit
        if (in_array($setting->protection_type, ['view_only', 'full_lock'])) {
            return false;
        }

        // If protection type is 'approval_required', return false
        // (actual approval logic would be implemented separately)
        return false;
    }

    /**
     * Check if a model instance can be deleted.
     *
     * @param Model $model
     * @param string $entityType
     * @return bool
     */
    public function canDelete(Model $model, string $entityType): bool
    {
        $companyId = $this->getCompanyIdFromModel($model);

        if (!$this->isProtected($entityType, $model->created_at, $companyId)) {
            return true;
        }

        if ($this->canBypass($entityType, $companyId)) {
            return true;
        }

        $setting = $this->getSetting($entityType, $companyId);

        if (!$setting) {
            return true;
        }

        // Full lock prevents deletion
        if ($setting->protection_type === 'full_lock') {
            return false;
        }

        // View only and approval required also prevent deletion
        return false;
    }

    /**
     * Get protection information for a model.
     *
     * @param Model $model
     * @param string $entityType
     * @return array
     */
    public function getProtectionInfo(Model $model, string $entityType): array
    {
        $companyId = $this->getCompanyIdFromModel($model);
        $isProtected = $this->isProtected($entityType, $model->created_at, $companyId);
        $canBypass = $this->canBypass($entityType, $companyId);
        $setting = $this->getSetting($entityType, $companyId);

        return [
            'is_protected' => $isProtected,
            'can_bypass' => $canBypass,
            'can_edit' => $this->canEdit($model, $entityType),
            'can_delete' => $this->canDelete($model, $entityType),
            'protection_type' => $setting?->protection_type,
            'protection_days' => $setting?->protection_days,
            'days_since_creation' => $model->created_at ? now()->diffInDays($model->created_at) : 0,
        ];
    }

    /**
     * Get company ID from a model instance.
     *
     * @param Model $model
     * @return int|null
     */
    protected function getCompanyIdFromModel(Model $model): ?int
    {
        if (property_exists($model, 'company_id') && isset($model->company_id)) {
            return $model->company_id;
        }

        if (method_exists($model, 'company') && $model->company) {
            return $model->company->id;
        }

        return null;
    }
}
