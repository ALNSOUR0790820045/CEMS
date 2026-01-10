<?php

namespace App\Http\Controllers;

use App\Models\TimeBarProtectionSetting;
use App\Services\TimeBarProtectionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TimeBarProtectionSettingController extends Controller
{
    protected TimeBarProtectionService $protectionService;

    public function __construct(TimeBarProtectionService $protectionService)
    {
        $this->protectionService = $protectionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = TimeBarProtectionSetting::query()->with('company');

        // Filter by company if provided
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by entity type if provided
        if ($request->has('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        // Filter by active status if provided
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $settings = $query->orderBy('company_id', 'desc')
                         ->orderBy('entity_type')
                         ->paginate(15);

        if ($request->wantsJson()) {
            return response()->json($settings);
        }

        return view('time-bar-protection.index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('time-bar-protection.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'entity_type' => 'required|string|max:255',
            'protection_days' => 'required|integer|min:1|max:3650',
            'protection_type' => 'required|in:view_only,full_lock,approval_required',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
            'excluded_roles' => 'nullable|array',
            'excluded_roles.*' => 'string',
            'metadata' => 'nullable|array',
        ]);

        $setting = TimeBarProtectionSetting::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Time bar protection setting created successfully.',
                'data' => $setting,
            ], 201);
        }

        return redirect()->route('time-bar-protection.index')
                        ->with('success', 'Time bar protection setting created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TimeBarProtectionSetting $timeBarProtectionSetting): View|JsonResponse
    {
        $timeBarProtectionSetting->load('company');

        if (request()->wantsJson()) {
            return response()->json($timeBarProtectionSetting);
        }

        return view('time-bar-protection.show', compact('timeBarProtectionSetting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimeBarProtectionSetting $timeBarProtectionSetting): View
    {
        return view('time-bar-protection.edit', compact('timeBarProtectionSetting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TimeBarProtectionSetting $timeBarProtectionSetting): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'entity_type' => 'required|string|max:255',
            'protection_days' => 'required|integer|min:1|max:3650',
            'protection_type' => 'required|in:view_only,full_lock,approval_required',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
            'excluded_roles' => 'nullable|array',
            'excluded_roles.*' => 'string',
            'metadata' => 'nullable|array',
        ]);

        $timeBarProtectionSetting->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Time bar protection setting updated successfully.',
                'data' => $timeBarProtectionSetting,
            ]);
        }

        return redirect()->route('time-bar-protection.index')
                        ->with('success', 'Time bar protection setting updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeBarProtectionSetting $timeBarProtectionSetting): RedirectResponse|JsonResponse
    {
        $timeBarProtectionSetting->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Time bar protection setting deleted successfully.',
            ]);
        }

        return redirect()->route('time-bar-protection.index')
                        ->with('success', 'Time bar protection setting deleted successfully.');
    }

    /**
     * Toggle the active status of a setting.
     */
    public function toggleActive(TimeBarProtectionSetting $timeBarProtectionSetting): JsonResponse
    {
        $timeBarProtectionSetting->update([
            'is_active' => !$timeBarProtectionSetting->is_active,
        ]);

        return response()->json([
            'message' => 'Status updated successfully.',
            'is_active' => $timeBarProtectionSetting->is_active,
        ]);
    }

    /**
     * Check protection status for a specific entity.
     */
    public function checkProtection(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'entity_type' => 'required|string',
            'company_id' => 'nullable|integer',
            'created_at' => 'required|date',
        ]);

        $isProtected = $this->protectionService->isProtected(
            $validated['entity_type'],
            \Carbon\Carbon::parse($validated['created_at']),
            $validated['company_id'] ?? null
        );

        $setting = $this->protectionService->getSetting(
            $validated['entity_type'],
            $validated['company_id'] ?? null
        );

        return response()->json([
            'is_protected' => $isProtected,
            'protection_type' => $setting?->protection_type,
            'protection_days' => $setting?->protection_days,
        ]);
    }
}
