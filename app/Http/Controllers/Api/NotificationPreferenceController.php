<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NotificationPreferenceController extends Controller
{
    public function index(Request $request)
    {
        $preferences = NotificationPreference::where('user_id', $request->user()->id)
            ->get();

        return response()->json($preferences);
    }

    public function update(Request $request)
    {
        $request->validate([
            'preferences' => 'required|array',
            'preferences.*.notification_type' => 'required|string',
            'preferences.*.in_app_enabled' => 'boolean',
            'preferences.*.email_enabled' => 'boolean',
            'preferences.*.sms_enabled' => 'boolean',
        ]);

        $updatedPreferences = [];

        foreach ($request->preferences as $preferenceData) {
            $preference = NotificationPreference::updateOrCreate(
                [
                    'user_id' => $request->user()->id,
                    'notification_type' => $preferenceData['notification_type'],
                ],
                [
                    'in_app_enabled' => $preferenceData['in_app_enabled'] ?? true,
                    'email_enabled' => $preferenceData['email_enabled'] ?? true,
                    'sms_enabled' => $preferenceData['sms_enabled'] ?? false,
                ]
            );

            $updatedPreferences[] = $preference;
        }

        return response()->json([
            'message' => 'Preferences updated successfully',
            'preferences' => $updatedPreferences,
        ]);
    }
}
