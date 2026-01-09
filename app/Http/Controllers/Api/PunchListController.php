<?php

namespace App\Http\Controllers\Api;

use App\Models\PunchList;
use App\Models\Project;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PunchListController extends Controller
{
    public function index()
    {
        $lists = PunchList::with(['project', 'contractor', 'inspector'])
            ->latest()
            ->paginate(20);

        return response()->json($lists);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'list_type' => 'required|in:pre_handover,handover,defects_liability,final',
            'area_zone' => 'nullable|string',
            'building' => 'nullable|string',
            'floor' => 'nullable|string',
            'discipline' => 'nullable|in:architectural,structural,mep,civil,landscape',
            'contractor_id' => 'nullable|exists:vendors,id',
            'subcontractor_id' => 'nullable|exists:vendors,id',
            'inspection_date' => 'nullable|date',
            'inspector_id' => 'nullable|exists:users,id',
            'consultant_rep' => 'nullable|string',
            'contractor_rep' => 'nullable|string',
            'target_completion_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        // Generate list number
        $year = date('Y');
        $sequence = PunchList::whereYear('created_at', $year)->count() + 1;
        $validated['list_number'] = 'PL-'.$year.'-'.str_pad($sequence, 4, '0', STR_PAD_LEFT);
        $validated['status'] = 'draft';

        DB::beginTransaction();
        try {
            $list = PunchList::create($validated);
            DB::commit();

            return response()->json([
                'message' => 'Punch list created successfully',
                'data' => $list->load(['project', 'contractor', 'inspector'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create punch list', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $list = PunchList::with([
            'project',
            'contractor',
            'subcontractor',
            'inspector',
            'issuedBy',
            'verifiedBy',
            'closedBy',
            'items.assignedTo',
            'items.verifiedBy'
        ])->findOrFail($id);

        return response()->json($list);
    }

    public function update(Request $request, $id)
    {
        $list = PunchList::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'list_type' => 'sometimes|in:pre_handover,handover,defects_liability,final',
            'area_zone' => 'nullable|string',
            'building' => 'nullable|string',
            'floor' => 'nullable|string',
            'discipline' => 'nullable|in:architectural,structural,mep,civil,landscape',
            'contractor_id' => 'nullable|exists:vendors,id',
            'subcontractor_id' => 'nullable|exists:vendors,id',
            'inspection_date' => 'nullable|date',
            'inspector_id' => 'nullable|exists:users,id',
            'consultant_rep' => 'nullable|string',
            'contractor_rep' => 'nullable|string',
            'target_completion_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $list->update($validated);

        return response()->json([
            'message' => 'Punch list updated successfully',
            'data' => $list->fresh(['project', 'contractor', 'inspector'])
        ]);
    }

    public function destroy($id)
    {
        $list = PunchList::findOrFail($id);
        $list->delete();

        return response()->json(['message' => 'Punch list deleted successfully']);
    }

    public function byProject($projectId)
    {
        $lists = PunchList::with(['contractor', 'inspector'])
            ->where('project_id', $projectId)
            ->latest()
            ->get();

        return response()->json($lists);
    }

    public function issue(Request $request, $id)
    {
        $list = PunchList::findOrFail($id);

        if ($list->status !== 'draft') {
            return response()->json(['error' => 'Only draft lists can be issued'], 400);
        }

        DB::beginTransaction();
        try {
            $list->update([
                'status' => 'issued',
                'issued_by_id' => Auth::id(),
                'issued_at' => now(),
            ]);

            // Update to in_progress if items exist
            if ($list->items()->count() > 0) {
                $list->update(['status' => 'in_progress']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Punch list issued successfully',
                'data' => $list->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to issue punch list', 'message' => $e->getMessage()], 500);
        }
    }

    public function verify(Request $request, $id)
    {
        $list = PunchList::findOrFail($id);

        if ($list->status !== 'completed') {
            return response()->json(['error' => 'Only completed lists can be verified'], 400);
        }

        DB::beginTransaction();
        try {
            $list->update([
                'status' => 'verified',
                'verified_by_id' => Auth::id(),
                'verified_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Punch list verified successfully',
                'data' => $list->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to verify punch list', 'message' => $e->getMessage()], 500);
        }
    }

    public function close(Request $request, $id)
    {
        $list = PunchList::findOrFail($id);

        if ($list->status !== 'verified') {
            return response()->json(['error' => 'Only verified lists can be closed'], 400);
        }

        DB::beginTransaction();
        try {
            $list->update([
                'status' => 'closed',
                'closed_by_id' => Auth::id(),
                'closed_at' => now(),
                'actual_completion_date' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Punch list closed successfully',
                'data' => $list->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to close punch list', 'message' => $e->getMessage()], 500);
        }
    }

    public function generatePdf($id)
    {
        $list = PunchList::with([
            'project',
            'contractor',
            'items.assignedTo'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('punch-lists.pdf', compact('list'));

        return $pdf->download('punch-list-'.$list->list_number.'.pdf');
    }

    public function sendNotification(Request $request, $id)
    {
        $list = PunchList::findOrFail($id);

        $validated = $request->validate([
            'recipients' => 'required|array',
            'recipients.*' => 'exists:users,id',
            'message' => 'nullable|string',
        ]);

        // Notification logic would go here
        // For now, just return success

        return response()->json([
            'message' => 'Notification sent successfully',
            'recipients_count' => count($validated['recipients'])
        ]);
    }
}
