<?php

namespace App\Http\Controllers;

use App\Models\AdvancePayment;
use App\Models\AdvanceRecovery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdvancePaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = AdvancePayment::with(['project', 'contract', 'currency', 'company']);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $advances = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $advances
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'contract_id' => 'required|exists:contracts,id',
            'advance_type' => 'required|in:mobilization,materials,equipment',
            'advance_percentage' => 'required|numeric|min:0|max:100',
            'advance_amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'guarantee_required' => 'boolean',
            'guarantee_id' => 'nullable|exists:retention_guarantees,id',
            'recovery_start_percentage' => 'required|numeric|min:0|max:100',
            'recovery_percentage' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
        ]);

        $validated['balance_amount'] = $validated['advance_amount'];

        $advance = AdvancePayment::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Advance payment created successfully',
            'data' => $advance->load(['project', 'contract', 'currency'])
        ], 201);
    }

    public function show($id)
    {
        $advance = AdvancePayment::with(['project', 'contract', 'currency', 'recoveries', 'guarantee'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $advance
        ]);
    }

    public function update(Request $request, $id)
    {
        $advance = AdvancePayment::findOrFail($id);

        $validated = $request->validate([
            'recovery_percentage' => 'sometimes|numeric|min:0|max:100',
            'status' => 'sometimes|in:pending,paid,recovering,fully_recovered',
            'notes' => 'nullable|string',
        ]);

        $advance->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Advance payment updated successfully',
            'data' => $advance
        ]);
    }

    public function destroy($id)
    {
        $advance = AdvancePayment::findOrFail($id);
        $advance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Advance payment deleted successfully'
        ]);
    }

    public function getRecoveries($id)
    {
        $advance = AdvancePayment::findOrFail($id);
        $recoveries = $advance->recoveries()->get();

        return response()->json([
            'success' => true,
            'data' => $recoveries
        ]);
    }

    public function approve(Request $request, $id)
    {
        $advance = AdvancePayment::findOrFail($id);

        if ($advance->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Advance payment is not in pending status'
            ], 422);
        }

        $advance->update([
            'status' => 'paid',
            'approved_by_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Advance payment approved successfully',
            'data' => $advance->load('approvedBy')
        ]);
    }

    public function pay(Request $request, $id)
    {
        $advance = AdvancePayment::findOrFail($id);

        $validated = $request->validate([
            'payment_date' => 'required|date',
        ]);

        $advance->update([
            'status' => 'recovering',
            'payment_date' => $validated['payment_date'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Advance payment marked as paid',
            'data' => $advance
        ]);
    }

    public function statement($id)
    {
        $advance = AdvancePayment::with([
            'project',
            'contract',
            'currency',
            'recoveries'
        ])->findOrFail($id);

        $statement = [
            'advance' => $advance,
            'total_advance' => $advance->advance_amount,
            'total_recovered' => $advance->recovered_amount,
            'current_balance' => $advance->balance_amount,
            'recovery_percentage' => $advance->recovery_percentage,
            'recoveries' => $advance->recoveries,
        ];

        return response()->json([
            'success' => true,
            'data' => $statement
        ]);
    }
}
