<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\PaymentTerm;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'warehouse', 'paymentTerm']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('order_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('order_date', '<=', $request->to_date);
        }

        $purchaseOrders = $query->latest()->paginate(15);
        $suppliers = Supplier::where('is_active', true)->get();

        return view('purchase-orders.index', compact('purchaseOrders', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $paymentTerms = PaymentTerm::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();

        return view('purchase-orders.create', compact('suppliers', 'warehouses', 'paymentTerms', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'payment_term_id' => 'required|exists:payment_terms,id',
            'notes' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'order_date' => $validated['order_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'payment_term_id' => $validated['payment_term_id'],
                'notes' => $validated['notes'] ?? null,
                'discount' => $validated['discount'] ?? 0,
                'created_by' => Auth::id(),
                'status' => 'draft',
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? $product->tax_rate,
                    'discount' => $item['discount'] ?? 0,
                ]);
            }

            DB::commit();

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'تم إنشاء أمر الشراء بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء أمر الشراء: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'warehouse', 'paymentTerm', 'items.product', 'creator']);
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        if (in_array($purchaseOrder->status, ['confirmed', 'received', 'cancelled'])) {
            return back()->with('error', 'لا يمكن تعديل أمر الشراء في هذه الحالة');
        }

        $suppliers = Supplier::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $paymentTerms = PaymentTerm::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        $purchaseOrder->load('items.product');

        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'warehouses', 'paymentTerms', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (in_array($purchaseOrder->status, ['confirmed', 'received', 'cancelled'])) {
            return back()->with('error', 'لا يمكن تعديل أمر الشراء في هذه الحالة');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'payment_term_id' => 'required|exists:payment_terms,id',
            'notes' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $purchaseOrder->update([
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'order_date' => $validated['order_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'payment_term_id' => $validated['payment_term_id'],
                'notes' => $validated['notes'] ?? null,
                'discount' => $validated['discount'] ?? 0,
            ]);

            // Delete existing items
            $purchaseOrder->items()->delete();

            // Create new items
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'] ?? $product->tax_rate,
                    'discount' => $item['discount'] ?? 0,
                ]);
            }

            DB::commit();

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'تم تحديث أمر الشراء بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث أمر الشراء: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received') {
            return back()->with('error', 'لا يمكن حذف أمر شراء تم استلامه');
        }

        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')
            ->with('success', 'تم حذف أمر الشراء بنجاح');
    }

    /**
     * Update status
     */
    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,confirmed,received,cancelled',
        ]);

        $purchaseOrder->update(['status' => $validated['status']]);

        return back()->with('success', 'تم تحديث حالة أمر الشراء بنجاح');
    }
}
