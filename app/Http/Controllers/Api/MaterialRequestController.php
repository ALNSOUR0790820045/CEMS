<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MaterialRequest::with(['requestedBy', 'department', 'project', 'items.material', 'approvedBy']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('request_type')) {
            $query->where('request_type', $request->request_type);
        }

        $materialRequests = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($materialRequests);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_date' => 'required|date',
            'required_date' => 'required|date|after_or_equal:request_date',
            'requested_by_id' => 'required|exists:employees,id',
            'department_id' => 'nullable|exists:departments,id',
            'project_id' => 'nullable|exists:projects,id',
            'request_type' => 'required|in:from_warehouse,for_purchase',
            'priority' => 'nullable|in:normal,urgent,critical',
            'notes' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.requested_quantity' => 'required|numeric|min:0.01',
            'items.*.purpose' => 'nullable|string',
            'items.*.specifications' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $materialRequest = MaterialRequest::create($validated);

            foreach ($validated['items'] as $item) {
                $materialRequest->items()->create($item);
            }

            DB::commit();

            return response()->json([
                'message' => 'Material request created successfully',
                'data' => $materialRequest->load(['items.material', 'requestedBy', 'department', 'project']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create material request'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MaterialRequest $materialRequest)
    {
        $materialRequest->load(['requestedBy', 'department', 'project', 'items.material', 'approvedBy']);
        return response()->json($materialRequest);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaterialRequest $materialRequest)
    {
        if (!in_array($materialRequest->status, ['draft', 'submitted'])) {
            return response()->json(['error' => 'Cannot update approved or fulfilled requests'], 422);
        }

        $validated = $request->validate([
            'request_date' => 'required|date',
            'required_date' => 'required|date|after_or_equal:request_date',
            'requested_by_id' => 'required|exists:employees,id',
            'department_id' => 'nullable|exists:departments,id',
            'project_id' => 'nullable|exists:projects,id',
            'request_type' => 'required|in:from_warehouse,for_purchase',
            'priority' => 'nullable|in:normal,urgent,critical',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.requested_quantity' => 'required|numeric|min:0.01',
            'items.*.purpose' => 'nullable|string',
            'items.*.specifications' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $materialRequest->update($validated);
            
            $materialRequest->items()->delete();
            foreach ($validated['items'] as $item) {
                $materialRequest->items()->create($item);
            }

            DB::commit();

            return response()->json([
                'message' => 'Material request updated successfully',
                'data' => $materialRequest->load(['items.material', 'requestedBy', 'department', 'project']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update material request'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaterialRequest $materialRequest)
    {
        if ($materialRequest->status !== 'draft') {
            return response()->json(['error' => 'Only draft requests can be deleted'], 422);
        }

        $materialRequest->delete();

        return response()->json(['message' => 'Material request deleted successfully']);
    }

    /**
     * Approve a material request.
     */
    public function approve(Request $request, MaterialRequest $materialRequest)
    {
        if ($materialRequest->status !== 'submitted') {
            return response()->json(['error' => 'Only submitted requests can be approved'], 422);
        }

        $materialRequest->approve($request->user()->id);

        return response()->json([
            'message' => 'Material request approved successfully',
            'data' => $materialRequest->load(['items.material', 'requestedBy', 'department', 'project', 'approvedBy']),
        ]);
    }

    /**
     * Issue materials from warehouse.
     */
    public function issue(Request $request, MaterialRequest $materialRequest)
    {
        if ($materialRequest->status !== 'approved') {
            return response()->json(['error' => 'Only approved requests can be issued'], 422);
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:material_request_items,id',
            'items.*.issued_quantity' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['items'] as $itemData) {
                $item = $materialRequest->items()->findOrFail($itemData['id']);
                $newIssuedQty = $item->issued_quantity + $itemData['issued_quantity'];
                
                if ($newIssuedQty > $item->requested_quantity) {
                    DB::rollBack();
                    return response()->json([
                        'error' => "Issued quantity exceeds requested quantity for material {$item->material->name}"
                    ], 422);
                }

                $item->update(['issued_quantity' => $newIssuedQty]);
            }

            // Update status based on fulfillment
            $totalRequested = $materialRequest->items->sum('requested_quantity');
            $totalIssued = $materialRequest->items->sum('issued_quantity');

            if ($totalIssued >= $totalRequested) {
                $materialRequest->update(['status' => 'fulfilled']);
            } else if ($totalIssued > 0) {
                $materialRequest->update(['status' => 'partially_fulfilled']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Materials issued successfully',
                'data' => $materialRequest->fresh()->load(['items.material', 'requestedBy', 'department', 'project']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to issue materials'], 500);
        }
    }

    /**
     * Convert to purchase requisition.
     */
    public function convertToPurchaseRequisition(Request $request, MaterialRequest $materialRequest)
    {
        if ($materialRequest->status !== 'approved') {
            return response()->json(['error' => 'Only approved requests can be converted'], 422);
        }

        if ($materialRequest->request_type !== 'for_purchase') {
            return response()->json(['error' => 'Only purchase type requests can be converted'], 422);
        }

        // Placeholder for actual purchase requisition creation
        // This would integrate with a purchase requisition module when available

        return response()->json([
            'message' => 'Material request converted to purchase requisition successfully',
            'data' => [
                'material_request_id' => $materialRequest->id,
                'note' => 'Purchase requisition creation pending - integration required',
            ],
        ]);
    }
}
