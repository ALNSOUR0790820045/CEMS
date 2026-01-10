<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CertificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Certification::with(['company', 'currency', 'renewals']);

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by reference
        if ($request->has('reference_type') && $request->has('reference_id')) {
            $query->byReference($request->reference_type, $request->reference_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('certification_number', 'like', "%{$search}%")
                  ->orWhere('issuing_authority', 'like', "%{$search}%");
            });
        }

        $certifications = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $certifications,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'type' => 'required|in:license,permit,certificate,registration,insurance',
            'category' => 'required|in:company,project,employee,equipment,safety',
            'issuing_authority' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'renewal_date' => 'nullable|date',
            'status' => 'nullable|in:active,expired,pending_renewal,suspended,cancelled',
            'reference_type' => 'nullable|string|max:255',
            'reference_id' => 'nullable|integer',
            'cost' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'attachment_path' => 'nullable|string|max:255',
            'reminder_days' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['certification_number'] = Certification::generateCertificationNumber();

        $certification = Certification::create($data);
        $certification->load(['company', 'currency']);

        return response()->json([
            'success' => true,
            'message' => 'Certification created successfully',
            'data' => $certification,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Certification $certification): JsonResponse
    {
        $certification->load(['company', 'currency', 'renewals.processedBy']);

        return response()->json([
            'success' => true,
            'data' => $certification,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Certification $certification): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'type' => 'sometimes|required|in:license,permit,certificate,registration,insurance',
            'category' => 'sometimes|required|in:company,project,employee,equipment,safety',
            'issuing_authority' => 'sometimes|required|string|max:255',
            'issue_date' => 'sometimes|required|date',
            'expiry_date' => 'sometimes|required|date',
            'renewal_date' => 'nullable|date',
            'status' => 'nullable|in:active,expired,pending_renewal,suspended,cancelled',
            'reference_type' => 'nullable|string|max:255',
            'reference_id' => 'nullable|integer',
            'cost' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'attachment_path' => 'nullable|string|max:255',
            'reminder_days' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $certification->update($validator->validated());
        $certification->load(['company', 'currency']);

        return response()->json([
            'success' => true,
            'message' => 'Certification updated successfully',
            'data' => $certification,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Certification $certification): JsonResponse
    {
        $certification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Certification deleted successfully',
        ]);
    }

    /**
     * Get expiring certifications.
     */
    public function expiring(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        
        $certifications = Certification::with(['company', 'currency'])
            ->where('company_id', $request->get('company_id'))
            ->expiring($days)
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $certifications,
        ]);
    }

    /**
     * Get expired certifications.
     */
    public function expired(Request $request): JsonResponse
    {
        $certifications = Certification::with(['company', 'currency'])
            ->where('company_id', $request->get('company_id'))
            ->expired()
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $certifications,
        ]);
    }

    /**
     * Renew a certification.
     */
    public function renew(Request $request, $id): JsonResponse
    {
        $certification = Certification::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'new_expiry_date' => 'required|date|after:' . $certification->expiry_date,
            'renewal_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $renewal = $certification->renew(
            $request->new_expiry_date,
            $request->renewal_cost,
            $request->user()->id ?? null,
            $request->notes
        );

        $certification->load(['company', 'currency', 'renewals']);

        return response()->json([
            'success' => true,
            'message' => 'Certification renewed successfully',
            'data' => [
                'certification' => $certification,
                'renewal' => $renewal,
            ],
        ]);
    }
}
