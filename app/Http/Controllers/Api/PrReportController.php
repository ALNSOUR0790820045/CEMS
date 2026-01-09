<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequisition;
use App\Models\PrApprovalWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrReportController extends Controller
{
    /**
     * PR Status Report
     */
    public function statusReport(Request $request)
    {
        $query = PurchaseRequisition::query();

        if ($request->has('date_from')) {
            $query->where('requisition_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('requisition_date', '<=', $request->date_to);
        }

        $statusCounts = $query->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $totalAmount = $query->sum('total_estimated_amount');

        return response()->json([
            'status_breakdown' => $statusCounts,
            'total_amount' => $totalAmount,
            'total_count' => $statusCounts->sum('count'),
        ]);
    }

    /**
     * PR by Department Report
     */
    public function byDepartment(Request $request)
    {
        $query = PurchaseRequisition::with('department');

        if ($request->has('date_from')) {
            $query->where('requisition_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('requisition_date', '<=', $request->date_to);
        }

        $departmentData = $query->select(
            'department_id',
            DB::raw('count(*) as pr_count'),
            DB::raw('sum(total_estimated_amount) as total_amount')
        )
        ->groupBy('department_id')
        ->get();

        return response()->json($departmentData);
    }

    /**
     * Pending Approvals Report
     */
    public function pendingApprovals(Request $request)
    {
        $userId = $request->get('approver_id', auth()->id());

        $pendingApprovals = PrApprovalWorkflow::with([
            'purchaseRequisition.requestedBy',
            'purchaseRequisition.project',
            'purchaseRequisition.department'
        ])
        ->where('approver_id', $userId)
        ->where('status', 'pending')
        ->orderBy('created_at', 'asc')
        ->get();

        return response()->json($pendingApprovals);
    }
}
