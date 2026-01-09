<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ComplianceCheck;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplianceCheckController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ComplianceCheck::with(['company', 'complianceRequirement', 'project', 'checkedBy']);

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by project
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by compliance requirement
        if ($request->has('compliance_requirement_id')) {
            $query->where('compliance_requirement_id', $request->compliance_requirement_id);
        }

        // Filter overdue
        if ($request->boolean('overdue')) {
            $query->overdue();
        }

        // Filter due soon
        if ($request->has('due_soon_days')) {
            $query->dueSoon($request->due_soon_days);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('check_number', 'like', "%{$search}%")
                  ->orWhere('findings', 'like', "%{$search}%");
            });
        }

        $checks = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $checks,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'compliance_requirement_id' => 'required|exists:compliance_requirements,id',
            'project_id' => 'nullable|exists:projects,id',
            'check_date' => 'required|date',
            'due_date' => 'required|date',
            'status' => 'nullable|in:pending,passed,failed,waived',
            'checked_by_id' => 'nullable|exists:users,id',
            'findings' => 'nullable|string',
            'corrective_action' => 'nullable|string',
            'evidence_path' => 'nullable|string|max:255',
            'next_check_date' => 'nullable|date|after:due_date',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['check_number'] = ComplianceCheck::generateCheckNumber();

        $check = ComplianceCheck::create($data);
        $check->load(['company', 'complianceRequirement', 'project', 'checkedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Compliance check created successfully',
            'data' => $check,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ComplianceCheck $complianceCheck): JsonResponse
    {
        $complianceCheck->load(['company', 'complianceRequirement', 'project', 'checkedBy']);

        return response()->json([
            'success' => true,
            'data' => $complianceCheck,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ComplianceCheck $complianceCheck): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'compliance_requirement_id' => 'sometimes|required|exists:compliance_requirements,id',
            'project_id' => 'nullable|exists:projects,id',
            'check_date' => 'sometimes|required|date',
            'due_date' => 'sometimes|required|date',
            'status' => 'nullable|in:pending,passed,failed,waived',
            'checked_by_id' => 'nullable|exists:users,id',
            'findings' => 'nullable|string',
            'corrective_action' => 'nullable|string',
            'evidence_path' => 'nullable|string|max:255',
            'next_check_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $complianceCheck->update($validator->validated());
        $complianceCheck->load(['company', 'complianceRequirement', 'project', 'checkedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Compliance check updated successfully',
            'data' => $complianceCheck,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ComplianceCheck $complianceCheck): JsonResponse
    {
        $complianceCheck->delete();

        return response()->json([
            'success' => true,
            'message' => 'Compliance check deleted successfully',
        ]);
    }

    /**
     * Mark a compliance check as passed.
     */
    public function pass(Request $request, $id): JsonResponse
    {
        $check = ComplianceCheck::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'findings' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $check->markAsPassed(
            $request->user()->id ?? null,
            $request->findings
        );

        $check->load(['company', 'complianceRequirement', 'project', 'checkedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Compliance check marked as passed',
            'data' => $check,
        ]);
    }

    /**
     * Mark a compliance check as failed.
     */
    public function fail(Request $request, $id): JsonResponse
    {
        $check = ComplianceCheck::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'findings' => 'required|string',
            'corrective_action' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $check->markAsFailed(
            $request->user()->id ?? null,
            $request->findings,
            $request->corrective_action
        );

        $check->load(['company', 'complianceRequirement', 'project', 'checkedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Compliance check marked as failed',
            'data' => $check,
        ]);
    }
}
