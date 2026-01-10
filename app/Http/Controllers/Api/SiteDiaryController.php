<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteDiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SiteDiaryController extends Controller
{
    public function index(Request $request)
    {
        $query = SiteDiary::with([
            'project', 
            'preparedBy', 
            'reviewedBy', 
            'approvedBy'
        ]);

        // Filter by project
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('diary_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->where('diary_date', '<=', $request->to_date);
        }

        $diaries = $query->orderBy('diary_date', 'desc')->paginate(15);

        return response()->json($diaries);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'diary_date' => [
                'required',
                'date',
                Rule::unique('site_diaries')->where(function ($query) use ($request) {
                    return $query->where('project_id', $request->project_id);
                }),
            ],
            'weather_morning' => 'nullable|in:sunny,cloudy,rainy,windy,stormy',
            'weather_afternoon' => 'nullable|in:sunny,cloudy,rainy,windy,stormy',
            'temperature_min' => 'nullable|numeric',
            'temperature_max' => 'nullable|numeric',
            'humidity' => 'nullable|numeric|min:0|max:100',
            'wind_speed' => 'nullable|numeric|min:0',
            'site_condition' => 'nullable|in:dry,wet,muddy,flooded',
            'work_status' => 'required|in:normal,delayed,suspended,holiday',
            'delay_reason' => 'required_if:work_status,delayed|nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $validated['company_id'] = auth()->user()->company_id;
            $validated['prepared_by_id'] = auth()->id();
            $validated['status'] = 'draft';

            $diary = SiteDiary::create($validated);

            DB::commit();

            return response()->json([
                'message' => 'Site diary created successfully',
                'data' => $diary->load(['project', 'preparedBy'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to create site diary',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $diary = SiteDiary::with([
            'project',
            'preparedBy',
            'reviewedBy',
            'approvedBy',
            'manpower.subcontractor',
            'equipment',
            'activities.unit',
            'materials.material',
            'materials.unit',
            'materials.supplier',
            'visitors',
            'incidents',
            'instructions.receivedBy',
            'photos.takenBy'
        ])->findOrFail($id);

        return response()->json($diary);
    }

    public function update(Request $request, $id)
    {
        $diary = SiteDiary::findOrFail($id);

        if (!$diary->canEdit()) {
            return response()->json([
                'error' => 'Cannot edit diary in current status'
            ], 403);
        }

        $validated = $request->validate([
            'diary_date' => [
                'sometimes',
                'date',
                Rule::unique('site_diaries')->where(function ($query) use ($request, $id) {
                    return $query->where('project_id', $request->project_id)->where('id', '!=', $id);
                }),
            ],
            'weather_morning' => 'nullable|in:sunny,cloudy,rainy,windy,stormy',
            'weather_afternoon' => 'nullable|in:sunny,cloudy,rainy,windy,stormy',
            'temperature_min' => 'nullable|numeric',
            'temperature_max' => 'nullable|numeric',
            'humidity' => 'nullable|numeric|min:0|max:100',
            'wind_speed' => 'nullable|numeric|min:0',
            'site_condition' => 'nullable|in:dry,wet,muddy,flooded',
            'work_status' => 'sometimes|in:normal,delayed,suspended,holiday',
            'delay_reason' => 'required_if:work_status,delayed|nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            $diary->update($validated);

            return response()->json([
                'message' => 'Site diary updated successfully',
                'data' => $diary->load(['project', 'preparedBy'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update site diary',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $diary = SiteDiary::findOrFail($id);

        if ($diary->status !== 'draft') {
            return response()->json([
                'error' => 'Can only delete draft diaries'
            ], 403);
        }

        try {
            $diary->delete();

            return response()->json([
                'message' => 'Site diary deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete site diary',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function byDate($projectId, $date)
    {
        $diary = SiteDiary::with([
            'project',
            'preparedBy',
            'manpower',
            'equipment',
            'activities',
            'materials',
            'visitors',
            'incidents',
            'instructions',
            'photos'
        ])
        ->where('project_id', $projectId)
        ->whereDate('diary_date', $date)
        ->first();

        if (!$diary) {
            return response()->json([
                'message' => 'No diary found for this date'
            ], 404);
        }

        return response()->json($diary);
    }

    public function latest($projectId)
    {
        $diary = SiteDiary::with([
            'project',
            'preparedBy',
            'manpower',
            'equipment',
            'activities',
            'materials'
        ])
        ->where('project_id', $projectId)
        ->orderBy('diary_date', 'desc')
        ->first();

        if (!$diary) {
            return response()->json([
                'message' => 'No diaries found for this project'
            ], 404);
        }

        return response()->json($diary);
    }

    public function submit($id)
    {
        $diary = SiteDiary::findOrFail($id);

        if ($diary->status !== 'draft') {
            return response()->json([
                'error' => 'Diary is not in draft status'
            ], 400);
        }

        try {
            $diary->submit();

            return response()->json([
                'message' => 'Site diary submitted successfully',
                'data' => $diary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to submit site diary',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function review($id)
    {
        $diary = SiteDiary::findOrFail($id);

        if ($diary->status !== 'submitted') {
            return response()->json([
                'error' => 'Diary must be submitted before review'
            ], 400);
        }

        try {
            $diary->review(auth()->id());

            return response()->json([
                'message' => 'Site diary reviewed successfully',
                'data' => $diary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to review site diary',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function approve($id)
    {
        $diary = SiteDiary::findOrFail($id);

        if ($diary->status !== 'reviewed') {
            return response()->json([
                'error' => 'Diary must be reviewed before approval'
            ], 400);
        }

        try {
            $diary->approve(auth()->id());

            return response()->json([
                'message' => 'Site diary approved successfully',
                'data' => $diary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to approve site diary',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function reject($id)
    {
        $diary = SiteDiary::findOrFail($id);

        if (!in_array($diary->status, ['submitted', 'reviewed'])) {
            return response()->json([
                'error' => 'Cannot reject diary in current status'
            ], 400);
        }

        try {
            $diary->reject();

            return response()->json([
                'message' => 'Site diary rejected successfully',
                'data' => $diary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to reject site diary',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function duplicateFromPrevious($id)
    {
        $sourceDiary = SiteDiary::with(['manpower', 'equipment'])
            ->findOrFail($id);

        try {
            DB::beginTransaction();

            // Create new diary for next day
            $newDate = \Carbon\Carbon::parse($sourceDiary->diary_date)->addDay();
            
            $newDiary = SiteDiary::create([
                'project_id' => $sourceDiary->project_id,
                'diary_date' => $newDate,
                'weather_morning' => $sourceDiary->weather_afternoon,
                'site_condition' => $sourceDiary->site_condition,
                'work_status' => 'normal',
                'company_id' => $sourceDiary->company_id,
                'prepared_by_id' => auth()->id(),
                'status' => 'draft',
            ]);

            // Duplicate manpower
            foreach ($sourceDiary->manpower as $manpower) {
                $newDiary->manpower()->create([
                    'trade' => $manpower->trade,
                    'own_count' => $manpower->own_count,
                    'subcontractor_count' => $manpower->subcontractor_count,
                    'subcontractor_id' => $manpower->subcontractor_id,
                    'hours_worked' => $manpower->hours_worked,
                ]);
            }

            // Duplicate equipment
            foreach ($sourceDiary->equipment as $equipment) {
                $newDiary->equipment()->create([
                    'equipment_type' => $equipment->equipment_type,
                    'quantity' => $equipment->quantity,
                    'operator_name' => $equipment->operator_name,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Site diary duplicated successfully',
                'data' => $newDiary->load(['project', 'preparedBy', 'manpower', 'equipment'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to duplicate site diary',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
