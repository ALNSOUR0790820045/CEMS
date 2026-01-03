<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApInvoice;
use App\Models\ApInvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ap_invoices.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:ap_invoices.create', ['only' => ['store']]);
        $this->middleware('permission:ap_invoices.edit', ['only' => ['update']]);
        $this->middleware('permission:ap_invoices.delete', ['only' => ['destroy']]);
        $this->middleware('permission:ap_invoices.approve', ['only' => ['approve']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ApInvoice::with(['vendor', 'currency', 'project', 'purchaseOrder', 'glAccount', 'createdBy', 'approvedBy'])
            ->where('company_id', $request->user()->company_id);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->has('from_date')) {
            $query->where('invoice_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('invoice_date', '<=', $request->to_date);
        }

        $invoices = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($invoices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'vendor_id' => 'required|exists:vendors,id',
            'project_id' => 'nullable|exists:projects,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_terms' => 'required|in:cod,net_7,net_15,net_30,net_45,net_60',
            'gl_account_id' => 'required|exists:gl_accounts,id',
            'attachment_path' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.gl_account_id' => 'nullable|exists:gl_accounts,id',
            'items.*.project_id' => 'nullable|exists:projects,id',
            'items.*.cost_center_id' => 'nullable|exists:cost_centers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $invoice = ApInvoice::create([
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'vendor_id' => $request->vendor_id,
                'project_id' => $request->project_id,
                'purchase_order_id' => $request->purchase_order_id,
                'currency_id' => $request->currency_id,
                'exchange_rate' => $request->exchange_rate ?? 1,
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'payment_terms' => $request->payment_terms,
                'gl_account_id' => $request->gl_account_id,
                'attachment_path' => $request->attachment_path,
                'notes' => $request->notes,
                'company_id' => $request->user()->company_id,
                'created_by_id' => $request->user()->id,
                'status' => 'draft',
            ]);

            // Create invoice items
            foreach ($request->items as $item) {
                ApInvoiceItem::create([
                    'ap_invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'gl_account_id' => $item['gl_account_id'] ?? null,
                    'project_id' => $item['project_id'] ?? null,
                    'cost_center_id' => $item['cost_center_id'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Invoice created successfully',
                'invoice' => $invoice->load('items', 'vendor', 'currency')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create invoice: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ApInvoice $invoice)
    {
        $invoice->load(['vendor', 'currency', 'project', 'purchaseOrder', 'glAccount', 'createdBy', 'approvedBy', 'items.glAccount', 'items.project', 'items.costCenter', 'paymentAllocations.apPayment']);

        return response()->json($invoice);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ApInvoice $invoice)
    {
        // Only draft invoices can be updated
        if ($invoice->status !== 'draft') {
            return response()->json(['error' => 'Only draft invoices can be updated'], 422);
        }

        $validator = Validator::make($request->all(), [
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'vendor_id' => 'required|exists:vendors,id',
            'project_id' => 'nullable|exists:projects,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'currency_id' => 'required|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_terms' => 'required|in:cod,net_7,net_15,net_30,net_45,net_60',
            'gl_account_id' => 'required|exists:gl_accounts,id',
            'attachment_path' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.gl_account_id' => 'nullable|exists:gl_accounts,id',
            'items.*.project_id' => 'nullable|exists:projects,id',
            'items.*.cost_center_id' => 'nullable|exists:cost_centers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $invoice->update([
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'vendor_id' => $request->vendor_id,
                'project_id' => $request->project_id,
                'purchase_order_id' => $request->purchase_order_id,
                'currency_id' => $request->currency_id,
                'exchange_rate' => $request->exchange_rate ?? 1,
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'payment_terms' => $request->payment_terms,
                'gl_account_id' => $request->gl_account_id,
                'attachment_path' => $request->attachment_path,
                'notes' => $request->notes,
            ]);

            // Delete existing items and create new ones
            $invoice->items()->delete();
            foreach ($request->items as $item) {
                ApInvoiceItem::create([
                    'ap_invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'gl_account_id' => $item['gl_account_id'] ?? null,
                    'project_id' => $item['project_id'] ?? null,
                    'cost_center_id' => $item['cost_center_id'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Invoice updated successfully',
                'invoice' => $invoice->load('items', 'vendor', 'currency')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update invoice: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApInvoice $invoice)
    {
        // Only draft invoices can be deleted
        if ($invoice->status !== 'draft') {
            return response()->json(['error' => 'Only draft invoices can be deleted'], 422);
        }

        $invoice->delete();

        return response()->json(['message' => 'Invoice deleted successfully']);
    }

    /**
     * Approve an invoice.
     */
    public function approve(Request $request, ApInvoice $invoice)
    {
        if ($invoice->status !== 'draft' && $invoice->status !== 'pending') {
            return response()->json(['error' => 'Invoice cannot be approved in current status'], 422);
        }

        $invoice->approve($request->user());

        return response()->json([
            'message' => 'Invoice approved successfully',
            'invoice' => $invoice->load('approvedBy')
        ]);
    }
}
