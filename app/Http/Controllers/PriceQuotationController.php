<?php

namespace App\Http\Controllers;

use App\Models\PriceRequest;
use App\Models\PriceQuotation;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceQuotationController extends Controller
{
    /**
     * Display quotations for a price request
     */
    public function index(PriceRequest $priceRequest)
    {
        $quotations = $priceRequest->quotations()
            ->with(['vendor', 'items.requestItem'])
            ->get();
            
        return view('prices.quotations.index', compact('priceRequest', 'quotations'));
    }

    /**
     * Store a new quotation
     */
    public function store(Request $request, PriceRequest $priceRequest)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'quotation_number' => 'nullable|string',
            'quotation_date' => 'required|date',
            'validity_date' => 'required|date|after:quotation_date',
            'currency' => 'required|string|size:3',
            'payment_terms' => 'nullable|string',
            'delivery_terms' => 'nullable|string',
            'file_path' => 'nullable|string',
            'items' => 'required|array',
            'items.*.request_item_id' => 'required|exists:price_request_items,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Calculate total amount
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $requestItem = $priceRequest->items()->find($item['request_item_id']);
                $totalAmount += $item['unit_price'] * $requestItem->quantity;
            }

            $quotation = PriceQuotation::create([
                'price_request_id' => $priceRequest->id,
                'vendor_id' => $validated['vendor_id'],
                'quotation_number' => $validated['quotation_number'] ?? null,
                'quotation_date' => $validated['quotation_date'],
                'validity_date' => $validated['validity_date'],
                'total_amount' => $totalAmount,
                'currency' => $validated['currency'],
                'payment_terms' => $validated['payment_terms'] ?? null,
                'delivery_terms' => $validated['delivery_terms'] ?? null,
                'file_path' => $validated['file_path'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $requestItem = $priceRequest->items()->find($item['request_item_id']);
                $quotation->items()->create([
                    'request_item_id' => $item['request_item_id'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['unit_price'] * $requestItem->quantity,
                    'remarks' => $item['remarks'] ?? null,
                ]);
            }

            DB::commit();
            
            return redirect()->route('price-requests.quotations', $priceRequest)
                ->with('success', 'تم إضافة عرض السعر بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إضافة عرض السعر');
        }
    }

    /**
     * Show a specific quotation
     */
    public function show(PriceQuotation $quotation)
    {
        $quotation->load(['priceRequest', 'vendor', 'items.requestItem']);
        return view('prices.quotations.show', compact('quotation'));
    }
}
