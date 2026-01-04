<?php

namespace App\Http\Controllers;

use App\Models\ComplianceTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ComplianceTrackingController extends Controller
{
    /**
     * Display a listing of compliance trackings.
     */
    public function index(Request $request)
    {
        $query = ComplianceTracking::with(['company', 'complianceRequirement', 'responsiblePerson'])
            ->when($request->company_id, function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            })
            ->when($request->compliance_requirement_id, function ($q) use ($request) {
                $q->where('compliance_requirement_id', $request->compliance_requirement_id);
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->responsible_person_id, function ($q) use ($request) {
                $q->where('responsible_person_id', $request->responsible_person_id);
            })
            ->latest();

        $trackings = $query->paginate(15);

        return response()->json($trackings);
    }

    /**
     * Store a newly created compliance tracking.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'compliance_requirement_id' => 'required|exists:compliance_requirements,id',
            'entity_type' => 'required|string|max:255',
            'entity_id' => 'required|integer',
            'due_date' => 'required|date',
            'completion_date' => 'nullable|date',
            'status' => 'nullable|in:pending,in_progress,completed,overdue,waived',
            'responsible_person_id' => 'nullable|exists:users,id',
            'evidence_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'remarks' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
        ]);

        // Handle file upload
        if ($request->hasFile('evidence_file')) {
            $path = $request->file('evidence_file')->store('compliance_evidence', 'public');
            $validated['evidence_file_path'] = $path;
        }

        $tracking = ComplianceTracking::create($validated);

        return response()->json([
            'message' => 'Compliance tracking created successfully',
            'data' => $tracking->load(['company', 'complianceRequirement', 'responsiblePerson']),
        ], 201);
    }

    /**
     * Display the specified compliance tracking.
     */
    public function show(ComplianceTracking $complianceTracking)
    {
        return response()->json($complianceTracking->load(['company', 'complianceRequirement', 'responsiblePerson']));
    }

    /**
     * Update the specified compliance tracking.
     */
    public function update(Request $request, ComplianceTracking $complianceTracking)
    {
        $validated = $request->validate([
            'compliance_requirement_id' => 'sometimes|required|exists:compliance_requirements,id',
            'entity_type' => 'sometimes|required|string|max:255',
            'entity_id' => 'sometimes|required|integer',
            'due_date' => 'sometimes|required|date',
            'completion_date' => 'nullable|date',
            'status' => 'nullable|in:pending,in_progress,completed,overdue,waived',
            'responsible_person_id' => 'nullable|exists:users,id',
            'evidence_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'remarks' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('evidence_file')) {
            // Delete old file if exists
            if ($complianceTracking->evidence_file_path) {
                Storage::disk('public')->delete($complianceTracking->evidence_file_path);
            }
            $path = $request->file('evidence_file')->store('compliance_evidence', 'public');
            $validated['evidence_file_path'] = $path;
        }

        $complianceTracking->update($validated);

        return response()->json([
            'message' => 'Compliance tracking updated successfully',
            'data' => $complianceTracking->fresh()->load(['company', 'complianceRequirement', 'responsiblePerson']),
        ]);
    }

    /**
     * Remove the specified compliance tracking.
     */
    public function destroy(ComplianceTracking $complianceTracking)
    {
        // Delete associated file if exists
        if ($complianceTracking->evidence_file_path) {
            Storage::disk('public')->delete($complianceTracking->evidence_file_path);
        }

        $complianceTracking->delete();

        return response()->json([
            'message' => 'Compliance tracking deleted successfully',
        ]);
    }

    /**
     * Get overdue compliance trackings.
     */
    public function overdue(Request $request)
    {
        $trackings = ComplianceTracking::with(['company', 'complianceRequirement', 'responsiblePerson'])
            ->overdue()
            ->when($request->company_id, function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            })
            ->orderBy('due_date', 'asc')
            ->paginate(15);

        return response()->json($trackings);
    }
}
