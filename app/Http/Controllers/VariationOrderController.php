<?php

namespace App\Http\Controllers;

use App\Models\VariationOrder;
use App\Models\Project;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VariationOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = VariationOrder::with(['project', 'contract', 'requestedBy']);

        // Filter by project
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $variationOrders = $query->latest()->paginate(15);
        $projects = Project::all();

        return view('variation-orders.index', compact('variationOrders', 'projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::where('status', 'active')->get();
        $contracts = Contract::where('status', 'active')->get();
        $users = User::where('is_active', true)->get();

        return view('variation-orders.create', compact('projects', 'contracts', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'justification' => 'nullable|string',
            'type' => 'required|in:addition,omission,modification,substitution',
            'source' => 'required|in:client,consultant,contractor,design_change,site_condition',
            'estimated_value' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'time_impact_days' => 'nullable|integer',
            'identification_date' => 'required|date',
            'priority' => 'required|in:low,medium,high,critical',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Generate sequence number
            $sequenceNumber = VariationOrder::where('project_id', $validated['project_id'])->max('sequence_number') + 1;

            $variationOrder = VariationOrder::create([
                ...$validated,
                'sequence_number' => $sequenceNumber,
                'requested_by' => auth()->id(),
                'status' => 'draft',
            ]);

            // Generate VO number
            $variationOrder->vo_number = $variationOrder->generateVoNumber();
            $variationOrder->save();

            // Add timeline entry
            $variationOrder->addTimelineEntry('Created', null, 'draft', 'Variation order created');

            DB::commit();

            return redirect()->route('variation-orders.show', $variationOrder)
                ->with('success', 'تم إنشاء أمر التغيير بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء أمر التغيير: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(VariationOrder $variationOrder)
    {
        $variationOrder->load([
            'project',
            'contract',
            'requestedBy',
            'preparedBy',
            'approvedBy',
            'items',
            'attachments.uploadedBy',
            'timeline.performedBy'
        ]);

        return view('variation-orders.show', compact('variationOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VariationOrder $variationOrder)
    {
        // Only allow editing if in draft or identified status
        if (!in_array($variationOrder->status, ['draft', 'identified'])) {
            return back()->with('error', 'لا يمكن تعديل أمر التغيير في الحالة الحالية');
        }

        $projects = Project::where('status', 'active')->get();
        $contracts = Contract::where('status', 'active')->get();
        $users = User::where('is_active', true)->get();

        return view('variation-orders.edit', compact('variationOrder', 'projects', 'contracts', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VariationOrder $variationOrder)
    {
        // Only allow updating if in draft or identified status
        if (!in_array($variationOrder->status, ['draft', 'identified'])) {
            return back()->with('error', 'لا يمكن تعديل أمر التغيير في الحالة الحالية');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'justification' => 'nullable|string',
            'type' => 'required|in:addition,omission,modification,substitution',
            'source' => 'required|in:client,consultant,contractor,design_change,site_condition',
            'estimated_value' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'time_impact_days' => 'nullable|integer',
            'identification_date' => 'required|date',
            'priority' => 'required|in:low,medium,high,critical',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $variationOrder->update($validated);

            // Add timeline entry
            $variationOrder->addTimelineEntry('Updated', null, null, 'Variation order updated');

            DB::commit();

            return redirect()->route('variation-orders.show', $variationOrder)
                ->with('success', 'تم تحديث أمر التغيير بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تحديث أمر التغيير: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VariationOrder $variationOrder)
    {
        // Only allow deletion if in draft status
        if ($variationOrder->status !== 'draft') {
            return back()->with('error', 'لا يمكن حذف أمر التغيير في الحالة الحالية');
        }

        $variationOrder->delete();

        return redirect()->route('variation-orders.index')
            ->with('success', 'تم حذف أمر التغيير بنجاح');
    }

    /**
     * Submit variation order for review
     */
    public function submit(VariationOrder $variationOrder)
    {
        if ($variationOrder->status !== 'draft') {
            return back()->with('error', 'لا يمكن تقديم أمر التغيير في الحالة الحالية');
        }

        DB::beginTransaction();
        try {
            $variationOrder->update([
                'status' => 'submitted',
                'submission_date' => now(),
            ]);

            $variationOrder->addTimelineEntry('Submitted', 'draft', 'submitted', 'Variation order submitted for review');

            DB::commit();

            return back()->with('success', 'تم تقديم أمر التغيير بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تقديم أمر التغيير: ' . $e->getMessage());
        }
    }

    /**
     * Approve variation order
     */
    public function approve(Request $request, VariationOrder $variationOrder)
    {
        if (!in_array($variationOrder->status, ['submitted', 'under_review', 'negotiating'])) {
            return back()->with('error', 'لا يمكن اعتماد أمر التغيير في الحالة الحالية');
        }

        $validated = $request->validate([
            'approved_value' => 'required|numeric|min:0',
            'approved_extension_days' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $variationOrder->status;
            
            $variationOrder->update([
                'status' => 'approved',
                'approved_value' => $validated['approved_value'],
                'approved_extension_days' => $validated['approved_extension_days'] ?? 0,
                'extension_approved' => ($validated['approved_extension_days'] ?? 0) > 0,
                'approval_date' => now(),
                'approved_by' => auth()->id(),
            ]);

            $variationOrder->addTimelineEntry(
                'Approved',
                $oldStatus,
                'approved',
                $validated['notes'] ?? 'Variation order approved'
            );

            DB::commit();

            return back()->with('success', 'تم اعتماد أمر التغيير بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء اعتماد أمر التغيير: ' . $e->getMessage());
        }
    }

    /**
     * Reject variation order
     */
    public function reject(Request $request, VariationOrder $variationOrder)
    {
        if (!in_array($variationOrder->status, ['submitted', 'under_review', 'negotiating'])) {
            return back()->with('error', 'لا يمكن رفض أمر التغيير في الحالة الحالية');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $variationOrder->status;
            $variationOrder->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'approved_by' => auth()->id(),
            ]);

            $variationOrder->addTimelineEntry(
                'Rejected',
                $oldStatus,
                'rejected',
                $validated['rejection_reason']
            );

            DB::commit();

            return back()->with('success', 'تم رفض أمر التغيير');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء رفض أمر التغيير: ' . $e->getMessage());
        }
    }

    /**
     * Get statistics
     */
    public function statistics(Request $request)
    {
        $projectId = $request->get('project_id');

        $query = VariationOrder::query();
        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        $statistics = [
            'total_count' => (clone $query)->count(),
            'by_status' => (clone $query)->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status'),
            'by_type' => (clone $query)->select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type'),
            'total_estimated_value' => (clone $query)->sum('estimated_value'),
            'total_approved_value' => (clone $query)->sum('approved_value'),
            'total_executed_value' => (clone $query)->sum('executed_value'),
            'total_time_impact' => (clone $query)->sum('time_impact_days'),
        ];

        return response()->json($statistics);
    }

    /**
     * Export to PDF
     */
    public function export(VariationOrder $variationOrder)
    {
        $variationOrder->load([
            'project',
            'contract',
            'requestedBy',
            'preparedBy',
            'approvedBy',
            'items',
            'attachments',
            'timeline.performedBy'
        ]);

        return view('variation-orders.print', compact('variationOrder'));
    }

    /**
     * Get variation orders for a specific project
     */
    public function byProject($projectId)
    {
        $project = Project::findOrFail($projectId);
        $variationOrders = VariationOrder::where('project_id', $projectId)
            ->with(['contract', 'requestedBy'])
            ->latest()
            ->get();

        return response()->json([
            'project' => $project,
            'variation_orders' => $variationOrders,
        ]);
    }
}
