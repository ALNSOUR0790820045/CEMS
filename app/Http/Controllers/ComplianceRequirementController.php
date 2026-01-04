<?php

namespace App\Http\Controllers;

use App\Models\ComplianceRequirement;
use Illuminate\Http\Request;

class ComplianceRequirementController extends Controller
{
    /**
     * Display a listing of compliance requirements.
     */
    public function index(Request $request)
    {
        $query = ComplianceRequirement::with(['company'])
            ->when($request->company_id, function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            })
            ->when($request->requirement_type, function ($q) use ($request) {
                $q->where('requirement_type', $request->requirement_type);
            })
            ->when($request->applicable_to, function ($q) use ($request) {
                $q->where('applicable_to', $request->applicable_to);
            })
            ->when($request->is_mandatory !== null, function ($q) use ($request) {
                $q->where('is_mandatory', $request->boolean('is_mandatory'));
            })
            ->latest();

        $requirements = $query->paginate(15);

        return response()->json($requirements);
    }

    /**
     * Store a newly created compliance requirement.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'requirement_code' => 'required|string|max:255|unique:compliance_requirements,requirement_code',
            'requirement_name' => 'required|string|max:255',
            'regulatory_body' => 'required|string|max:255',
            'requirement_type' => 'required|in:license,permit,certification,audit,reporting',
            'applicable_to' => 'required|in:company,project,department,employee',
            'description' => 'required|string',
            'frequency' => 'required|in:one_time,annual,quarterly,monthly',
            'is_mandatory' => 'boolean',
            'penalty_description' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
        ]);

        $requirement = ComplianceRequirement::create($validated);

        return response()->json([
            'message' => 'Compliance requirement created successfully',
            'data' => $requirement->load('company')
        ], 201);
    }

    /**
     * Display the specified compliance requirement.
     */
    public function show(ComplianceRequirement $complianceRequirement)
    {
        return response()->json($complianceRequirement->load(['company', 'trackings']));
    }

    /**
     * Update the specified compliance requirement.
     */
    public function update(Request $request, ComplianceRequirement $complianceRequirement)
    {
        $validated = $request->validate([
            'requirement_code' => 'sometimes|required|string|max:255|unique:compliance_requirements,requirement_code,' . $complianceRequirement->id,
            'requirement_name' => 'sometimes|required|string|max:255',
            'regulatory_body' => 'sometimes|required|string|max:255',
            'requirement_type' => 'sometimes|required|in:license,permit,certification,audit,reporting',
            'applicable_to' => 'sometimes|required|in:company,project,department,employee',
            'description' => 'sometimes|required|string',
            'frequency' => 'sometimes|required|in:one_time,annual,quarterly,monthly',
            'is_mandatory' => 'boolean',
            'penalty_description' => 'nullable|string',
        ]);

        $complianceRequirement->update($validated);

        return response()->json([
            'message' => 'Compliance requirement updated successfully',
            'data' => $complianceRequirement->fresh()->load('company')
        ]);
    }

    /**
     * Remove the specified compliance requirement.
     */
    public function destroy(ComplianceRequirement $complianceRequirement)
    {
        $complianceRequirement->delete();

        return response()->json([
            'message' => 'Compliance requirement deleted successfully'
        ]);
    }
}
