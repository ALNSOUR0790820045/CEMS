<?php

namespace App\Http\Controllers;

use App\Models\ProgressBill;
use App\Models\ProgressBillItem;
use App\Models\Project;
use App\Models\BoqItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProgressBillController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ProgressBill::with([
            'project',
            'contract',
            'currency',
            'preparedBy',
        ]);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $bills = $query->orderBy('bill_date', 'desc')->paginate(15);

        return response()->json($bills);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'contract_id' => 'required|exists:contracts,id',
            'period_from' => 'required|date',
            'period_to' => 'required|date|after:period_from',
            'bill_date' => 'required|date',
            'bill_type' => 'required|in:interim,final,retention_release',
            'currency_id' => 'required|exists:currencies,id',
            'retention_percentage' => 'nullable|numeric|min:0|max:100',
            'advance_recovery_percentage' => 'nullable|numeric|min:0|max:100',
            'vat_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Get previous bill
            $previousBill = ProgressBill::where('project_id', $validated['project_id'])
                ->where('contract_id', $validated['contract_id'])
                ->orderBy('bill_sequence', 'desc')
                ->first();

            $bill = new ProgressBill($validated);
            $bill->bill_sequence = $previousBill ? $previousBill->bill_sequence + 1 : 1;
            $bill->previous_bill_id = $previousBill?->id;
            $bill->bill_number = $bill->generateBillNumber();
            $bill->company_id = Auth::user()->company_id;
            $bill->prepared_by_id = Auth::id();
            $bill->status = 'draft';
            $bill->save();

            DB::commit();
            return response()->json($bill->load('project', 'contract'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create progress bill', 'message' => $e->getMessage()], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $bill = ProgressBill::with([
            'project',
            'contract',
            'currency',
            'previousBill',
            'items.boqItem',
            'items.unit',
            'variations',
            'deductions',
            'attachments',
            'measurementSheets',
            'approvalWorkflow.approver',
            'preparedBy',
            'reviewedBy',
            'certifiedBy',
            'approvedBy',
        ])->findOrFail($id);

        return response()->json($bill);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $bill = ProgressBill::findOrFail($id);

        if (!in_array($bill->status, ['draft', 'rejected'])) {
            return response()->json(['error' => 'Cannot update bill in current status'], 403);
        }

        $validated = $request->validate([
            'period_from' => 'sometimes|date',
            'period_to' => 'sometimes|date',
            'bill_date' => 'sometimes|date',
            'retention_percentage' => 'nullable|numeric|min:0|max:100',
            'advance_recovery_percentage' => 'nullable|numeric|min:0|max:100',
            'vat_percentage' => 'nullable|numeric|min:0|max:100',
            'other_deductions' => 'nullable|numeric|min:0',
            'deduction_remarks' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $bill->update($validated);
        return response()->json($bill->load('project', 'contract'));
    }

    public function destroy(int $id): JsonResponse
    {
        $bill = ProgressBill::findOrFail($id);

        if ($bill->status !== 'draft') {
            return response()->json(['error' => 'Cannot delete bill that is not in draft status'], 403);
        }

        $bill->delete();
        return response()->json(['message' => 'Progress bill deleted successfully']);
    }

    public function byProject(int $projectId): JsonResponse
    {
        $bills = ProgressBill::where('project_id', $projectId)
            ->with(['contract', 'currency', 'preparedBy'])
            ->orderBy('bill_sequence', 'desc')
            ->get();

        return response()->json($bills);
    }

    public function preview(int $id): JsonResponse
    {
        $bill = ProgressBill::with([
            'project',
            'contract',
            'currency',
            'items.boqItem',
            'items.unit',
            'variations',
            'deductions',
        ])->findOrFail($id);

        return response()->json($bill);
    }

    public function submit(Request $request, int $id): JsonResponse
    {
        $bill = ProgressBill::findOrFail($id);

        if ($bill->status !== 'draft') {
            return response()->json(['error' => 'Bill must be in draft status to submit'], 403);
        }

        // Validate that bill has items
        if ($bill->items()->count() === 0) {
            return response()->json(['error' => 'Bill must have at least one item'], 422);
        }

        DB::beginTransaction();
        try {
            $bill->status = 'submitted';
            $bill->submitted_at = now();
            $bill->save();

            DB::commit();
            return response()->json($bill);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to submit bill', 'message' => $e->getMessage()], 500);
        }
    }

    public function review(Request $request, int $id): JsonResponse
    {
        $bill = ProgressBill::findOrFail($id);

        if ($bill->status !== 'submitted') {
            return response()->json(['error' => 'Bill must be submitted to review'], 403);
        }

        $bill->status = 'reviewed';
        $bill->reviewed_by_id = Auth::id();
        $bill->save();

        return response()->json($bill);
    }

    public function certify(Request $request, int $id): JsonResponse
    {
        $bill = ProgressBill::findOrFail($id);

        if ($bill->status !== 'reviewed') {
            return response()->json(['error' => 'Bill must be reviewed to certify'], 403);
        }

        $bill->status = 'certified';
        $bill->certified_by_id = Auth::id();
        $bill->certified_at = now();
        $bill->save();

        return response()->json($bill);
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $bill = ProgressBill::findOrFail($id);

        if ($bill->status !== 'certified') {
            return response()->json(['error' => 'Bill must be certified to approve'], 403);
        }

        $bill->status = 'approved';
        $bill->approved_by_id = Auth::id();
        $bill->approved_at = now();
        $bill->save();

        return response()->json($bill);
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $bill = ProgressBill::findOrFail($id);

        if (!in_array($bill->status, ['submitted', 'reviewed', 'certified'])) {
            return response()->json(['error' => 'Cannot reject bill in current status'], 403);
        }

        $bill->status = 'rejected';
        $bill->rejection_reason = $validated['rejection_reason'];
        $bill->save();

        return response()->json($bill);
    }

    public function markPaid(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'payment_reference' => 'required|string',
        ]);

        $bill = ProgressBill::findOrFail($id);

        if ($bill->status !== 'approved') {
            return response()->json(['error' => 'Bill must be approved to mark as paid'], 403);
        }

        $bill->status = 'paid';
        $bill->payment_reference = $validated['payment_reference'];
        $bill->paid_at = now();
        $bill->save();

        return response()->json($bill);
    }

    public function createNext(int $projectId): JsonResponse
    {
        $lastBill = ProgressBill::where('project_id', $projectId)
            ->orderBy('bill_sequence', 'desc')
            ->first();

        if (!$lastBill) {
            return response()->json(['error' => 'No previous bill found for this project'], 404);
        }

        if (!in_array($lastBill->status, ['approved', 'paid'])) {
            return response()->json(['error' => 'Previous bill must be approved or paid'], 403);
        }

        DB::beginTransaction();
        try {
            $bill = new ProgressBill([
                'project_id' => $projectId,
                'contract_id' => $lastBill->contract_id,
                'bill_type' => 'interim',
                'currency_id' => $lastBill->currency_id,
                'retention_percentage' => $lastBill->retention_percentage,
                'advance_recovery_percentage' => $lastBill->advance_recovery_percentage,
                'vat_percentage' => $lastBill->vat_percentage,
                'company_id' => $lastBill->company_id,
            ]);
            
            $bill->bill_sequence = $lastBill->bill_sequence + 1;
            $bill->previous_bill_id = $lastBill->id;
            $bill->bill_number = $bill->generateBillNumber();
            $bill->prepared_by_id = Auth::id();
            $bill->status = 'draft';
            $bill->save();

            DB::commit();
            return response()->json($bill->load('project', 'contract'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create next bill', 'message' => $e->getMessage()], 500);
        }
    }

    public function getItems(int $id): JsonResponse
    {
        $bill = ProgressBill::findOrFail($id);
        $items = $bill->items()->with('boqItem', 'unit')->get();
        
        return response()->json($items);
    }

    public function updateItems(Request $request, int $id): JsonResponse
    {
        $bill = ProgressBill::findOrFail($id);

        if (!in_array($bill->status, ['draft', 'rejected'])) {
            return response()->json(['error' => 'Cannot update items for bill in current status'], 403);
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.boq_item_id' => 'required|exists:boq_items,id',
            'items.*.current_quantity' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['items'] as $itemData) {
                $boqItem = BoqItem::find($itemData['boq_item_id']);
                
                // Get previous quantity
                $previousQuantity = 0;
                if ($bill->previous_bill_id) {
                    $previousItem = ProgressBillItem::where('progress_bill_id', $bill->previous_bill_id)
                        ->where('boq_item_id', $itemData['boq_item_id'])
                        ->first();
                    $previousQuantity = $previousItem ? $previousItem->cumulative_quantity : 0;
                }

                $item = ProgressBillItem::updateOrCreate(
                    [
                        'progress_bill_id' => $bill->id,
                        'boq_item_id' => $itemData['boq_item_id'],
                    ],
                    [
                        'item_code' => $boqItem->item_number,
                        'description' => $boqItem->description,
                        'unit_id' => $boqItem->unit_id ?? null,
                        'contract_quantity' => $boqItem->quantity,
                        'contract_rate' => $boqItem->unit_rate,
                        'contract_amount' => $boqItem->amount,
                        'previous_quantity' => $previousQuantity,
                        'current_quantity' => $itemData['current_quantity'],
                    ]
                );

                $item->calculateAmounts();
            }

            // Recalculate bill amounts
            $bill->calculateAmounts();

            DB::commit();
            return response()->json($bill->load('items'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update items', 'message' => $e->getMessage()], 500);
        }
    }

    public function importFromBoq(Request $request, int $id): JsonResponse
    {
        $bill = ProgressBill::findOrFail($id);

        if (!in_array($bill->status, ['draft', 'rejected'])) {
            return response()->json(['error' => 'Cannot import items for bill in current status'], 403);
        }

        DB::beginTransaction();
        try {
            // Get BOQ items for the project
            $boqItems = BoqItem::where('boq_header_id', function($query) use ($bill) {
                $query->select('id')
                    ->from('boq_headers')
                    ->where('project_id', $bill->project_id)
                    ->orderBy('created_at', 'desc')
                    ->limit(1);
            })->get();

            foreach ($boqItems as $boqItem) {
                // Get previous quantity
                $previousQuantity = 0;
                if ($bill->previous_bill_id) {
                    $previousItem = ProgressBillItem::where('progress_bill_id', $bill->previous_bill_id)
                        ->where('boq_item_id', $boqItem->id)
                        ->first();
                    $previousQuantity = $previousItem ? $previousItem->cumulative_quantity : 0;
                }

                ProgressBillItem::create([
                    'progress_bill_id' => $bill->id,
                    'boq_item_id' => $boqItem->id,
                    'item_code' => $boqItem->item_number,
                    'description' => $boqItem->description,
                    'unit_id' => null,
                    'contract_quantity' => $boqItem->quantity,
                    'contract_rate' => $boqItem->unit_rate,
                    'contract_amount' => $boqItem->amount,
                    'previous_quantity' => $previousQuantity,
                    'current_quantity' => 0,
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Items imported successfully', 'count' => $boqItems->count()]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to import items', 'message' => $e->getMessage()], 500);
        }
    }

    public function calculateItems(int $id): JsonResponse
    {
        $bill = ProgressBill::findOrFail($id);

        DB::beginTransaction();
        try {
            foreach ($bill->items as $item) {
                $item->calculateAmounts();
            }

            $bill->calculateAmounts();

            DB::commit();
            return response()->json($bill->load('items'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to calculate items', 'message' => $e->getMessage()], 500);
        }
    }

    public function getVariations(int $id): JsonResponse
    {
        $bill = ProgressBill::findOrFail($id);
        $variations = $bill->variations()->with('variationOrder', 'unit')->get();
        
        return response()->json($variations);
    }

    public function addVariation(Request $request, int $id): JsonResponse
    {
        $bill = ProgressBill::findOrFail($id);

        if (!in_array($bill->status, ['draft', 'rejected'])) {
            return response()->json(['error' => 'Cannot add variations for bill in current status'], 403);
        }

        $validated = $request->validate([
            'variation_order_id' => 'nullable|exists:variation_orders,id',
            'description' => 'required|string',
            'quantity' => 'required|numeric|min:0',
            'unit_id' => 'nullable|exists:units,id',
            'rate' => 'required|numeric|min:0',
            'current_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,approved,rejected',
            'remarks' => 'nullable|string',
        ]);

        $variation = $bill->variations()->create($validated);

        return response()->json($variation, 201);
    }

    public function getDeductions(int $id): JsonResponse
    {
        $bill = ProgressBill::findOrFail($id);
        $deductions = $bill->deductions()->get();
        
        return response()->json($deductions);
    }

    public function addDeduction(Request $request, int $id): JsonResponse
    {
        $bill = ProgressBill::findOrFail($id);

        if (!in_array($bill->status, ['draft', 'rejected'])) {
            return response()->json(['error' => 'Cannot add deductions for bill in current status'], 403);
        }

        $validated = $request->validate([
            'deduction_type' => 'required|in:retention,advance_recovery,penalty,defects,materials,other',
            'description' => 'required|string',
            'calculation_basis' => 'required|in:percentage,fixed',
            'percentage' => 'required_if:calculation_basis,percentage|nullable|numeric|min:0|max:100',
            'base_amount' => 'required_if:calculation_basis,percentage|nullable|numeric|min:0',
            'amount' => 'required_if:calculation_basis,fixed|nullable|numeric|min:0',
            'reference' => 'nullable|string',
        ]);

        $deduction = $bill->deductions()->create($validated);
        
        if ($deduction->calculation_basis === 'percentage') {
            $deduction->calculateAmount();
        }

        return response()->json($deduction, 201);
    }

    public function calculateDeductions(int $id): JsonResponse
    {
        $bill = ProgressBill::findOrFail($id);

        DB::beginTransaction();
        try {
            foreach ($bill->deductions as $deduction) {
                $deduction->calculateAmount();
            }

            $bill->calculateAmounts();

            DB::commit();
            return response()->json($bill->load('deductions'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to calculate deductions', 'message' => $e->getMessage()], 500);
        }
    }
}
