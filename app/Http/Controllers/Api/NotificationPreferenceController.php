<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationPreferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        
        $preferences = NotificationPreference::where('user_id', $user->id)->get();

        return response()->json([
            'success' => true,
            'data' => $preferences,
        ]);
    }

    /**
     * Update notification preferences.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'preferences' => 'required|array',
            'preferences.*.notification_type' => 'required|string',
            'preferences.*.channel_email' => 'boolean',
            'preferences.*.channel_sms' => 'boolean',
            'preferences.*.channel_push' => 'boolean',
            'preferences.*.channel_in_app' => 'boolean',
            'preferences.*.is_enabled' => 'boolean',
            'preferences.*.quiet_hours_start' => 'nullable|date_format:H:i',
            'preferences.*.quiet_hours_end' => 'nullable|date_format:H:i',
        ]);

        $user = Auth::user();

        foreach ($validated['preferences'] as $preference) {
            NotificationPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'notification_type' => $preference['notification_type'],
                ],
                $preference
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification preferences updated successfully',
        ]);
    }

    /**
     * Update a specific notification type preference.
     */
    public function updateByType(Request $request, $type): JsonResponse
    {
        $validated = $request->validate([
            'channel_email' => 'boolean',
            'channel_sms' => 'boolean',
            'channel_push' => 'boolean',
            'channel_in_app' => 'boolean',
            'is_enabled' => 'boolean',
            'quiet_hours_start' => 'nullable|date_format:H:i',
            'quiet_hours_end' => 'nullable|date_format:H:i',
        ]);

        $user = Auth::user();

        $preference = NotificationPreference::updateOrCreate(
            [
                'user_id' => $user->id,
                'notification_type' => $type,
            ],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Notification preference updated successfully',
            'data' => $preference,
        ]);
    }
}
