<?php

namespace App\Http\Controllers;

use App\Models\BillApprovalWorkflow;
use App\Models\ProgressBill;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillApprovalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = BillApprovalWorkflow::with(['progressBill', 'approver']);

        if ($request->has('progress_bill_id')) {
            $query->where('progress_bill_id', $request->progress_bill_id);
        }

        if ($request->has('approver_id')) {
            $query->where('approver_id', $request->approver_id);
        }

        $workflows = $query->orderBy('actioned_at', 'desc')->paginate(15);

        return response()->json($workflows);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'progress_bill_id' => 'required|exists:progress_bills,id',
            'approval_stage' => 'required|in:prepared,reviewed,certified,approved',
            'action' => 'required|in:approve,reject,return',
            'comments' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $workflow = new BillApprovalWorkflow($validated);
            $workflow->approver_id = Auth::id();
            $workflow->actioned_at = now();
            $workflow->save();

            // Update bill status based on action
            $bill = ProgressBill::find($validated['progress_bill_id']);
            
            if ($validated['action'] === 'approve') {
                switch ($validated['approval_stage']) {
                    case 'prepared':
                        $bill->status = 'submitted';
                        break;
                    case 'reviewed':
                        $bill->status = 'reviewed';
                        $bill->reviewed_by_id = Auth::id();
                        break;
                    case 'certified':
                        $bill->status = 'certified';
                        $bill->certified_by_id = Auth::id();
                        $bill->certified_at = now();
                        break;
                    case 'approved':
                        $bill->status = 'approved';
                        $bill->approved_by_id = Auth::id();
                        $bill->approved_at = now();
                        break;
                }
            } elseif ($validated['action'] === 'reject') {
                $bill->status = 'rejected';
                $bill->rejection_reason = $validated['comments'];
            } elseif ($validated['action'] === 'return') {
                $bill->status = 'draft';
            }

            $bill->save();

            DB::commit();
            return response()->json($workflow->load('progressBill', 'approver'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to record approval', 'message' => $e->getMessage()], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $workflow = BillApprovalWorkflow::with(['progressBill', 'approver'])->findOrFail($id);
        return response()->json($workflow);
    }

    public function getByBill(int $billId): JsonResponse
    {
        $workflows = BillApprovalWorkflow::where('progress_bill_id', $billId)
            ->with('approver')
            ->orderBy('actioned_at', 'asc')
            ->get();

        return response()->json($workflows);
    }
}
