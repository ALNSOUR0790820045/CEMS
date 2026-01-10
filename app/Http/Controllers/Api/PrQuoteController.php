<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrQuote;
use App\Models\PrQuoteItem;
use App\Models\PurchaseRequisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PrQuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PrQuote::with(['vendor', 'currency', 'purchaseRequisition', 'items']);

        if ($request->has('purchase_requisition_id')) {
            $query->where('purchase_requisition_id', $request->purchase_requisition_id);
        }

        if ($request->has('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $quotes = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($quotes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_requisition_id' => 'required|exists:purchase_requisitions,id',
            'vendor_id' => 'required|exists:vendors,id',
            'quote_date' => 'required|date',
            'validity_date' => 'required|date|after_or_equal:quote_date',
            'currency_id' => 'required|exists:currencies,id',
            'payment_terms' => 'nullable|string',
            'delivery_terms' => 'nullable|string',
            'attachment_path' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.pr_item_id' => 'required|exists:purchase_requisition_items,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.delivery_days' => 'nullable|integer|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            foreach ($request->items as $itemData) {
                $discountPercentage = $itemData['discount_percentage'] ?? 0;
                $itemTotal = $itemData['quantity'] * $itemData['unit_price'];
                $itemTotal = $itemTotal - ($itemTotal * $discountPercentage / 100);
                $totalAmount += $itemTotal;
            }

            $quote = PrQuote::create([
                'purchase_requisition_id' => $request->purchase_requisition_id,
                'vendor_id' => $request->vendor_id,
                'quote_date' => $request->quote_date,
                'validity_date' => $request->validity_date,
                'total_amount' => $totalAmount,
                'currency_id' => $request->currency_id,
                'payment_terms' => $request->payment_terms,
                'delivery_terms' => $request->delivery_terms,
                'status' => 'received',
                'attachment_path' => $request->attachment_path,
                'notes' => $request->notes,
                'company_id' => auth()->user()->company_id,
            ]);

            foreach ($request->items as $itemData) {
                $discountPercentage = $itemData['discount_percentage'] ?? 0;
                $itemTotal = $itemData['quantity'] * $itemData['unit_price'];
                $itemTotal = $itemTotal - ($itemTotal * $discountPercentage / 100);

                PrQuoteItem::create([
                    'pr_quote_id' => $quote->id,
                    'pr_item_id' => $itemData['pr_item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'discount_percentage' => $discountPercentage,
                    'total_price' => $itemTotal,
                    'delivery_days' => $itemData['delivery_days'] ?? null,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Quote created successfully',
                'data' => $quote->load(['items.prItem', 'vendor', 'currency'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create quote', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $quote = PrQuote::with([
            'vendor',
            'currency',
            'purchaseRequisition',
            'items.prItem.unit'
        ])->findOrFail($id);

        return response()->json($quote);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $quote = PrQuote::findOrFail($id);

        if ($quote->status === 'selected') {
            return response()->json(['error' => 'Cannot edit selected quote'], 403);
        }

        $validator = Validator::make($request->all(), [
            'quote_date' => 'sometimes|date',
            'validity_date' => 'sometimes|date',
            'payment_terms' => 'nullable|string',
            'delivery_terms' => 'nullable|string',
            'attachment_path' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'sometimes|array|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $quote->update($request->only([
                'quote_date',
                'validity_date',
                'payment_terms',
                'delivery_terms',
                'attachment_path',
                'notes',
            ]));

            if ($request->has('items')) {
                $quote->items()->delete();
                
                $totalAmount = 0;
                foreach ($request->items as $itemData) {
                    $discountPercentage = $itemData['discount_percentage'] ?? 0;
                    $itemTotal = $itemData['quantity'] * $itemData['unit_price'];
                    $itemTotal = $itemTotal - ($itemTotal * $discountPercentage / 100);
                    $totalAmount += $itemTotal;

                    PrQuoteItem::create([
                        'pr_quote_id' => $quote->id,
                        'pr_item_id' => $itemData['pr_item_id'],
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $itemData['unit_price'],
                        'discount_percentage' => $discountPercentage,
                        'total_price' => $itemTotal,
                        'delivery_days' => $itemData['delivery_days'] ?? null,
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                }

                $quote->update(['total_amount' => $totalAmount]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Quote updated successfully',
                'data' => $quote->load(['items.prItem', 'vendor', 'currency'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update quote', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $quote = PrQuote::findOrFail($id);

        if ($quote->status === 'selected') {
            return response()->json(['error' => 'Cannot delete selected quote'], 403);
        }

        $quote->delete();

        return response()->json(['message' => 'Quote deleted successfully']);
    }

    /**
     * Select a quote
     */
    public function select(string $id)
    {
        $quote = PrQuote::findOrFail($id);

        if ($quote->status === 'selected') {
            return response()->json(['error' => 'Quote already selected'], 400);
        }

        $quote->select();

        return response()->json([
            'message' => 'Quote selected successfully',
            'data' => $quote
        ]);
    }

    /**
     * Get quotes for a purchase requisition
     */
    public function getQuotes(string $prId)
    {
        $quotes = PrQuote::where('purchase_requisition_id', $prId)
            ->with(['vendor', 'currency', 'items.prItem.unit'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($quotes);
    }

    /**
     * Request quotes from vendors
     */
    public function requestQuotes(Request $request, string $prId)
    {
        $validator = Validator::make($request->all(), [
            'vendor_ids' => 'required|array|min:1',
            'vendor_ids.*' => 'exists:vendors,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $requisition = PurchaseRequisition::findOrFail($prId);

        if ($requisition->status !== 'approved') {
            return response()->json(['error' => 'Only approved requisitions can request quotes'], 403);
        }

        // This would send quote requests to vendors - placeholder for now
        return response()->json([
            'message' => 'Quote request email functionality not yet implemented',
            'vendor_count' => count($request->vendor_ids)
        ]);
    }
}
