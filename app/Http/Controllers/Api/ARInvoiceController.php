<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreARInvoiceRequest;
use App\Http\Requests\UpdateARInvoiceRequest;
use App\Http\Resources\ARInvoiceResource;
use App\Models\ARInvoice;
use App\Models\ARInvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ARInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = ARInvoice::with(['client', 'currency', 'createdBy']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by client
        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('invoice_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->where('invoice_date', '<=', $request->to_date);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(15);

        return ARInvoiceResource::collection($invoices);
    }

    public function store(StoreARInvoiceRequest $request)
    {
        try {
            DB::beginTransaction();

            $invoiceData = $request->validated();
            $items = $invoiceData['items'];
            unset($invoiceData['items']);

            // Add company and user
            $invoiceData['company_id'] = auth()->user()->company_id;
            $invoiceData['created_by_id'] = auth()->id();
            $invoiceData['received_amount'] = 0;

            $invoice = ARInvoice::create($invoiceData);

            // Create invoice items
            foreach ($items as $item) {
                $item['a_r_invoice_id'] = $invoice->id;
                ARInvoiceItem::create($item);
            }

            DB::commit();

            return new ARInvoiceResource($invoice->load(['client', 'currency', 'items', 'createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create invoice', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $invoice = ARInvoice::with(['client', 'project', 'contract', 'ipc', 'currency', 'items', 'createdBy'])
            ->findOrFail($id);

        return new ARInvoiceResource($invoice);
    }

    public function update(UpdateARInvoiceRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $invoice = ARInvoice::findOrFail($id);
            
            $invoiceData = $request->validated();
            
            // Handle items separately if provided
            if (isset($invoiceData['items'])) {
                $items = $invoiceData['items'];
                unset($invoiceData['items']);
                
                // Delete existing items and create new ones
                $invoice->items()->delete();
                foreach ($items as $item) {
                    $item['a_r_invoice_id'] = $invoice->id;
                    ARInvoiceItem::create($item);
                }
            }

            $invoice->update($invoiceData);

            DB::commit();

            return new ARInvoiceResource($invoice->load(['client', 'currency', 'items', 'createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update invoice', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $invoice = ARInvoice::findOrFail($id);
            
            // Check if invoice has been paid
            if ($invoice->received_amount > 0) {
                return response()->json(['error' => 'Cannot delete invoice with payments'], 400);
            }

            $invoice->delete();

            return response()->json(['message' => 'Invoice deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete invoice', 'message' => $e->getMessage()], 500);
        }
    }

    public function send($id)
    {
        try {
            $invoice = ARInvoice::findOrFail($id);
            
            $invoice->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return new ARInvoiceResource($invoice->load(['client', 'currency', 'items', 'createdBy']));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send invoice', 'message' => $e->getMessage()], 500);
        }
    }
}
