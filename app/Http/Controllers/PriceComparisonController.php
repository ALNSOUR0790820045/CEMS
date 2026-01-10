<?php

namespace App\Http\Controllers;

use App\Models\PriceRequest;
use App\Models\PriceComparison;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PriceComparisonController extends Controller
{
    /**
     * Create a price comparison
     */
    public function create(PriceRequest $priceRequest)
    {
        $comparison = PriceComparison::create([
            'comparison_number' => 'CMP-' . $priceRequest->request_number,
            'price_request_id' => $priceRequest->id,
            'comparison_date' => now(),
            'prepared_by' => Auth::id(),
        ]);

        $priceRequest->load(['quotations.vendor', 'quotations.items.requestItem']);

        return view('prices.comparisons.create', compact('priceRequest', 'comparison'));
    }

    /**
     * Store the comparison result
     */
    public function store(Request $request, PriceRequest $priceRequest)
    {
        $validated = $request->validate([
            'selected_quotation_id' => 'required|exists:price_quotations,id',
            'selection_justification' => 'required|string',
        ]);

        $comparison = PriceComparison::create([
            'comparison_number' => 'CMP-' . $priceRequest->request_number . '-' . time(),
            'price_request_id' => $priceRequest->id,
            'comparison_date' => now(),
            'selected_quotation_id' => $validated['selected_quotation_id'],
            'selection_justification' => $validated['selection_justification'],
            'prepared_by' => Auth::id(),
        ]);

        // Mark the selected quotation
        $priceRequest->quotations()->update(['is_selected' => false]);
        $priceRequest->quotations()
            ->where('id', $validated['selected_quotation_id'])
            ->update(['is_selected' => true]);

        return redirect()->route('price-requests.show', $priceRequest)
            ->with('success', 'تم حفظ مقارنة الأسعار بنجاح');
    }

    /**
     * Show a comparison
     */
    public function show(PriceComparison $comparison)
    {
        $comparison->load([
            'priceRequest.quotations.vendor',
            'priceRequest.quotations.items.requestItem',
            'selectedQuotation',
            'preparer',
            'approver'
        ]);

        return view('prices.comparisons.show', compact('comparison'));
    }

    /**
     * Approve a comparison
     */
    public function approve(PriceComparison $comparison)
    {
        $comparison->update(['approved_by' => Auth::id()]);
        
        return redirect()->route('price-comparisons.show', $comparison)
            ->with('success', 'تم اعتماد المقارنة بنجاح');
    }
}
