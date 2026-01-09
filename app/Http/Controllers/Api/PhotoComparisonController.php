<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhotoComparison;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhotoComparisonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PhotoComparison::with(['project', 'beforePhoto', 'afterPhoto', 'createdBy']);

        // Filter by project
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $comparisons = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($comparisons);
    }

    /**
     * Get comparisons by project
     */
    public function byProject($projectId)
    {
        $comparisons = PhotoComparison::with(['beforePhoto', 'afterPhoto'])
            ->where('project_id', $projectId)
            ->orderBy('comparison_date', 'desc')
            ->get();

        return response()->json($comparisons);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'before_photo_id' => 'required|exists:photos,id',
            'after_photo_id' => 'required|exists:photos,id',
            'comparison_date' => 'nullable|date',
            'location' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $validated['created_by_id'] = Auth::id();
        $validated['comparison_date'] = $validated['comparison_date'] ?? now()->toDateString();

        $comparison = PhotoComparison::create($validated);

        return response()->json($comparison->load(['beforePhoto', 'afterPhoto', 'createdBy']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $comparison = PhotoComparison::with(['project', 'beforePhoto', 'afterPhoto', 'createdBy'])
            ->findOrFail($id);

        return response()->json($comparison);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $comparison = PhotoComparison::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'comparison_date' => 'sometimes|date',
            'location' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $comparison->update($validated);

        return response()->json($comparison);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $comparison = PhotoComparison::findOrFail($id);
        $comparison->delete();

        return response()->json(['message' => 'Comparison deleted successfully'], 200);
    }
}
