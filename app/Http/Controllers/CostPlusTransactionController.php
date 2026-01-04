<?php

namespace App\Http\Controllers;

use App\Models\CostPlusTransaction;
use App\Models\CostPlusContract;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CostPlusTransactionController extends Controller
{
    public function index()
    {
        $transactions = CostPlusTransaction::with([
            'costPlusContract',
            'project',
            'recorder',
            'approver'
        ])->latest()->get();

        if (request()->wantsJson()) {
            return response()->json($transactions);
        }

        return view('cost-plus.transactions.index', compact('transactions'));
    }

    public function create()
    {
        $contracts = CostPlusContract::with('contract')->get();
        $projects = Project::where('status', 'active')->get();

        return view('cost-plus.transactions.create', compact('contracts', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_number' => 'required|string|unique:cost_plus_transactions',
            'cost_plus_contract_id' => 'required|exists:cost_plus_contracts,id',
            'project_id' => 'required|exists:projects,id',
            'transaction_date' => 'required|date',
            'cost_type' => 'required|in:material,labor,equipment,subcontract,overhead,other',
            'description' => 'required|string',
            'vendor_name' => 'nullable|string',
            'invoice_number' => 'nullable|string',
            'invoice_date' => 'nullable|date',
            'gross_amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'currency' => 'string|size:3',
            'is_reimbursable' => 'boolean',
        ]);

        $validated['recorded_by'] = auth()->id();
        $transaction = CostPlusTransaction::create($validated);

        if ($request->wantsJson()) {
            return response()->json($transaction, 201);
        }

        return redirect()->route('cost-plus.transactions.show', $transaction)
            ->with('success', 'Transaction created successfully');
    }

    public function show($id)
    {
        $transaction = CostPlusTransaction::with([
            'costPlusContract',
            'project',
            'recorder',
            'approver',
            'grn'
        ])->findOrFail($id);

        if (request()->wantsJson()) {
            return response()->json($transaction);
        }

        return view('cost-plus.transactions.show', compact('transaction'));
    }

    public function update(Request $request, $id)
    {
        $transaction = CostPlusTransaction::findOrFail($id);

        // Only allow updates if not yet approved
        if ($transaction->status === 'approved') {
            return back()->with('error', 'Cannot update approved transaction');
        }

        $validated = $request->validate([
            'transaction_date' => 'sometimes|date',
            'cost_type' => 'sometimes|in:material,labor,equipment,subcontract,overhead,other',
            'description' => 'sometimes|string',
            'vendor_name' => 'nullable|string',
            'invoice_number' => 'nullable|string',
            'invoice_date' => 'nullable|date',
            'gross_amount' => 'sometimes|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'net_amount' => 'sometimes|numeric|min:0',
            'is_reimbursable' => 'boolean',
        ]);

        $transaction->update($validated);

        if ($request->wantsJson()) {
            return response()->json($transaction);
        }

        return back()->with('success', 'Transaction updated successfully');
    }

    public function approve($id)
    {
        $transaction = CostPlusTransaction::findOrFail($id);

        if (!$transaction->documentation_complete) {
            return back()->with('error', 'Cannot approve: Documentation incomplete');
        }

        $transaction->approve(auth()->id());

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Transaction approved successfully']);
        }

        return back()->with('success', 'Transaction approved successfully');
    }

    public function uploadDocuments(Request $request, $id)
    {
        $transaction = CostPlusTransaction::findOrFail($id);

        $validated = $request->validate([
            'original_invoice' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'payment_receipt' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'photo_evidence' => 'nullable|image|max:5120',
            'photo_latitude' => 'nullable|numeric',
            'photo_longitude' => 'nullable|numeric',
            'grn_id' => 'nullable|exists:goods_receipt_notes,id',
        ]);

        // Handle file uploads
        if ($request->hasFile('original_invoice')) {
            $path = $request->file('original_invoice')->store('cost-plus/invoices', 'public');
            $transaction->original_invoice_file = $path;
            $transaction->has_original_invoice = true;
        }

        if ($request->hasFile('payment_receipt')) {
            $path = $request->file('payment_receipt')->store('cost-plus/receipts', 'public');
            $transaction->payment_receipt_file = $path;
            $transaction->has_payment_receipt = true;
        }

        if ($request->hasFile('photo_evidence')) {
            $path = $request->file('photo_evidence')->store('cost-plus/photos', 'public');
            $transaction->photo_file = $path;
            $transaction->has_photo_evidence = true;
            $transaction->photo_latitude = $request->input('photo_latitude');
            $transaction->photo_longitude = $request->input('photo_longitude');
            $transaction->photo_timestamp = now();
        }

        if ($request->filled('grn_id')) {
            $transaction->grn_id = $request->input('grn_id');
            $transaction->has_grn = true;
        }

        $transaction->save();
        $transaction->checkDocumentation();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Documents uploaded successfully']);
        }

        return back()->with('success', 'Documents uploaded successfully');
    }
}
