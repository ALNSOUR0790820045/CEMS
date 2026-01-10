<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubcontractorIpc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubcontractorIpcController extends Controller
{
    public function index(Request $request)
    {
        $query = SubcontractorIpc::with(['subcontractor', 'project', 'agreement'])
            ->where('company_id', Auth::user()->company_id);

        if ($request->has('subcontractor_id')) {
            $query->where('subcontractor_id', $request->subcontractor_id);
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        return response()->json($query->latest()->paginate($perPage));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ipc_date' => 'required|date',
            'period_from' => 'required|date',
            'period_to' => 'required|date|after:period_from',
            'subcontractor_agreement_id' => 'required|exists:subcontractor_agreements,id',
            'subcontractor_id' => 'required|exists:subcontractors,id',
            'project_id' => 'required|exists:projects,id',
            'ipc_type' => 'required|in:interim,final',
            'current_work_value' => 'required|numeric|min:0',
            'retention_percentage' => 'required|numeric|min:0|max:100',
            'currency_id' => 'required|exists:currencies,id',
            'items' => 'nullable|array',
            'items.*.description' => 'required|string',
            'items.*.current_quantity' => 'required|numeric|min:0',
            'items.*.unit_rate' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->except('items');
            $data['company_id'] = Auth::user()->company_id;
            $data['created_by_id'] = Auth::id();

            $ipc = SubcontractorIpc::create($data);

            // Create IPC items if provided
            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    $item['company_id'] = Auth::user()->company_id;
                    $ipc->items()->create($item);
                }
            }

            DB::commit();

            return response()->json($ipc->load(['items', 'subcontractor', 'project', 'agreement']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error creating IPC', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        $ipc = SubcontractorIpc::with([
            'subcontractor',
            'project',
            'agreement',
            'currency',
            'items.unit',
            'payment'
        ])->findOrFail($id);

        if ($ipc->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($ipc);
    }

    public function update(Request $request, string $id)
    {
        $ipc = SubcontractorIpc::findOrFail($id);

        if ($ipc->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Only allow updates if IPC is in draft status
        if ($ipc->status !== 'draft') {
            return response()->json(['message' => 'Can only update draft IPCs'], 422);
        }

        $validator = Validator::make($request->all(), [
            'ipc_date' => 'required|date',
            'period_from' => 'required|date',
            'period_to' => 'required|date|after:period_from',
            'current_work_value' => 'required|numeric|min:0',
            'retention_percentage' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $ipc->update($request->except('items'));

            // Update items if provided
            if ($request->has('items')) {
                $ipc->items()->delete();
                foreach ($request->items as $item) {
                    $item['company_id'] = Auth::user()->company_id;
                    $ipc->items()->create($item);
                }
            }

            DB::commit();

            return response()->json($ipc->load(['items', 'subcontractor', 'project']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error updating IPC', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        $ipc = SubcontractorIpc::findOrFail($id);

        if ($ipc->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Only allow deletion if IPC is in draft status
        if ($ipc->status !== 'draft') {
            return response()->json(['message' => 'Can only delete draft IPCs'], 422);
        }

        $ipc->delete();

        return response()->json(['message' => 'IPC deleted successfully']);
    }

    /**
     * Approve an IPC.
     */
    public function approve(string $id)
    {
        $ipc = SubcontractorIpc::findOrFail($id);

        if ($ipc->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($ipc->status !== 'submitted' && $ipc->status !== 'under_review') {
            return response()->json(['message' => 'IPC must be submitted or under review to approve'], 422);
        }

        $ipc->update([
            'status' => 'approved',
            'approved_by_id' => Auth::id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'IPC approved successfully',
            'ipc' => $ipc
        ]);
    }

    /**
     * Generate PDF for IPC.
     */
    public function pdf(string $id)
    {
        $ipc = SubcontractorIpc::with([
            'subcontractor',
            'project',
            'agreement',
            'items.unit'
        ])->findOrFail($id);

        if ($ipc->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Generate PDF using DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('subcontractor-ipc.pdf', [
            'ipc' => $ipc,
        ]);
        
        $filename = 'subcontractor_ipc_' . $ipc->ipc_number . '_' . time() . '.pdf';
        
        return $pdf->download($filename);
    }
}
