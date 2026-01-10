<?php

namespace App\Http\Controllers;

use App\Models\ChangeOrder;
use App\Models\Project;
use App\Models\Tender;
use App\Models\Contract;
use App\Models\ProjectWbs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ChangeOrderController extends Controller
{
    /**
     * Display a listing of change orders.
     */
    public function index(Request $request)
    {
        $query = ChangeOrder::with(['project', 'tender', 'contract', 'creator']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->where('issue_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('issue_date', '<=', $request->date_to);
        }

        $changeOrders = $query->latest()->paginate(15);

        // Statistics
        $stats = [
            'total' => ChangeOrder::count(),
            'total_value' => ChangeOrder::sum('total_amount'),
            'approved' => ChangeOrder::where('status', 'approved')->count(),
            'pending' => ChangeOrder::whereIn('status', ['pending_pm', 'pending_technical', 'pending_consultant', 'pending_client'])->count(),
            'rejected' => ChangeOrder::where('status', 'rejected')->count(),
        ];

        return view('change-orders.index', compact('changeOrders', 'stats'));
    }

    /**
     * Show the form for creating a new change order.
     */
    public function create()
    {
        $projects = Project::where('status', 'active')->get();
        $tenders = Tender::all();
        $contracts = Contract::all();
        $coNumber = ChangeOrder::generateCoNumber();

        return view('change-orders.create', compact('projects', 'tenders', 'contracts', 'coNumber'));
    }

    /**
     * Store a newly created change order in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'tender_id' => 'nullable|exists:tenders,id',
            'original_contract_id' => 'nullable|exists:contracts,id',
            'issue_date' => 'required|date',
            'type' => 'required|in:scope_change,quantity_change,design_change,specification_change,other',
            'reason' => 'required|in:client_request,design_error,site_condition,regulatory,other',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'justification' => 'nullable|string',
            'original_contract_value' => 'required|numeric|min:0',
            'net_amount' => 'required|numeric',
            'tax_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric',
            'fee_percentage' => 'nullable|numeric|min:0|max:100',
            'time_extension_days' => 'nullable|integer|min:0',
            'schedule_impact_description' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.description' => 'required|string',
            'items.*.wbs_id' => 'nullable|exists:project_wbs,id',
            'items.*.original_quantity' => 'required|numeric|min:0',
            'items.*.changed_quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'nullable|string',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $changeOrder = ChangeOrder::create([
                'co_number' => ChangeOrder::generateCoNumber(),
                'created_by' => Auth::id(),
                ...$validated,
            ]);

            // Create items if provided
            if ($request->has('items')) {
                foreach ($request->items as $itemData) {
                    $changeOrder->items()->create($itemData);
                }
            }

            // Handle file uploads
            if ($request->hasFile('attachments')) {
                $attachments = [];
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('change-orders', 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                    ];
                }
                $changeOrder->update(['attachments' => $attachments]);
            }

            DB::commit();

            return redirect()->route('change-orders.show', $changeOrder)
                ->with('success', 'تم إنشاء أمر التغيير بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء أمر التغيير: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified change order.
     */
    public function show(ChangeOrder $changeOrder)
    {
        $changeOrder->load(['project', 'tender', 'contract', 'items.wbs', 'creator', 'pmUser', 'technicalUser', 'consultantUser', 'clientUser']);

        return view('change-orders.show', compact('changeOrder'));
    }

    /**
     * Show the form for editing the specified change order.
     */
    public function edit(ChangeOrder $changeOrder)
    {
        // Only allow editing if in draft status
        if ($changeOrder->status !== 'draft') {
            return back()->with('error', 'لا يمكن تعديل أمر تغيير تم إرساله للموافقة');
        }

        $projects = Project::where('status', 'active')->get();
        $tenders = Tender::all();
        $contracts = Contract::all();

        return view('change-orders.edit', compact('changeOrder', 'projects', 'tenders', 'contracts'));
    }

    /**
     * Update the specified change order in storage.
     */
    public function update(Request $request, ChangeOrder $changeOrder)
    {
        // Only allow updating if in draft status
        if ($changeOrder->status !== 'draft') {
            return back()->with('error', 'لا يمكن تعديل أمر تغيير تم إرساله للموافقة');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'tender_id' => 'nullable|exists:tenders,id',
            'original_contract_id' => 'nullable|exists:contracts,id',
            'issue_date' => 'required|date',
            'type' => 'required|in:scope_change,quantity_change,design_change,specification_change,other',
            'reason' => 'required|in:client_request,design_error,site_condition,regulatory,other',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'justification' => 'nullable|string',
            'original_contract_value' => 'required|numeric|min:0',
            'net_amount' => 'required|numeric',
            'tax_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric',
            'fee_percentage' => 'nullable|numeric|min:0|max:100',
            'time_extension_days' => 'nullable|integer|min:0',
            'schedule_impact_description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $changeOrder->update($validated);

            // Update items if provided
            if ($request->has('items')) {
                // Delete existing items
                $changeOrder->items()->delete();
                
                // Create new items
                foreach ($request->items as $itemData) {
                    $changeOrder->items()->create($itemData);
                }
            }

            DB::commit();

            return redirect()->route('change-orders.show', $changeOrder)
                ->with('success', 'تم تحديث أمر التغيير بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تحديث أمر التغيير: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified change order from storage.
     */
    public function destroy(ChangeOrder $changeOrder)
    {
        // Only allow deletion if in draft status
        if ($changeOrder->status !== 'draft') {
            return back()->with('error', 'لا يمكن حذف أمر تغيير تم إرساله للموافقة');
        }

        $changeOrder->delete();

        return redirect()->route('change-orders.index')
            ->with('success', 'تم حذف أمر التغيير بنجاح');
    }

    /**
     * Show approval form for the specified change order.
     */
    public function approve(ChangeOrder $changeOrder)
    {
        $user = Auth::user();
        
        // Determine user's approval level
        $approvalLevel = null;
        if ($changeOrder->status === 'pending_pm' && $changeOrder->pm_user_id === $user->id) {
            $approvalLevel = 'pm';
        } elseif ($changeOrder->status === 'pending_technical' && $changeOrder->technical_user_id === $user->id) {
            $approvalLevel = 'technical';
        } elseif ($changeOrder->status === 'pending_consultant' && $changeOrder->consultant_user_id === $user->id) {
            $approvalLevel = 'consultant';
        } elseif ($changeOrder->status === 'pending_client' && $changeOrder->client_user_id === $user->id) {
            $approvalLevel = 'client';
        }

        if (!$approvalLevel) {
            return back()->with('error', 'ليس لديك صلاحية للموافقة على هذا الأمر في المرحلة الحالية');
        }

        $changeOrder->load(['project', 'tender', 'contract', 'items.wbs', 'creator']);

        return view('change-orders.approve', compact('changeOrder', 'approvalLevel'));
    }

    /**
     * Process approval decision for the specified change order.
     */
    public function processApproval(Request $request, ChangeOrder $changeOrder)
    {
        $validated = $request->validate([
            'decision' => 'required|in:approved,rejected',
            'comments' => 'required|string',
            'level' => 'required|in:pm,technical,consultant,client',
        ]);

        $user = Auth::user();
        $level = $validated['level'];
        
        // Verify user has permission for this level
        if ($changeOrder->{$level . '_user_id'} !== $user->id) {
            return back()->with('error', 'ليس لديك صلاحية للموافقة على هذا الأمر');
        }

        DB::beginTransaction();
        try {
            // Update decision for this level
            $changeOrder->update([
                $level . '_decision' => $validated['decision'],
                $level . '_signed_at' => now(),
                $level . '_comments' => $validated['comments'],
            ]);

            // Update status based on decision
            if ($validated['decision'] === 'rejected') {
                $changeOrder->update(['status' => 'rejected']);
            } else {
                // Move to next approval level
                $nextStatus = match($level) {
                    'pm' => 'pending_technical',
                    'technical' => 'pending_consultant',
                    'consultant' => 'pending_client',
                    'client' => 'approved',
                };
                $changeOrder->update(['status' => $nextStatus]);
            }

            DB::commit();

            $message = $validated['decision'] === 'approved' 
                ? 'تمت الموافقة بنجاح' 
                : 'تم رفض أمر التغيير';

            return redirect()->route('change-orders.show', $changeOrder)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء معالجة الموافقة: ' . $e->getMessage());
        }
    }

    /**
     * Submit change order for approval.
     */
    public function submit(ChangeOrder $changeOrder)
    {
        if ($changeOrder->status !== 'draft') {
            return back()->with('error', 'تم إرسال أمر التغيير بالفعل');
        }

        // TODO: Assign PM user based on project configuration or user roles
        // This is a placeholder implementation - replace with actual logic
        // Examples:
        // - Get PM from project: $changeOrder->pm_user_id = $changeOrder->project->pm_user_id;
        // - Get PM from role: $changeOrder->pm_user_id = User::role('project-manager')->first()->id;
        // - Get PM from assignment: $changeOrder->pm_user_id = ProjectAssignment::where('project_id', ...)->first()->user_id;
        if (!$changeOrder->pm_user_id) {
            // Temporary fallback: use current user (should be replaced)
            $changeOrder->pm_user_id = Auth::id();
        }

        $changeOrder->update(['status' => 'pending_pm']);

        return redirect()->route('change-orders.show', $changeOrder)
            ->with('success', 'تم إرسال أمر التغيير للموافقة');
    }

    /**
     * Generate report view.
     */
    public function report()
    {
        $changeOrders = ChangeOrder::with(['project', 'tender', 'contract'])->get();

        // Statistics by type
        $byType = ChangeOrder::select('type', DB::raw('count(*) as count'), DB::raw('sum(total_amount) as total'))
            ->groupBy('type')
            ->get();

        // Statistics by status
        $byStatus = ChangeOrder::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Monthly trend - Database agnostic using Laravel's date functions
        $monthlyTrend = ChangeOrder::selectRaw('extract(year from issue_date) as year, extract(month from issue_date) as month, count(*) as count, sum(total_amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                $item->month = sprintf('%04d-%02d', $item->year, $item->month);
                return $item;
            });

        return view('change-orders.report', compact('changeOrders', 'byType', 'byStatus', 'monthlyTrend'));
    }

    /**
     * Export change order to PDF.
     */
    public function exportPdf(ChangeOrder $changeOrder)
    {
        $changeOrder->load(['project', 'tender', 'contract', 'items.wbs', 'creator', 'pmUser', 'technicalUser', 'consultantUser', 'clientUser']);

        $pdf = Pdf::loadView('change-orders.pdf', compact('changeOrder'));
        
        return $pdf->download('change-order-' . $changeOrder->co_number . '.pdf');
    }

    /**
     * Get WBS items for a project (AJAX).
     */
    public function getProjectWbs(Project $project)
    {
        $wbs = ProjectWbs::where('project_id', $project->id)->get();
        return response()->json($wbs);
    }

    /**
     * Get contract details (AJAX).
     */
    public function getContractDetails(Contract $contract)
    {
        return response()->json([
            'contract_value' => $contract->contract_value,
            'start_date' => $contract->start_date,
            'end_date' => $contract->end_date,
        ]);
    }
}
