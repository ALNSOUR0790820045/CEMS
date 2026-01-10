<?php

namespace App\Services;

use App\Models\ChangeOrder;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ChangeOrderService
{
    /**
     * Submit a change order for approval.
     */
    public function submit(ChangeOrder $changeOrder): void
    {
        // Assign PM if not already assigned
        if (!$changeOrder->pm_user_id) {
            $changeOrder->pm_user_id = $this->assignProjectManager($changeOrder);
        }
        
        // Update status
        $changeOrder->status = 'pending_pm';
        $changeOrder->save();
        
        // Create audit log
        $this->createAuditLog($changeOrder, 'submitted');
        
        // Send notifications (placeholder for future implementation)
        // $this->sendNotifications($changeOrder);
    }

    /**
     * Assign a project manager to the change order.
     */
    protected function assignProjectManager(ChangeOrder $changeOrder): int
    {
        // Try to get PM from project's company
        $pmUser = $changeOrder->project->load('company')->company
            ->users()
            ->whereHas('roles', function($q) {
                $q->where('name', 'project-manager');
            })
            ->first();

        if ($pmUser) {
            return $pmUser->id;
        }
        
        // Fallback to current user
        return auth()->id();
    }

    /**
     * Calculate fees for the change order.
     */
    public function calculateFees(ChangeOrder $changeOrder): void
    {
        $changeOrder->calculateFees();
    }

    /**
     * Create audit log entry.
     */
    protected function createAuditLog(ChangeOrder $changeOrder, string $action): void
    {
        Log::info('Change Order ' . $action, [
            'change_order_id' => $changeOrder->id,
            'co_number' => $changeOrder->co_number,
            'action' => $action,
            'user_id' => auth()->id(),
            'status' => $changeOrder->status,
        ]);
    }

    /**
     * Approve change order.
     */
    public function approve(ChangeOrder $changeOrder, User $user, string $role): void
    {
        $statusMap = [
            'pm' => 'pending_technical',
            'technical' => 'pending_consultant',
            'consultant' => 'pending_client',
            'client' => 'approved',
        ];
        
        // Update approval fields based on role
        $changeOrder->update([
            "{$role}_user_id" => $user->id,
            "{$role}_signed_at" => now(),
            "{$role}_decision" => 'approved',
            'status' => $statusMap[$role] ?? 'approved',
        ]);
        
        $this->createAuditLog($changeOrder, "approved_by_{$role}");
    }

    /**
     * Reject change order.
     */
    public function reject(ChangeOrder $changeOrder, User $user, string $role, string $reason): void
    {
        $changeOrder->update([
            "{$role}_user_id" => $user->id,
            "{$role}_signed_at" => now(),
            "{$role}_decision" => 'rejected',
            "{$role}_comments" => $reason,
            'status' => 'rejected',
        ]);
        
        $this->createAuditLog($changeOrder, "rejected_by_{$role}");
    }
}
