<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhotoLocation;
use Illuminate\Http\Request;

class PhotoLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PhotoLocation::with(['project']);

        // Filter by project
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $locations = $query->orderBy('created_at', 'desc')->get();

        return response()->json($locations);
    }

    /**
     * Get locations by project
     */
    public function byProject($projectId)
    {
        $locations = PhotoLocation::where('project_id', $projectId)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($locations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'gps_latitude' => 'required|numeric',
            'gps_longitude' => 'required|numeric',
            'radius_meters' => 'nullable|integer',
        ]);

        $validated['radius_meters'] = $validated['radius_meters'] ?? 100;

        $location = PhotoLocation::create($validated);

        return response()->json($location->load('project'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $location = PhotoLocation::with(['project'])->findOrFail($id);

        return response()->json($location);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $location = PhotoLocation::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'gps_latitude' => 'sometimes|numeric',
            'gps_longitude' => 'sometimes|numeric',
            'radius_meters' => 'nullable|integer',
        ]);

        $location->update($validated);

        return response()->json($location);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $location = PhotoLocation::findOrFail($id);
        $location->delete();

        return response()->json(['message' => 'Location deleted successfully'], 200);
    }
}
