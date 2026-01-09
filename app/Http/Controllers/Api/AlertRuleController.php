<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlertRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        $query = AlertRule::where('company_id', $user->company_id);

        // Filter by event type
        if ($request->has('event_type')) {
            $query->byEventType($request->event_type);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $rules = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $rules,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'required|string',
            'conditions' => 'nullable|array',
            'recipients_type' => 'required|in:user,role,department,all',
            'recipients_ids' => 'nullable|array',
            'channels' => 'nullable|array',
            'message_template' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $user = Auth::user();
        $validated['company_id'] = $user->company_id;

        $rule = AlertRule::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Alert rule created successfully',
            'data' => $rule,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AlertRule $alertRule): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $alertRule,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AlertRule $alertRule): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'string',
            'conditions' => 'nullable|array',
            'recipients_type' => 'in:user,role,department,all',
            'recipients_ids' => 'nullable|array',
            'channels' => 'nullable|array',
            'message_template' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $alertRule->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Alert rule updated successfully',
            'data' => $alertRule,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AlertRule $alertRule): JsonResponse
    {
        $alertRule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alert rule deleted successfully',
        ]);
    }

    /**
     * Toggle alert rule status.
     */
    public function toggle(AlertRule $alertRule): JsonResponse
    {
        $newStatus = $alertRule->toggle();

        return response()->json([
            'success' => true,
            'message' => 'Alert rule status toggled successfully',
            'is_active' => $newStatus,
        ]);
    }

    /**
     * Test an alert rule.
     */
    public function test(Request $request, AlertRule $alertRule): JsonResponse
    {
        $validated = $request->validate([
            'test_data' => 'nullable|array',
        ]);

        $testData = $validated['test_data'] ?? [];
        $matches = $alertRule->matchesConditions($testData);
        $recipients = $alertRule->getRecipients();

        return response()->json([
            'success' => true,
            'message' => 'Alert rule test completed',
            'matches' => $matches,
            'recipients_count' => $recipients->count(),
            'recipients' => $recipients->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
            ]),
        ]);
    }
}
