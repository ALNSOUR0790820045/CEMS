<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Document $document): bool
    {
        // Check if document belongs to the same company
        if ($user->company_id !== $document->company_id) {
            return false;
        }

        // If document is confidential, check access rights
        if ($document->is_confidential) {
            return $this->hasAccess($user, $document, ['view', 'download', 'edit', 'delete']);
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Document $document): bool
    {
        // Check if document belongs to the same company
        if ($user->company_id !== $document->company_id) {
            return false;
        }

        // If document is confidential, check access rights
        if ($document->is_confidential) {
            return $this->hasAccess($user, $document, ['edit', 'delete']);
        }

        // Check if user is the uploader
        return $user->id === $document->uploaded_by_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): bool
    {
        // Check if document belongs to the same company
        if ($user->company_id !== $document->company_id) {
            return false;
        }

        // If document is confidential, check access rights
        if ($document->is_confidential) {
            return $this->hasAccess($user, $document, ['delete']);
        }

        // Check if user is the uploader
        return $user->id === $document->uploaded_by_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Document $document): bool
    {
        return $this->delete($user, $document);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Document $document): bool
    {
        return $this->delete($user, $document);
    }

    /**
     * Check if user has specific access level to document.
     */
    private function hasAccess(User $user, Document $document, array $accessLevels): bool
    {
        // Check user-specific access
        $userAccess = $document->accessRights()
            ->where('user_id', $user->id)
            ->whereIn('access_level', $accessLevels)
            ->exists();

        if ($userAccess) {
            return true;
        }

        // Check role-based access
        $userRoleIds = $user->roles->pluck('id')->toArray();
        $roleAccess = $document->accessRights()
            ->whereIn('role_id', $userRoleIds)
            ->whereIn('access_level', $accessLevels)
            ->exists();

        return $roleAccess;
    }
}
