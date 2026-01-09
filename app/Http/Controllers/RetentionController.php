<?php

namespace App\Http\Controllers;

use App\Models\Retention;
use App\Models\RetentionAccumulation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RetentionController extends Controller
{
    public function index(Request $request)
    {
        $query = Retention::with(['project', 'contract', 'currency', 'company']);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $retentions = $query->paginate(15);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $retentions
            ]);
        }

        return view('retentions.index', compact('retentions'));
    }

    public function byProject($projectId)
    {
        $retentions = Retention::where('project_id', $projectId)
            ->with(['contract', 'currency'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $retentions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'contract_id' => 'required|exists:contracts,id',
            'retention_type' => 'required|in:performance,defects_liability,advance_payment,materials',
            'retention_percentage' => 'required|numeric|min:0|max:100',
            'max_retention_percentage' => 'required|numeric|min:0|max:100',
            'release_schedule' => 'required|in:single,staged',
            'total_contract_value' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'company_id' => 'required|exists:companies,id',
        ]);

        $retention = Retention::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Retention created successfully',
            'data' => $retention->load(['project', 'contract', 'currency'])
        ], 201);
    }

    public function show($id)
    {
        $retention = Retention::with(['project', 'contract', 'currency', 'accumulations', 'releases'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $retention
        ]);
    }

    public function update(Request $request, $id)
    {
        $retention = Retention::findOrFail($id);

        $validated = $request->validate([
            'retention_percentage' => 'sometimes|numeric|min:0|max:100',
            'max_retention_percentage' => 'sometimes|numeric|min:0|max:100',
            'status' => 'sometimes|in:accumulating,held,partially_released,fully_released,forfeited',
            'notes' => 'nullable|string',
        ]);

        $retention->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Retention updated successfully',
            'data' => $retention
        ]);
    }

    public function destroy($id)
    {
        $retention = Retention::findOrFail($id);
        $retention->delete();

        return response()->json([
            'success' => true,
            'message' => 'Retention deleted successfully'
        ]);
    }

    public function getAccumulations($id)
    {
        $retention = Retention::findOrFail($id);
        $accumulations = $retention->accumulations()->with('ipc')->get();

        return response()->json([
            'success' => true,
            'data' => $accumulations
        ]);
    }

    public function getReleases($id)
    {
        $retention = Retention::findOrFail($id);
        $releases = $retention->releases()->with('approvedBy')->get();

        return response()->json([
            'success' => true,
            'data' => $releases
        ]);
    }

    public function statement($id)
    {
        $retention = Retention::with([
            'project',
            'contract',
            'currency',
            'accumulations',
            'releases'
        ])->findOrFail($id);

        $statement = [
            'retention' => $retention,
            'total_accumulated' => $retention->total_retention_amount,
            'total_released' => $retention->released_amount,
            'current_balance' => $retention->balance_amount,
            'accumulations' => $retention->accumulations,
            'releases' => $retention->releases,
        ];

        return response()->json([
            'success' => true,
            'data' => $statement
        ]);
    }

    public function calculate(Request $request, $id)
    {
        $retention = Retention::findOrFail($id);

        $validated = $request->validate([
            'bill_amount' => 'required|numeric|min:0',
            'bill_date' => 'required|date',
            'ipc_id' => 'nullable|exists:i_p_c_s,id',
        ]);

        DB::beginTransaction();
        try {
            // Calculate retention amount
            $retentionAmount = $validated['bill_amount'] * ($retention->retention_percentage / 100);
            
            // Check max retention limit
            $newCumulativeRetention = $retention->total_retention_amount + $retentionAmount;
            $maxRetentionAmount = $retention->total_contract_value * ($retention->max_retention_percentage / 100);
            
            if ($newCumulativeRetention > $maxRetentionAmount) {
                $retentionAmount = $maxRetentionAmount - $retention->total_retention_amount;
                $newCumulativeRetention = $maxRetentionAmount;
            }

            // Create accumulation record
            $accumulation = RetentionAccumulation::create([
                'retention_id' => $retention->id,
                'ipc_id' => $validated['ipc_id'] ?? null,
                'bill_date' => $validated['bill_date'],
                'bill_amount' => $validated['bill_amount'],
                'retention_percentage' => $retention->retention_percentage,
                'retention_amount' => $retentionAmount,
                'cumulative_retention' => $newCumulativeRetention,
            ]);

            // Update retention totals
            $retention->update([
                'total_retention_amount' => $newCumulativeRetention,
                'balance_amount' => $newCumulativeRetention - $retention->released_amount,
                'status' => $newCumulativeRetention >= $maxRetentionAmount ? 'held' : 'accumulating',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Retention calculated and recorded successfully',
                'data' => [
                    'accumulation' => $accumulation,
                    'retention' => $retention->fresh()
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate retention: ' . $e->getMessage()
            ], 500);
        }
    }
}
