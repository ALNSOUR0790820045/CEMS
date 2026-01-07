<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $query = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->notExpired();

        // Filter by type
        if ($request->has('type')) {
            $query->byType($request->type);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->byPriority($request->priority);
        }

        // Filter by read status
        if ($request->has('read')) {
            $request->read ? $query->read() : $query->unread();
        }

        $notifications = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * Get unread notifications.
     */
    public function unread(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $notifications = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->unread()
            ->notExpired()
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * Get unread notifications count.
     */
    public function unreadCount(): JsonResponse
    {
        $user = Auth::user();
        
        $count = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->unread()
            ->notExpired()
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id): JsonResponse
    {
        $user = Auth::user();
        
        $notification = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->findOrFail($id);

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        
        Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $user = Auth::user();
        
        $notification = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->findOrFail($id);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully',
        ]);
    }

    /**
     * Clear all notifications.
     */
    public function clearAll(): JsonResponse
    {
        $user = Auth::user();
        
        Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'All notifications cleared',
        ]);
    }

    /**
     * Send a notification to specific users (admin only).
     */
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'nullable|in:info,success,warning,error,reminder',
            'category' => 'nullable|in:system,approval,deadline,alert,message',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'action_url' => 'nullable|url',
            'expires_at' => 'nullable|date',
        ]);

        $users = User::whereIn('id', $validated['user_ids'])->get();
        
        foreach ($users as $user) {
            Notification::create([
                'type' => $validated['type'] ?? 'info',
                'category' => $validated['category'] ?? 'system',
                'title' => $validated['title'],
                'body' => $validated['body'],
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'action_url' => $validated['action_url'] ?? null,
                'priority' => $validated['priority'] ?? 'normal',
                'expires_at' => $validated['expires_at'] ?? null,
                'company_id' => $user->company_id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification sent successfully',
        ], 201);
    }

    /**
     * Broadcast a notification to all users in company (admin only).
     */
    public function broadcast(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'nullable|in:info,success,warning,error,reminder',
            'category' => 'nullable|in:system,approval,deadline,alert,message',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'action_url' => 'nullable|url',
            'expires_at' => 'nullable|date',
        ]);

        $user = Auth::user();
        $users = User::where('company_id', $user->company_id)->get();
        
        foreach ($users as $recipient) {
            Notification::create([
                'type' => $validated['type'] ?? 'info',
                'category' => $validated['category'] ?? 'system',
                'title' => $validated['title'],
                'body' => $validated['body'],
                'notifiable_type' => User::class,
                'notifiable_id' => $recipient->id,
                'action_url' => $validated['action_url'] ?? null,
                'priority' => $validated['priority'] ?? 'normal',
                'expires_at' => $validated['expires_at'] ?? null,
                'company_id' => $recipient->company_id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification broadcast successfully',
        ], 201);
    }
}
