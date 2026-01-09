<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ComplianceRequirement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplianceRequirementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ComplianceRequirement::with(['company', 'checks']);

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Filter by frequency
        if ($request->has('frequency')) {
            $query->byFrequency($request->frequency);
        }

        // Filter by mandatory
        if ($request->has('is_mandatory')) {
            $query->where('is_mandatory', $request->boolean('is_mandatory'));
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $requirements = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $requirements,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:compliance_requirements,code',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:safety,environmental,legal,quality,financial',
            'regulation_reference' => 'nullable|string|max:255',
            'is_mandatory' => 'nullable|boolean',
            'frequency' => 'required|in:one_time,monthly,quarterly,annually',
            'responsible_role' => 'nullable|string|max:255',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $requirement = ComplianceRequirement::create($validator->validated());
        $requirement->load(['company']);

        return response()->json([
            'success' => true,
            'message' => 'Compliance requirement created successfully',
            'data' => $requirement,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ComplianceRequirement $complianceRequirement): JsonResponse
    {
        $complianceRequirement->load(['company', 'checks.checkedBy', 'checks.project']);

        return response()->json([
            'success' => true,
            'data' => $complianceRequirement,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ComplianceRequirement $complianceRequirement): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'sometimes|required|string|max:255|unique:compliance_requirements,code,' . $complianceRequirement->id,
            'name' => 'sometimes|required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category' => 'sometimes|required|in:safety,environmental,legal,quality,financial',
            'regulation_reference' => 'nullable|string|max:255',
            'is_mandatory' => 'nullable|boolean',
            'frequency' => 'sometimes|required|in:one_time,monthly,quarterly,annually',
            'responsible_role' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $complianceRequirement->update($validator->validated());
        $complianceRequirement->load(['company']);

        return response()->json([
            'success' => true,
            'message' => 'Compliance requirement updated successfully',
            'data' => $complianceRequirement,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ComplianceRequirement $complianceRequirement): JsonResponse
    {
        $complianceRequirement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Compliance requirement deleted successfully',
        ]);
    }
}
