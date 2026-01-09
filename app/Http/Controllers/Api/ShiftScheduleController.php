<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShiftSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShiftScheduleController extends Controller
{
    /**
     * Display a listing of shift schedules.
     */
    public function index(Request $request)
    {
        $query = ShiftSchedule::with('company');

        // Filter by company
        if ($request->has('company_id')) {
            $query->forCompany($request->company_id);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $isActive = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);
            if ($isActive) {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        $perPage = $request->get('per_page', 15);
        $shifts = $query->orderBy('shift_name')
            ->paginate($perPage);

        return response()->json($shifts);
    }

    /**
     * Store a newly created shift schedule.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shift_name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'grace_period_minutes' => 'nullable|integer|min:0',
            'working_hours' => 'required|numeric|min:0|max:24',
            'is_active' => 'nullable|boolean',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $shift = ShiftSchedule::create($request->all());

        return response()->json([
            'message' => 'Shift schedule created successfully',
            'data' => $shift
        ], 201);
    }

    /**
     * Display the specified shift schedule.
     */
    public function show($id)
    {
        $shift = ShiftSchedule::with(['company', 'employees.user'])
            ->findOrFail($id);

        return response()->json($shift);
    }

    /**
     * Update the specified shift schedule.
     */
    public function update(Request $request, $id)
    {
        $shift = ShiftSchedule::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'shift_name' => 'nullable|string|max:255',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'grace_period_minutes' => 'nullable|integer|min:0',
            'working_hours' => 'nullable|numeric|min:0|max:24',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $shift->update($request->all());

        return response()->json([
            'message' => 'Shift schedule updated successfully',
            'data' => $shift
        ]);
    }

    /**
     * Remove the specified shift schedule.
     */
    public function destroy($id)
    {
        $shift = ShiftSchedule::findOrFail($id);
        
        // Check if any employees are assigned to this shift
        if ($shift->employees()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete shift schedule with assigned employees'
            ], 400);
        }

        $shift->delete();

        return response()->json([
            'message' => 'Shift schedule deleted successfully'
        ]);
    }
}
