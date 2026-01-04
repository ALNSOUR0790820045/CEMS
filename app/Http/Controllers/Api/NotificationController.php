<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::query()
            ->with(['user', 'role', 'company']);

        // Filter by user or role
        if ($request->user()) {
            $userRoles = $request->user()->roles->pluck('id');
            $query->where(function ($q) use ($request, $userRoles) {
                $q->where('user_id', $request->user()->id)
                  ->orWhereIn('role_id', $userRoles);
            });
        }

        // Filter by read status
        if ($request->has('unread_only') && $request->unread_only) {
            $query->unread();
        }

        // Filter by notification type
        if ($request->has('notification_type')) {
            $query->where('notification_type', $request->notification_type);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Order by most recent
        $query->orderBy('created_at', 'desc');

        // Paginate results
        $perPage = $request->input('per_page', 15);
        $notifications = $query->paginate($perPage);

        return response()->json($notifications);
    }

    public function markRead(Notification $notification)
    {
        $notification->markAsRead();
        
        return response()->json([
            'message' => 'Notification marked as read',
            'notification' => $notification->fresh(),
        ]);
    }

    public function markAllRead(Request $request)
    {
        $query = Notification::unread();

        if ($request->user()) {
            $userRoles = $request->user()->roles->pluck('id');
            $query->where(function ($q) use ($request, $userRoles) {
                $q->where('user_id', $request->user()->id)
                  ->orWhereIn('role_id', $userRoles);
            });
        }

        $count = $query->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'message' => 'All notifications marked as read',
            'count' => $count,
        ]);
    }
}
