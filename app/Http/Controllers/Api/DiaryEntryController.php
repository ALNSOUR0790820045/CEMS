<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteDiary;
use App\Models\DiaryManpower;
use App\Models\DiaryEquipment;
use App\Models\DiaryActivity;
use App\Models\DiaryMaterial;
use App\Models\DiaryVisitor;
use App\Models\DiaryIncident;
use App\Models\DiaryInstruction;
use App\Models\DiaryPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DiaryEntryController extends Controller
{
    // Manpower methods
    public function addManpower(Request $request, $id)
    {
        $diary = SiteDiary::findOrFail($id);

        if (!$diary->canEdit()) {
            return response()->json(['error' => 'Cannot edit diary in current status'], 403);
        }

        $validated = $request->validate([
            'trade' => 'required|in:carpenter,mason,electrician,plumber,steel_fixer,painter,laborer,foreman,engineer,supervisor,driver,operator,welder,other',
            'own_count' => 'required|integer|min:0',
            'subcontractor_count' => 'required|integer|min:0',
            'subcontractor_id' => 'nullable|exists:subcontractors,id',
            'hours_worked' => 'nullable|numeric|min:0|max:24',
            'overtime_hours' => 'nullable|numeric|min:0|max:12',
            'notes' => 'nullable|string',
        ]);

        try {
            $manpower = $diary->manpower()->create($validated);

            return response()->json([
                'message' => 'Manpower added successfully',
                'data' => $manpower->load('subcontractor')
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add manpower', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateManpower(Request $request, $id, $entryId)
    {
        $diary = SiteDiary::findOrFail($id);

        if (!$diary->canEdit()) {
            return response()->json(['error' => 'Cannot edit diary in current status'], 403);
        }

        $manpower = DiaryManpower::where('site_diary_id', $id)->findOrFail($entryId);

        $validated = $request->validate([
            'trade' => 'sometimes|in:carpenter,mason,electrician,plumber,steel_fixer,painter,laborer,foreman,engineer,supervisor,driver,operator,welder,other',
            'own_count' => 'sometimes|integer|min:0',
            'subcontractor_count' => 'sometimes|integer|min:0',
            'subcontractor_id' => 'nullable|exists:subcontractors,id',
            'hours_worked' => 'nullable|numeric|min:0|max:24',
            'overtime_hours' => 'nullable|numeric|min:0|max:12',
            'notes' => 'nullable|string',
        ]);

        try {
            $manpower->update($validated);

            return response()->json([
                'message' => 'Manpower updated successfully',
                'data' => $manpower->load('subcontractor')
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update manpower', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteManpower($id, $entryId)
    {
        $diary = SiteDiary::findOrFail($id);

        if (!$diary->canEdit()) {
            return response()->json(['error' => 'Cannot edit diary in current status'], 403);
        }

        $manpower = DiaryManpower::where('site_diary_id', $id)->findOrFail($entryId);
        $manpower->delete();

        return response()->json(['message' => 'Manpower deleted successfully']);
    }

    // Equipment methods
    public function addEquipment(Request $request, $id)
    {
        $diary = SiteDiary::findOrFail($id);

        if (!$diary->canEdit()) {
            return response()->json(['error' => 'Cannot edit diary in current status'], 403);
        }

        $validated = $request->validate([
            'equipment_type' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'hours_worked' => 'nullable|numeric|min:0|max:24',
            'hours_idle' => 'nullable|numeric|min:0|max:24',
            'idle_reason' => 'nullable|string',
            'fuel_consumed' => 'nullable|numeric|min:0',
            'operator_name' => 'nullable|string',
            'status' => 'required|in:working,idle,breakdown,maintenance',
            'notes' => 'nullable|string',
        ]);

        try {
            $equipment = $diary->equipment()->create($validated);

            return response()->json([
                'message' => 'Equipment added successfully',
                'data' => $equipment
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add equipment', 'message' => $e->getMessage()], 500);
        }
    }

    // Activities methods
    public function addActivity(Request $request, $id)
    {
        $diary = SiteDiary::findOrFail($id);

        if (!$diary->canEdit()) {
            return response()->json(['error' => 'Cannot edit diary in current status'], 403);
        }

        $validated = $request->validate([
            'location' => 'nullable|string',
            'description' => 'required|string',
            'description_en' => 'nullable|string',
            'quantity_today' => 'nullable|numeric|min:0',
            'unit_id' => 'nullable|exists:units,id',
            'cumulative_quantity' => 'nullable|numeric|min:0',
            'percentage_complete' => 'nullable|numeric|min:0|max:100',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'status' => 'required|in:in_progress,completed,delayed,on_hold',
            'remarks' => 'nullable|string',
        ]);

        try {
            $activity = $diary->activities()->create($validated);

            return response()->json([
                'message' => 'Activity added successfully',
                'data' => $activity->load('unit')
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add activity', 'message' => $e->getMessage()], 500);
        }
    }

    // Materials methods
    public function addMaterial(Request $request, $id)
    {
        $diary = SiteDiary::findOrFail($id);

        if (!$diary->canEdit()) {
            return response()->json(['error' => 'Cannot edit diary in current status'], 403);
        }

        $validated = $request->validate([
            'material_id' => 'nullable|exists:materials,id',
            'quantity_received' => 'nullable|numeric|min:0',
            'quantity_used' => 'nullable|numeric|min:0',
            'unit_id' => 'nullable|exists:units,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'delivery_note_number' => 'nullable|string',
            'location_used' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            $material = $diary->materials()->create($validated);

            return response()->json([
                'message' => 'Material added successfully',
                'data' => $material->load(['material', 'unit', 'supplier'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add material', 'message' => $e->getMessage()], 500);
        }
    }

    // Visitors methods
    public function addVisitor(Request $request, $id)
    {
        $diary = SiteDiary::findOrFail($id);

        if (!$diary->canEdit()) {
            return response()->json(['error' => 'Cannot edit diary in current status'], 403);
        }

        $validated = $request->validate([
            'visitor_name' => 'required|string',
            'organization' => 'nullable|string',
            'designation' => 'nullable|string',
            'purpose' => 'required|in:inspection,meeting,audit,delivery,other',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i|after:time_in',
            'escorted_by' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        try {
            $visitor = $diary->visitors()->create($validated);

            return response()->json([
                'message' => 'Visitor added successfully',
                'data' => $visitor
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add visitor', 'message' => $e->getMessage()], 500);
        }
    }

    // Incidents methods
    public function addIncident(Request $request, $id)
    {
        $diary = SiteDiary::findOrFail($id);

        if (!$diary->canEdit()) {
            return response()->json(['error' => 'Cannot edit diary in current status'], 403);
        }

        $validated = $request->validate([
            'incident_type' => 'required|in:accident,near_miss,property_damage,environmental,security',
            'severity' => 'required|in:minor,moderate,major,critical',
            'time_occurred' => 'nullable|date_format:H:i',
            'location' => 'nullable|string',
            'description' => 'required|string',
            'persons_involved' => 'nullable|string',
            'injuries' => 'nullable|string',
            'property_damage' => 'nullable|string',
            'immediate_action' => 'nullable|string',
            'reported_to' => 'nullable|string',
            'hse_notified' => 'boolean',
            'investigation_required' => 'boolean',
            'photos' => 'nullable|array',
        ]);

        try {
            $incident = $diary->incidents()->create($validated);

            return response()->json([
                'message' => 'Incident added successfully',
                'data' => $incident
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add incident', 'message' => $e->getMessage()], 500);
        }
    }

    // Instructions methods
    public function addInstruction(Request $request, $id)
    {
        $diary = SiteDiary::findOrFail($id);

        if (!$diary->canEdit()) {
            return response()->json(['error' => 'Cannot edit diary in current status'], 403);
        }

        $validated = $request->validate([
            'instruction_type' => 'required|in:client,consultant,internal,safety',
            'issued_by' => 'required|string',
            'received_by_id' => 'nullable|exists:users,id',
            'description' => 'required|string',
            'action_required' => 'nullable|string',
            'deadline' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed',
            'reference_number' => 'nullable|string',
        ]);

        try {
            $instruction = $diary->instructions()->create($validated);

            return response()->json([
                'message' => 'Instruction added successfully',
                'data' => $instruction->load('receivedBy')
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add instruction', 'message' => $e->getMessage()], 500);
        }
    }

    // Photos methods
    public function uploadPhoto(Request $request, $id)
    {
        $diary = SiteDiary::findOrFail($id);

        if (!$diary->canEdit()) {
            return response()->json(['error' => 'Cannot edit diary in current status'], 403);
        }

        $validated = $request->validate([
            'photo' => 'required|image|max:10240', // 10MB max
            'caption' => 'nullable|string',
            'location' => 'nullable|string',
            'category' => 'required|in:progress,quality,safety,general',
            'gps_latitude' => 'nullable|numeric|between:-90,90',
            'gps_longitude' => 'nullable|numeric|between:-180,180',
        ]);

        try {
            $path = $request->file('photo')->store('diary-photos', 'public');

            $photoData = [
                'photo_path' => $path,
                'caption' => $validated['caption'] ?? null,
                'location' => $validated['location'] ?? null,
                'category' => $validated['category'],
                'taken_by_id' => auth()->id(),
                'taken_at' => now(),
                'gps_latitude' => $validated['gps_latitude'] ?? null,
                'gps_longitude' => $validated['gps_longitude'] ?? null,
            ];

            $photo = $diary->photos()->create($photoData);

            return response()->json([
                'message' => 'Photo uploaded successfully',
                'data' => $photo->load('takenBy')
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to upload photo', 'message' => $e->getMessage()], 500);
        }
    }
}
