<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of leave requests.
     */
    public function index(Request $request)
    {
        $query = LeaveRequest::with(['employee.user', 'approvedBy']);

        // Filter by company
        if ($request->has('company_id')) {
            $query->forCompany($request->company_id);
        }

        // Filter by employee
        if ($request->has('employee_id')) {
            $query->forEmployee($request->employee_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by leave type
        if ($request->has('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        $perPage = $request->get('per_page', 15);
        $leaveRequests = $query->orderBy('requested_at', 'desc')
            ->paginate($perPage);

        return response()->json($leaveRequests);
    }

    /**
     * Store a newly created leave request.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|in:annual,sick,unpaid,maternity,emergency',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $leaveRequest = LeaveRequest::create($request->all());
        
        // Calculate total days
        $leaveRequest->calculateTotalDays();

        return response()->json([
            'message' => 'Leave request created successfully',
            'data' => $leaveRequest->load(['employee.user'])
        ], 201);
    }

    /**
     * Display the specified leave request.
     */
    public function show($id)
    {
        $leaveRequest = LeaveRequest::with(['employee.user', 'approvedBy'])
            ->findOrFail($id);

        return response()->json($leaveRequest);
    }

    /**
     * Update the specified leave request.
     */
    public function update(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        // Only pending requests can be updated
        if ($leaveRequest->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending leave requests can be updated'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'leave_type' => 'nullable|in:annual,sick,unpaid,maternity,emergency',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $leaveRequest->update($request->all());

        // Recalculate total days if dates changed
        if ($request->has('start_date') || $request->has('end_date')) {
            $leaveRequest->calculateTotalDays();
        }

        return response()->json([
            'message' => 'Leave request updated successfully',
            'data' => $leaveRequest->load(['employee.user'])
        ]);
    }

    /**
     * Approve a leave request.
     */
    public function approve(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'approved_by_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $leaveRequest = LeaveRequest::findOrFail($id);

        if ($leaveRequest->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending leave requests can be approved'
            ], 400);
        }

        $leaveRequest->approve($request->approved_by_id);

        return response()->json([
            'message' => 'Leave request approved successfully',
            'data' => $leaveRequest->load(['employee.user', 'approvedBy'])
        ]);
    }

    /**
     * Reject a leave request.
     */
    public function reject(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'approved_by_id' => 'required|exists:users,id',
            'rejection_reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $leaveRequest = LeaveRequest::findOrFail($id);

        if ($leaveRequest->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending leave requests can be rejected'
            ], 400);
        }

        $leaveRequest->reject($request->approved_by_id, $request->rejection_reason);

        return response()->json([
            'message' => 'Leave request rejected successfully',
            'data' => $leaveRequest->load(['employee.user', 'approvedBy'])
        ]);
    }

    /**
     * Cancel a leave request.
     */
    public function cancel($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        if (!in_array($leaveRequest->status, ['pending', 'approved'])) {
            return response()->json([
                'message' => 'Only pending or approved leave requests can be cancelled'
            ], 400);
        }

        $leaveRequest->cancel();

        return response()->json([
            'message' => 'Leave request cancelled successfully',
            'data' => $leaveRequest
        ]);
    }

    /**
     * Remove the specified leave request.
     */
    public function destroy($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        
        // Only pending requests can be deleted
        if ($leaveRequest->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending leave requests can be deleted'
            ], 400);
        }

        $leaveRequest->delete();

        return response()->json([
            'message' => 'Leave request deleted successfully'
        ]);
    }
}
