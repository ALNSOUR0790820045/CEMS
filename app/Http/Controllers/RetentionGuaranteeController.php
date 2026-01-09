<?php

namespace App\Http\Controllers;

use App\Models\RetentionGuarantee;
use Illuminate\Http\Request;

class RetentionGuaranteeController extends Controller
{
    public function index(Request $request)
    {
        $query = RetentionGuarantee::with(['retention', 'currency']);

        if ($request->has('retention_id')) {
            $query->where('retention_id', $request->retention_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $guarantees = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $guarantees
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'retention_id' => 'required|exists:retentions,id',
            'guarantee_type' => 'required|in:bank_guarantee,insurance_bond,cash',
            'guarantee_number' => 'required|string|unique:retention_guarantees,guarantee_number',
            'issuing_bank_id' => 'nullable|integer',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'in_lieu_of_retention' => 'boolean',
            'document_path' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $guarantee = RetentionGuarantee::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Guarantee created successfully',
            'data' => $guarantee->load(['retention', 'currency'])
        ], 201);
    }

    public function show($id)
    {
        $guarantee = RetentionGuarantee::with(['retention', 'currency'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $guarantee
        ]);
    }

    public function update(Request $request, $id)
    {
        $guarantee = RetentionGuarantee::findOrFail($id);

        $validated = $request->validate([
            'expiry_date' => 'sometimes|date',
            'status' => 'sometimes|in:active,expired,released,claimed',
            'notes' => 'nullable|string',
        ]);

        $guarantee->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Guarantee updated successfully',
            'data' => $guarantee
        ]);
    }

    public function destroy($id)
    {
        $guarantee = RetentionGuarantee::findOrFail($id);
        $guarantee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Guarantee deleted successfully'
        ]);
    }

    public function release(Request $request, $id)
    {
        $guarantee = RetentionGuarantee::findOrFail($id);

        if ($guarantee->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Only active guarantees can be released'
            ], 422);
        }

        $guarantee->update([
            'status' => 'released',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Guarantee released successfully',
            'data' => $guarantee
        ]);
    }

    public function expiring(Request $request)
    {
        $days = $request->input('days', 30);
        $expiryDate = now()->addDays($days);

        $guarantees = RetentionGuarantee::with(['retention', 'currency'])
            ->where('status', 'active')
            ->where('expiry_date', '<=', $expiryDate)
            ->where('expiry_date', '>=', now())
            ->orderBy('expiry_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $guarantees
        ]);
    }
}
