<?php

namespace App\Http\Controllers;

use App\Models\SalesQuotation;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesQuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quotations = SalesQuotation::with('customer', 'creator')
            ->latest()
            ->get();
        
        return view('sales-quotations.index', compact('quotations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::all();
        $products = Product::where('is_active', true)->get();
        $quotationNumber = SalesQuotation::generateQuotationNumber();
        
        return view('sales-quotations.create', compact('customers', 'products', 'quotationNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'quotation_date' => 'required|date',
            'valid_until' => 'required|date|after:quotation_date',
            'status' => 'required|in:draft,sent,accepted,rejected,expired',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Create quotation
            $quotation = SalesQuotation::create([
                'quotation_number' => SalesQuotation::generateQuotationNumber(),
                'customer_id' => $validated['customer_id'],
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'],
                'status' => $validated['status'],
                'terms_conditions' => $validated['terms_conditions'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount' => 0,
                'total' => 0,
            ]);

            // Add items and calculate totals
            $subtotal = 0;
            $totalTax = 0;
            $totalDiscount = 0;

            foreach ($validated['items'] as $item) {
                $quantity = $item['quantity'];
                $unitPrice = $item['unit_price'];
                $taxRate = $item['tax_rate'];
                $discount = $item['discount'] ?? 0;

                $itemSubtotal = $quantity * $unitPrice;
                $itemDiscount = $discount;
                $itemTaxableAmount = $itemSubtotal - $itemDiscount;
                $itemTax = $itemTaxableAmount * ($taxRate / 100);
                $itemTotal = $itemTaxableAmount + $itemTax;

                $quotation->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $itemTax,
                    'discount' => $itemDiscount,
                    'total' => $itemTotal,
                ]);

                $subtotal += $itemSubtotal;
                $totalTax += $itemTax;
                $totalDiscount += $itemDiscount;
            }

            // Update quotation totals
            $quotation->update([
                'subtotal' => $subtotal,
                'tax_amount' => $totalTax,
                'discount' => $totalDiscount,
                'total' => $subtotal - $totalDiscount + $totalTax,
            ]);

            DB::commit();

            return redirect()->route('sales-quotations.show', $quotation)
                ->with('success', 'تم إنشاء عرض السعر بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء عرض السعر: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SalesQuotation $salesQuotation)
    {
        $salesQuotation->load('customer', 'items.product', 'creator');
        return view('sales-quotations.show', compact('salesQuotation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesQuotation $salesQuotation)
    {
        $customers = Customer::all();
        $products = Product::where('is_active', true)->get();
        $salesQuotation->load('items.product');
        
        return view('sales-quotations.edit', compact('salesQuotation', 'customers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalesQuotation $salesQuotation)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'quotation_date' => 'required|date',
            'valid_until' => 'required|date|after:quotation_date',
            'status' => 'required|in:draft,sent,accepted,rejected,expired',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Update quotation
            $salesQuotation->update([
                'customer_id' => $validated['customer_id'],
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'],
                'status' => $validated['status'],
                'terms_conditions' => $validated['terms_conditions'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Delete old items
            $salesQuotation->items()->delete();

            // Add new items and calculate totals
            $subtotal = 0;
            $totalTax = 0;
            $totalDiscount = 0;

            foreach ($validated['items'] as $item) {
                $quantity = $item['quantity'];
                $unitPrice = $item['unit_price'];
                $taxRate = $item['tax_rate'];
                $discount = $item['discount'] ?? 0;

                $itemSubtotal = $quantity * $unitPrice;
                $itemDiscount = $discount;
                $itemTaxableAmount = $itemSubtotal - $itemDiscount;
                $itemTax = $itemTaxableAmount * ($taxRate / 100);
                $itemTotal = $itemTaxableAmount + $itemTax;

                $salesQuotation->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $itemTax,
                    'discount' => $itemDiscount,
                    'total' => $itemTotal,
                ]);

                $subtotal += $itemSubtotal;
                $totalTax += $itemTax;
                $totalDiscount += $itemDiscount;
            }

            // Update quotation totals
            $salesQuotation->update([
                'subtotal' => $subtotal,
                'tax_amount' => $totalTax,
                'discount' => $totalDiscount,
                'total' => $subtotal - $totalDiscount + $totalTax,
            ]);

            DB::commit();

            return redirect()->route('sales-quotations.show', $salesQuotation)
                ->with('success', 'تم تحديث عرض السعر بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث عرض السعر: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesQuotation $salesQuotation)
    {
        $salesQuotation->delete();
        return redirect()->route('sales-quotations.index')
            ->with('success', 'تم حذف عرض السعر بنجاح');
    }

    /**
     * Export quotation as PDF
     */
    public function pdf(SalesQuotation $salesQuotation)
    {
        $salesQuotation->load('customer', 'items.product', 'creator');
        $pdf = Pdf::loadView('sales-quotations.pdf', compact('salesQuotation'));
        return $pdf->download($salesQuotation->quotation_number . '.pdf');
    }
}
