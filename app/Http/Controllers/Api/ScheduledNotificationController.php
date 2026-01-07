<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScheduledNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduledNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        $query = ScheduledNotification::where('company_id', $user->company_id)
            ->with('createdBy');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('scheduled_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('scheduled_at', '<=', $request->to_date);
        }

        $notifications = $query->latest('scheduled_at')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'scheduled_at' => 'required|date|after:now',
            'repeat_type' => 'required|in:once,daily,weekly,monthly',
            'recipients_type' => 'required|in:user,role,department,all',
            'recipients_ids' => 'nullable|array',
        ]);

        $user = Auth::user();
        $validated['created_by_id'] = $user->id;
        $validated['company_id'] = $user->company_id;

        $notification = ScheduledNotification::create($validated);
        $notification->load('createdBy');

        return response()->json([
            'success' => true,
            'message' => 'Scheduled notification created successfully',
            'data' => $notification,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ScheduledNotification $scheduledNotification): JsonResponse
    {
        $scheduledNotification->load('createdBy');

        return response()->json([
            'success' => true,
            'data' => $scheduledNotification,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ScheduledNotification $scheduledNotification): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'string|max:255',
            'body' => 'string',
            'scheduled_at' => 'date|after:now',
            'repeat_type' => 'in:once,daily,weekly,monthly',
            'recipients_type' => 'in:user,role,department,all',
            'recipients_ids' => 'nullable|array',
        ]);

        $scheduledNotification->update($validated);
        $scheduledNotification->load('createdBy');

        return response()->json([
            'success' => true,
            'message' => 'Scheduled notification updated successfully',
            'data' => $scheduledNotification,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScheduledNotification $scheduledNotification): JsonResponse
    {
        $scheduledNotification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Scheduled notification deleted successfully',
        ]);
    }

    /**
     * Cancel a scheduled notification.
     */
    public function cancel(ScheduledNotification $scheduledNotification): JsonResponse
    {
        $scheduledNotification->cancel();

        return response()->json([
            'success' => true,
            'message' => 'Scheduled notification cancelled successfully',
        ]);
    }
}
