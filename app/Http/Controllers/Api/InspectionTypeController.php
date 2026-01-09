<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InspectionType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InspectionTypeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = InspectionType::where('company_id', $user->company_id)
            ->with('defaultChecklist');

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $types = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $types,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:inspection_types,code',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'category' => 'required|in:structural,mep,architectural,civil,safety,environmental',
            'description' => 'nullable|string',
            'default_checklist_id' => 'nullable|exists:inspection_templates,id',
            'requires_witness' => 'boolean',
            'requires_approval' => 'boolean',
            'frequency' => 'required|in:once,daily,weekly,milestone',
            'is_active' => 'boolean',
        ]);

        $user = Auth::user();
        $validated['company_id'] = $user->company_id;

        $type = InspectionType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Inspection type created successfully',
            'data' => $type->load('defaultChecklist'),
        ], 201);
    }

    public function show(InspectionType $inspectionType): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $inspectionType->load('defaultChecklist'),
        ]);
    }

    public function update(Request $request, InspectionType $inspectionType): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'string|max:255|unique:inspection_types,code,' . $inspectionType->id,
            'name' => 'string|max:255',
            'name_en' => 'nullable|string|max:255',
            'category' => 'in:structural,mep,architectural,civil,safety,environmental',
            'description' => 'nullable|string',
            'default_checklist_id' => 'nullable|exists:inspection_templates,id',
            'requires_witness' => 'boolean',
            'requires_approval' => 'boolean',
            'frequency' => 'in:once,daily,weekly,milestone',
            'is_active' => 'boolean',
        ]);

        $inspectionType->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Inspection type updated successfully',
            'data' => $inspectionType->load('defaultChecklist'),
        ]);
    }

    public function destroy(InspectionType $inspectionType): JsonResponse
    {
        $inspectionType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inspection type deleted successfully',
        ]);
    }
}
