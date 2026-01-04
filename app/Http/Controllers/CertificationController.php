<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificationController extends Controller
{
    /**
     * Display a listing of certifications.
     */
    public function index(Request $request)
    {
        $query = Certification::with(['company'])
            ->when($request->company_id, function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            })
            ->when($request->certification_type, function ($q) use ($request) {
                $q->where('certification_type', $request->certification_type);
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->latest();

        $certifications = $query->paginate(15);

        return response()->json($certifications);
    }

    /**
     * Store a newly created certification.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'certification_name' => 'required|string|max:255',
            'certification_type' => 'required|in:company,employee,equipment,material,contractor',
            'entity_type' => 'required|string|max:255',
            'entity_id' => 'required|integer',
            'issuing_authority' => 'required|string|max:255',
            'certificate_number' => 'nullable|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'is_renewable' => 'boolean',
            'renewal_period_days' => 'nullable|integer|min:1',
            'status' => 'nullable|in:active,expired,suspended,renewed',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'alert_before_days' => 'nullable|integer|min:1|max:365',
            'notes' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
        ]);

        // Handle file upload
        if ($request->hasFile('certificate_file')) {
            $path = $request->file('certificate_file')->store('certifications', 'public');
            $validated['certificate_file_path'] = $path;
        }

        $certification = Certification::create($validated);

        return response()->json([
            'message' => 'Certification created successfully',
            'data' => $certification->load('company')
        ], 201);
    }

    /**
     * Display the specified certification.
     */
    public function show(Certification $certification)
    {
        return response()->json($certification->load('company'));
    }

    /**
     * Update the specified certification.
     */
    public function update(Request $request, Certification $certification)
    {
        $validated = $request->validate([
            'certification_name' => 'sometimes|required|string|max:255',
            'certification_type' => 'sometimes|required|in:company,employee,equipment,material,contractor',
            'entity_type' => 'sometimes|required|string|max:255',
            'entity_id' => 'sometimes|required|integer',
            'issuing_authority' => 'sometimes|required|string|max:255',
            'certificate_number' => 'nullable|string|max:255',
            'issue_date' => 'sometimes|required|date',
            'expiry_date' => 'sometimes|required|date|after:issue_date',
            'is_renewable' => 'boolean',
            'renewal_period_days' => 'nullable|integer|min:1',
            'status' => 'nullable|in:active,expired,suspended,renewed',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'alert_before_days' => 'nullable|integer|min:1|max:365',
            'notes' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('certificate_file')) {
            // Delete old file if exists
            if ($certification->certificate_file_path) {
                Storage::disk('public')->delete($certification->certificate_file_path);
            }
            $path = $request->file('certificate_file')->store('certifications', 'public');
            $validated['certificate_file_path'] = $path;
        }

        $certification->update($validated);

        return response()->json([
            'message' => 'Certification updated successfully',
            'data' => $certification->fresh()->load('company')
        ]);
    }

    /**
     * Remove the specified certification.
     */
    public function destroy(Certification $certification)
    {
        // Delete associated file if exists
        if ($certification->certificate_file_path) {
            Storage::disk('public')->delete($certification->certificate_file_path);
        }

        $certification->delete();

        return response()->json([
            'message' => 'Certification deleted successfully'
        ]);
    }

    /**
     * Get expiring certifications.
     */
    public function expiring(Request $request)
    {
        $days = $request->input('days', 30);

        $certifications = Certification::with(['company'])
            ->expiring($days)
            ->when($request->company_id, function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            })
            ->orderBy('expiry_date', 'asc')
            ->paginate(15);

        return response()->json($certifications);
    }

    /**
     * Renew a certification.
     */
    public function renew(Request $request, Certification $certification)
    {
        $validated = $request->validate([
            'new_issue_date' => 'required|date',
            'new_expiry_date' => 'required|date|after:new_issue_date',
            'certificate_number' => 'nullable|string|max:255',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string',
        ]);

        // Mark old certification as renewed
        $certification->update(['status' => 'renewed']);

        // Create new certification with renewed data
        $newCertificationData = $certification->toArray();
        unset($newCertificationData['id'], $newCertificationData['created_at'], $newCertificationData['updated_at'], $newCertificationData['certification_code']);
        
        $newCertificationData['issue_date'] = $validated['new_issue_date'];
        $newCertificationData['expiry_date'] = $validated['new_expiry_date'];
        $newCertificationData['status'] = 'active';
        $newCertificationData['last_alert_sent'] = null;

        if (isset($validated['certificate_number'])) {
            $newCertificationData['certificate_number'] = $validated['certificate_number'];
        }

        if (isset($validated['notes'])) {
            $newCertificationData['notes'] = $validated['notes'];
        }

        // Handle file upload for renewed certification
        if ($request->hasFile('certificate_file')) {
            $path = $request->file('certificate_file')->store('certifications', 'public');
            $newCertificationData['certificate_file_path'] = $path;
        } else {
            unset($newCertificationData['certificate_file_path']);
        }

        $newCertification = Certification::create($newCertificationData);

        return response()->json([
            'message' => 'Certification renewed successfully',
            'data' => $newCertification->load('company')
        ], 201);
    }
}
