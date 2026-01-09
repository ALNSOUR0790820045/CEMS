<?php

namespace App\Http\Controllers;

use App\Models\MainIpc;
use App\Models\MainIpcItem;
use App\Models\Project;
use App\Models\BoqItem;
use App\Models\ChangeOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainIpcController extends Controller
{
    public function index(Request $request)
    {
        $query = MainIpc::with(['project', 'pmPreparer']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('date_from')) {
            $query->where('submission_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('submission_date', '<=', $request->date_to);
        }

        if ($request->boolean('overdue')) {
            $query->overdue();
        }

        $ipcs = $query->latest()->paginate(15);

        // Statistics
        $statistics = [
            'total_count' => MainIpc::count(),
            'pending_count' => MainIpc::pending()->count(),
            'approved_count' => MainIpc::approved()->count(),
            'paid_count' => MainIpc::where('status', 'paid')->count(),
            'total_value' => MainIpc::sum('current_cumulative'),
            'paid_value' => MainIpc::where('status', 'paid')->sum('paid_amount'),
            'overdue_count' => MainIpc::overdue()->count(),
        ];

        $projects = Project::where('status', 'active')->get();

        return view('main-ipcs.index', compact('ipcs', 'statistics', 'projects'));
    }

    public function create()
    {
        $projects = Project::where('status', 'active')->get();
        return view('main-ipcs.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'period_from' => 'required|date',
            'period_to' => 'required|date|after:period_from',
            'submission_date' => 'required|date',
            'retention_percent' => 'required|numeric|min:0|max:100',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'advance_payment_deduction' => 'nullable|numeric|min:0',
            'other_deductions' => 'nullable|numeric|min:0',
            'deductions_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.boq_item_id' => 'required|exists:boq_items,id',
            'items.*.current_quantity' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $project = Project::findOrFail($validated['project_id']);
            
            // Get last IPC sequence
            $lastIpc = MainIpc::where('project_id', $project->id)
                ->orderBy('ipc_sequence', 'desc')
                ->first();
            
            $sequence = $lastIpc ? $lastIpc->ipc_sequence + 1 : 1;
            
            // Generate IPC number
            $ipcNumber = 'IPC-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
            
            // Get previous cumulative
            $previousCumulative = $lastIpc ? $lastIpc->current_cumulative : 0;
            
            // Get approved change orders
            $approvedChangeOrders = ChangeOrder::where('project_id', $project->id)
                ->where('status', 'approved')
                ->sum('amount');
            
            // Create IPC
            $ipc = MainIpc::create([
                'project_id' => $project->id,
                'ipc_number' => $ipcNumber,
                'ipc_sequence' => $sequence,
                'period_from' => $validated['period_from'],
                'period_to' => $validated['period_to'],
                'submission_date' => $validated['submission_date'],
                'previous_cumulative' => $previousCumulative,
                'approved_change_orders' => $approvedChangeOrders,
                'retention_percent' => $validated['retention_percent'],
                'tax_rate' => $validated['tax_rate'],
                'advance_payment_deduction' => $validated['advance_payment_deduction'] ?? 0,
                'other_deductions' => $validated['other_deductions'] ?? 0,
                'deductions_notes' => $validated['deductions_notes'],
                'status' => 'draft',
            ]);

            // Calculate current period work from items
            $currentPeriodWork = 0;

            // Create IPC items
            foreach ($validated['items'] as $itemData) {
                $boqItem = BoqItem::findOrFail($itemData['boq_item_id']);
                
                // Get previous quantity from last IPC
                $previousQuantity = 0;
                if ($lastIpc) {
                    $lastIpcItem = MainIpcItem::where('main_ipc_id', $lastIpc->id)
                        ->where('boq_item_id', $boqItem->id)
                        ->first();
                    if ($lastIpcItem) {
                        $previousQuantity = $lastIpcItem->cumulative_quantity;
                    }
                }
                
                $ipcItem = new MainIpcItem([
                    'main_ipc_id' => $ipc->id,
                    'boq_item_id' => $boqItem->id,
                    'wbs_id' => $boqItem->wbs_id,
                    'item_code' => $boqItem->item_code,
                    'description' => $boqItem->description,
                    'unit' => $boqItem->unit,
                    'contract_quantity' => $boqItem->quantity,
                    'previous_quantity' => $previousQuantity,
                    'current_quantity' => $itemData['current_quantity'],
                    'unit_price' => $boqItem->unit_price,
                    'notes' => $itemData['notes'] ?? null,
                ]);
                
                $ipcItem->recalculate();
                $ipcItem->save();
                
                $currentPeriodWork += $ipcItem->current_amount;
            }

            // Update IPC with current period work and recalculate
            $ipc->current_period_work = $currentPeriodWork;
            $ipc->recalculate();
            $ipc->save();

            DB::commit();

            return redirect()->route('main-ipcs.show', $ipc)
                ->with('success', 'تم إنشاء المستخلص بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء المستخلص: ' . $e->getMessage());
        }
    }

    public function show(MainIpc $mainIpc)
    {
        $mainIpc->load([
            'project',
            'items.boqItem',
            'items.wbs',
            'pmPreparer',
            'technicalReviewer',
            'consultantReviewer',
            'clientApprover',
            'financeReviewer',
            'paidByUser'
        ]);

        return view('main-ipcs.show', compact('mainIpc'));
    }

    public function submitForApproval(MainIpc $mainIpc)
    {
        if ($mainIpc->status !== 'draft') {
            return back()->with('error', 'لا يمكن إرسال هذا المستخلص للموافقة');
        }

        $mainIpc->update([
            'status' => 'pending_pm',
            'pm_prepared_by' => auth()->id(),
            'pm_prepared_at' => now(),
        ]);

        return redirect()->route('main-ipcs.show', $mainIpc)
            ->with('success', 'تم إرسال المستخلص للموافقة');
    }

    public function approve(MainIpc $mainIpc)
    {
        return view('main-ipcs.approve', compact('mainIpc'));
    }

    public function processApproval(Request $request, MainIpc $mainIpc)
    {
        $validated = $request->validate([
            'decision' => 'required|in:approved,rejected,revision_required',
            'comments' => 'required|string',
            'approved_amount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $status = $mainIpc->status;
            $decision = $validated['decision'];

            if ($status === 'pending_pm' || $status === 'pending_technical') {
                // Technical review
                $mainIpc->update([
                    'technical_reviewed_by' => auth()->id(),
                    'technical_reviewed_at' => now(),
                    'technical_decision' => $decision,
                    'technical_comments' => $validated['comments'],
                    'status' => $decision === 'approved' ? 'pending_consultant' : ($decision === 'rejected' ? 'rejected' : 'draft'),
                ]);

                // If approved, set consultant dates
                if ($decision === 'approved') {
                    $mainIpc->update([
                        'consultant_submission_date' => now()->toDateString(),
                        'consultant_due_date' => now()->addDays(14)->toDateString(),
                    ]);
                }
            } elseif ($status === 'pending_consultant') {
                // Consultant review
                $reviewDays = now()->diffInDays($mainIpc->consultant_submission_date);
                
                $mainIpc->update([
                    'consultant_reviewed_by' => auth()->id(),
                    'consultant_reviewed_at' => now(),
                    'consultant_decision' => $decision,
                    'consultant_comments' => $validated['comments'],
                    'consultant_approved_amount' => $validated['approved_amount'] ?? $mainIpc->net_payable,
                    'consultant_review_days' => $reviewDays,
                    'status' => $decision === 'approved' ? 'pending_client' : ($decision === 'rejected' ? 'rejected' : 'draft'),
                ]);

                // If approved, set client dates
                if ($decision === 'approved') {
                    $mainIpc->update([
                        'client_submission_date' => now()->toDateString(),
                        'client_due_date' => now()->addDays(21)->toDateString(),
                    ]);
                }
            } elseif ($status === 'pending_client') {
                // Client approval
                $reviewDays = now()->diffInDays($mainIpc->client_submission_date);
                
                $mainIpc->update([
                    'client_approved_by' => auth()->id(),
                    'client_approved_at' => now(),
                    'client_decision' => $decision,
                    'client_comments' => $validated['comments'],
                    'client_approved_amount' => $validated['approved_amount'] ?? $mainIpc->net_payable,
                    'client_review_days' => $reviewDays,
                    'status' => $decision === 'approved' ? 'pending_finance' : ($decision === 'rejected' ? 'rejected' : 'draft'),
                ]);
            } elseif ($status === 'pending_finance') {
                // Finance review
                $mainIpc->update([
                    'finance_reviewed_by' => auth()->id(),
                    'finance_reviewed_at' => now(),
                    'finance_decision' => $decision === 'approved' ? 'approved' : 'on_hold',
                    'finance_comments' => $validated['comments'],
                    'status' => $decision === 'approved' ? 'approved_for_payment' : 'on_hold',
                ]);
            }

            DB::commit();

            return redirect()->route('main-ipcs.show', $mainIpc)
                ->with('success', 'تم حفظ القرار بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function payment(MainIpc $mainIpc)
    {
        if ($mainIpc->status !== 'approved_for_payment') {
            return back()->with('error', 'هذا المستخلص غير جاهز للدفع');
        }

        return view('main-ipcs.payment', compact('mainIpc'));
    }

    public function processPayment(Request $request, MainIpc $mainIpc)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'payment_reference' => 'required|string',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $mainIpc->update([
            'paid_by' => auth()->id(),
            'payment_date' => $validated['payment_date'],
            'payment_reference' => $validated['payment_reference'],
            'paid_amount' => $validated['paid_amount'],
            'status' => 'paid',
        ]);

        return redirect()->route('main-ipcs.show', $mainIpc)
            ->with('success', 'تم تسجيل الدفع بنجاح');
    }

    public function report()
    {
        $statistics = [
            'total_count' => MainIpc::count(),
            'total_value' => MainIpc::sum('current_cumulative'),
            'pending_count' => MainIpc::pending()->count(),
            'pending_value' => MainIpc::pending()->sum('net_payable'),
            'approved_count' => MainIpc::where('status', 'approved_for_payment')->count(),
            'approved_value' => MainIpc::where('status', 'approved_for_payment')->sum('net_payable'),
            'paid_count' => MainIpc::where('status', 'paid')->count(),
            'paid_value' => MainIpc::where('status', 'paid')->sum('paid_amount'),
            'retention_total' => MainIpc::sum('retention_amount'),
        ];

        $ipcs = MainIpc::with(['project', 'pmPreparer'])->latest()->get();

        // Calculate average approval times
        $avgConsultantDays = MainIpc::whereNotNull('consultant_review_days')->avg('consultant_review_days');
        $avgClientDays = MainIpc::whereNotNull('client_review_days')->avg('client_review_days');

        return view('main-ipcs.report', compact('statistics', 'ipcs', 'avgConsultantDays', 'avgClientDays'));
    }

    public function getBoqItems(Request $request)
    {
        $projectId = $request->get('project_id');
        
        if (!$projectId) {
            return response()->json([]);
        }

        $boqItems = BoqItem::where('project_id', $projectId)
            ->with('wbs')
            ->orderBy('item_code')
            ->get();

        // Get last IPC for previous quantities
        $lastIpc = MainIpc::where('project_id', $projectId)
            ->orderBy('ipc_sequence', 'desc')
            ->first();

        $items = $boqItems->map(function ($boqItem) use ($lastIpc) {
            $previousQuantity = 0;
            if ($lastIpc) {
                $lastIpcItem = MainIpcItem::where('main_ipc_id', $lastIpc->id)
                    ->where('boq_item_id', $boqItem->id)
                    ->first();
                if ($lastIpcItem) {
                    $previousQuantity = $lastIpcItem->cumulative_quantity;
                }
            }

            return [
                'id' => $boqItem->id,
                'item_code' => $boqItem->item_code,
                'description' => $boqItem->description,
                'unit' => $boqItem->unit,
                'contract_quantity' => $boqItem->quantity,
                'previous_quantity' => $previousQuantity,
                'unit_price' => $boqItem->unit_price,
                'wbs_name' => $boqItem->wbs ? $boqItem->wbs->name : null,
            ];
        });

        return response()->json($items);
    }
}
