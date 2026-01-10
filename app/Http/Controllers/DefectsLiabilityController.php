<?php

namespace App\Http\Controllers;

use App\Models\DefectsLiability;
use App\Models\DefectNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DefectsLiabilityController extends Controller
{
    public function index(Request $request)
    {
        $query = DefectsLiability::with(['project', 'contract', 'retention', 'company']);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $dlps = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $dlps
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'contract_id' => 'required|exists:contracts,id',
            'retention_id' => 'nullable|exists:retentions,id',
            'taking_over_date' => 'required|date',
            'dlp_months' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
        ]);

        $takingOverDate = \Carbon\Carbon::parse($validated['taking_over_date']);
        $validated['dlp_start_date'] = $takingOverDate;
        $validated['dlp_end_date'] = $takingOverDate->copy()->addMonths($validated['dlp_months']);

        $dlp = DefectsLiability::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Defects liability period created successfully',
            'data' => $dlp->load(['project', 'contract'])
        ], 201);
    }

    public function show($id)
    {
        $dlp = DefectsLiability::with(['project', 'contract', 'retention', 'notifications'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $dlp
        ]);
    }

    public function update(Request $request, $id)
    {
        $dlp = DefectsLiability::findOrFail($id);

        $validated = $request->validate([
            'final_certificate_date' => 'nullable|date',
            'status' => 'sometimes|in:active,completed,extended',
            'performance_bond_released' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $dlp->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'DLP updated successfully',
            'data' => $dlp
        ]);
    }

    public function destroy($id)
    {
        $dlp = DefectsLiability::findOrFail($id);
        $dlp->delete();

        return response()->json([
            'success' => true,
            'message' => 'DLP deleted successfully'
        ]);
    }

    public function getNotifications($id)
    {
        $dlp = DefectsLiability::findOrFail($id);
        $notifications = $dlp->notifications()->get();

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    public function extend(Request $request, $id)
    {
        $dlp = DefectsLiability::findOrFail($id);

        if ($dlp->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'DLP must be active to extend'
            ], 422);
        }

        $validated = $request->validate([
            'extension_months' => 'required|integer|min:1',
            'extension_reason' => 'required|string',
        ]);

        $newEndDate = \Carbon\Carbon::parse($dlp->dlp_end_date)->addMonths($validated['extension_months']);

        $dlp->update([
            'dlp_end_date' => $newEndDate,
            'dlp_months' => $dlp->dlp_months + $validated['extension_months'],
            'extension_months' => $dlp->extension_months + $validated['extension_months'],
            'extension_reason' => $validated['extension_reason'],
            'status' => 'extended',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'DLP extended successfully',
            'data' => $dlp
        ]);
    }

    public function complete(Request $request, $id)
    {
        $dlp = DefectsLiability::findOrFail($id);

        if ($dlp->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'DLP is already completed'
            ], 422);
        }

        $validated = $request->validate([
            'final_certificate_date' => 'required|date',
        ]);

        $dlp->update([
            'status' => 'completed',
            'final_certificate_date' => $validated['final_certificate_date'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'DLP completed successfully',
            'data' => $dlp
        ]);
    }
}
