<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\ClaimTimeline;
use App\Models\Contract;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClaimController extends Controller
{
    public function index()
    {
        $claims = Claim::with(['project', 'contract', 'preparedBy'])
            ->latest()
            ->paginate(20);

        return view('claims.index', compact('claims'));
    }

    public function create()
    {
        $projects = Project::where('status', 'active')->get();
        $contracts = Contract::where('status', 'active')->get();

        return view('claims.create', compact('projects', 'contracts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'contractual_basis' => 'nullable|string',
            'facts' => 'nullable|string',
            'type' => 'required|in:time_extension,cost_compensation,time_and_cost,acceleration,disruption,prolongation,loss_of_productivity',
            'cause' => 'required|in:client_delay,design_changes,differing_conditions,force_majeure,suspension,late_payment,acceleration_order,other',
            'claimed_amount' => 'required|numeric|min:0',
            'claimed_days' => 'required|integer|min:0',
            'currency' => 'required|string|max:3',
            'event_start_date' => 'required|date',
            'event_end_date' => 'nullable|date|after_or_equal:event_start_date',
            'notice_date' => 'required|date',
            'priority' => 'required|in:low,medium,high,critical',
        ]);

        // Generate claim number
        $project = Project::findOrFail($validated['project_id']);
        $sequenceNumber = Claim::where('project_id', $project->id)->max('sequence_number') + 1;
        $validated['sequence_number'] = $sequenceNumber;
        $validated['claim_number'] = 'CLM-'.$project->code.'-'.str_pad($sequenceNumber, 3, '0', STR_PAD_LEFT);
        $validated['prepared_by'] = Auth::id();
        $validated['status'] = 'identified';

        DB::beginTransaction();
        try {
            $claim = Claim::create($validated);

            // Create timeline entry
            ClaimTimeline::create([
                'claim_id' => $claim->id,
                'action' => 'تم إنشاء المطالبة',
                'to_status' => 'identified',
                'performed_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('claims.show', $claim)
                ->with('success', 'تم إنشاء المطالبة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء المطالبة');
        }
    }

    public function show(Claim $claim)
    {
        $claim->load([
            'project',
            'contract',
            'preparedBy',
            'reviewedBy',
            'events',
            'documents.uploadedBy',
            'timeline.performedBy',
            'correspondence',
        ]);

        return view('claims.show', compact('claim'));
    }

    public function edit(Claim $claim)
    {
        $projects = Project::all();
        $contracts = Contract::all();

        return view('claims.edit', compact('claim', 'projects', 'contracts'));
    }

    public function update(Request $request, Claim $claim)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'contractual_basis' => 'nullable|string',
            'facts' => 'nullable|string',
            'type' => 'required|in:time_extension,cost_compensation,time_and_cost,acceleration,disruption,prolongation,loss_of_productivity',
            'cause' => 'required|in:client_delay,design_changes,differing_conditions,force_majeure,suspension,late_payment,acceleration_order,other',
            'claimed_amount' => 'required|numeric|min:0',
            'claimed_days' => 'required|integer|min:0',
            'assessed_amount' => 'nullable|numeric|min:0',
            'assessed_days' => 'nullable|integer|min:0',
            'approved_amount' => 'nullable|numeric|min:0',
            'approved_days' => 'nullable|integer|min:0',
            'currency' => 'required|string|max:3',
            'event_start_date' => 'required|date',
            'event_end_date' => 'nullable|date|after_or_equal:event_start_date',
            'notice_date' => 'required|date',
            'submission_date' => 'nullable|date',
            'response_due_date' => 'nullable|date',
            'response_date' => 'nullable|date',
            'resolution_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high,critical',
            'client_response' => 'nullable|string',
            'resolution_notes' => 'nullable|string',
            'lessons_learned' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $claim->status;
            $claim->update($validated);

            if ($oldStatus !== $claim->status) {
                ClaimTimeline::create([
                    'claim_id' => $claim->id,
                    'action' => 'تم تحديث المطالبة',
                    'from_status' => $oldStatus,
                    'to_status' => $claim->status,
                    'performed_by' => Auth::id(),
                ]);
            }

            DB::commit();

            return redirect()->route('claims.show', $claim)
                ->with('success', 'تم تحديث المطالبة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث المطالبة');
        }
    }

    public function destroy(Claim $claim)
    {
        $claim->delete();

        return redirect()->route('claims.index')
            ->with('success', 'تم حذف المطالبة بنجاح');
    }

    public function sendNotice(Request $request, Claim $claim)
    {
        $validated = $request->validate([
            'notice_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $claim->update([
                'status' => 'notice_sent',
                'notice_date' => $validated['notice_date'],
            ]);

            ClaimTimeline::create([
                'claim_id' => $claim->id,
                'action' => 'تم إرسال الإشعار',
                'from_status' => $claim->status,
                'to_status' => 'notice_sent',
                'notes' => 'تم إرسال إشعار المطالبة',
                'performed_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال الإشعار بنجاح',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الإشعار',
            ], 500);
        }
    }

    public function submit(Request $request, Claim $claim)
    {
        $validated = $request->validate([
            'submission_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $claim->update([
                'status' => 'submitted',
                'submission_date' => $validated['submission_date'],
            ]);

            ClaimTimeline::create([
                'claim_id' => $claim->id,
                'action' => 'تم تقديم المطالبة',
                'from_status' => $claim->status,
                'to_status' => 'submitted',
                'notes' => 'تم تقديم المطالبة رسمياً',
                'performed_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تقديم المطالبة بنجاح',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تقديم المطالبة',
            ], 500);
        }
    }

    public function resolve(Request $request, Claim $claim)
    {
        $validated = $request->validate([
            'resolution_date' => 'required|date',
            'approved_amount' => 'required|numeric|min:0',
            'approved_days' => 'required|integer|min:0',
            'resolution_notes' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $validated['status'] = 'settled';
            $claim->update($validated);

            ClaimTimeline::create([
                'claim_id' => $claim->id,
                'action' => 'تمت تسوية المطالبة',
                'from_status' => $claim->status,
                'to_status' => 'settled',
                'notes' => $validated['resolution_notes'],
                'performed_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تمت تسوية المطالبة بنجاح',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسوية المطالبة',
            ], 500);
        }
    }

    public function projectClaims($projectId)
    {
        $project = Project::findOrFail($projectId);
        $claims = Claim::where('project_id', $projectId)
            ->with(['contract', 'preparedBy'])
            ->latest()
            ->get();

        return response()->json([
            'project' => $project,
            'claims' => $claims,
        ]);
    }

    public function statistics()
    {
        $stats = [
            'total' => Claim::count(),
            'by_status' => Claim::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'by_type' => Claim::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type'),
            'total_claimed' => Claim::sum('claimed_amount'),
            'total_approved' => Claim::sum('approved_amount'),
            'average_days' => Claim::avg('claimed_days'),
        ];

        return response()->json($stats);
    }

    public function export(Claim $claim)
    {
        $claim->load([
            'project',
            'contract',
            'preparedBy',
            'reviewedBy',
            'events',
            'documents',
            'timeline.performedBy',
            'correspondence',
        ]);

        $pdf = Pdf::loadView('claims.report', compact('claim'));

        return $pdf->download('claim-'.$claim->claim_number.'.pdf');
    }
}
