<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReportSchedule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReportScheduleController extends Controller
{
    public function index(): JsonResponse
    {
        $schedules = ReportSchedule::with(['company', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $schedules,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'report_type' => 'required|string',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'schedule_time' => 'nullable|date_format:H:i',
            'schedule_day' => 'nullable|integer|min:1|max:31',
            'email_recipients' => 'nullable|array',
            'email_recipients.*' => 'email',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['created_by_id'] = auth()->id() ?? 1;
        $validated['company_id'] = 1; // Replace with actual company from auth

        $schedule = ReportSchedule::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Report schedule created successfully',
            'data' => $schedule,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $schedule = ReportSchedule::with(['company', 'createdBy'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $schedule,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $schedule = ReportSchedule::findOrFail($id);

        $validated = $request->validate([
            'report_type' => 'sometimes|string',
            'frequency' => 'sometimes|in:daily,weekly,monthly,quarterly,yearly',
            'schedule_time' => 'nullable|date_format:H:i',
            'schedule_day' => 'nullable|integer|min:1|max:31',
            'email_recipients' => 'nullable|array',
            'email_recipients.*' => 'email',
            'is_active' => 'nullable|boolean',
        ]);

        $schedule->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Report schedule updated successfully',
            'data' => $schedule,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $schedule = ReportSchedule::findOrFail($id);
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Report schedule deleted successfully',
        ]);
    }
}

