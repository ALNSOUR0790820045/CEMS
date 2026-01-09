<?php

namespace App\Http\Controllers;

use App\Models\ScheduleCalendar;
use App\Models\CalendarException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $query = ScheduleCalendar::with('exceptions');

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        } else {
            $query->where('company_id', Auth::user()->company_id);
        }

        $calendars = $query->get();

        return response()->json([
            'success' => true,
            'data' => $calendars
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_default' => 'nullable|boolean',
            'working_days' => 'nullable|array',
            'working_hours' => 'nullable|array',
            'hours_per_day' => 'nullable|numeric|min:1|max:24',
        ]);

        $validated['company_id'] = Auth::user()->company_id;

        $calendar = ScheduleCalendar::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Calendar created successfully',
            'data' => $calendar
        ], 201);
    }

    public function show($id)
    {
        $calendar = ScheduleCalendar::with('exceptions')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $calendar
        ]);
    }

    public function update(Request $request, $id)
    {
        $calendar = ScheduleCalendar::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_default' => 'nullable|boolean',
            'working_days' => 'nullable|array',
            'working_hours' => 'nullable|array',
            'hours_per_day' => 'nullable|numeric|min:1|max:24',
        ]);

        $calendar->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Calendar updated successfully',
            'data' => $calendar
        ]);
    }

    public function destroy($id)
    {
        $calendar = ScheduleCalendar::findOrFail($id);
        $calendar->delete();

        return response()->json([
            'success' => true,
            'message' => 'Calendar deleted successfully'
        ]);
    }

    public function addException(Request $request, $id)
    {
        $calendar = ScheduleCalendar::findOrFail($id);

        $validated = $request->validate([
            'exception_date' => 'required|date',
            'exception_type' => 'required|in:holiday,non_working,extra_working',
            'name' => 'required|string|max:255',
            'working_hours' => 'nullable|array',
            'is_recurring' => 'nullable|boolean',
            'recurrence_pattern' => 'nullable|in:yearly,none',
        ]);

        $validated['schedule_calendar_id'] = $calendar->id;

        $exception = CalendarException::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Calendar exception added successfully',
            'data' => $exception
        ], 201);
    }
}
