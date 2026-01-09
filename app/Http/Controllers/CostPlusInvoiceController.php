<?php

namespace App\Http\Controllers;

use App\Models\CostPlusInvoice;
use App\Models\CostPlusContract;
use App\Models\CostPlusTransaction;
use App\Models\CostPlusInvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CostPlusInvoiceController extends Controller
{
    public function index()
    {
        $invoices = CostPlusInvoice::with([
            'costPlusContract',
            'project',
            'preparer'
        ])->latest()->get();

        $contracts = CostPlusContract::with('contract')->get();
        $projects = Project::where('status', 'active')->get();

        if (request()->wantsJson()) {
            return response()->json($invoices);
        }

        return view('cost-plus.invoices.index', compact('invoices', 'contracts', 'projects'));
    }

    public function show($id)
    {
        $invoice = CostPlusInvoice::with([
            'costPlusContract',
            'project',
            'preparer',
            'approver',
            'items.transaction'
        ])->findOrFail($id);

        if (request()->wantsJson()) {
            return response()->json($invoice);
        }

        return view('cost-plus.invoices.show', compact('invoice'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'cost_plus_contract_id' => 'required|exists:cost_plus_contracts,id',
            'project_id' => 'required|exists:projects,id',
            'invoice_number' => 'required|string|unique:cost_plus_invoices',
            'period_from' => 'required|date',
            'period_to' => 'required|date|after:period_from',
            'vat_percentage' => 'numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $contract = CostPlusContract::findOrFail($validated['cost_plus_contract_id']);

            // Get approved transactions in the period
            $transactions = CostPlusTransaction::where('cost_plus_contract_id', $contract->id)
                ->where('status', 'approved')
                ->whereBetween('transaction_date', [$validated['period_from'], $validated['period_to']])
                ->where('is_reimbursable', true)
                ->whereNotIn('status', ['invoiced', 'paid'])
                ->get();

            // Calculate costs by type
            $materialCosts = $transactions->where('cost_type', 'material')->sum('net_amount');
            $laborCosts = $transactions->where('cost_type', 'labor')->sum('net_amount');
            $equipmentCosts = $transactions->where('cost_type', 'equipment')->sum('net_amount');
            $subcontractCosts = $transactions->where('cost_type', 'subcontract')->sum('net_amount');
            $overheadCosts = $transactions->where('cost_type', 'overhead')->sum('net_amount');
            $otherCosts = $transactions->where('cost_type', 'other')->sum('net_amount');

            $totalDirectCosts = $materialCosts + $laborCosts + $equipmentCosts + 
                               $subcontractCosts + $overheadCosts + $otherCosts;

            // Calculate fee
            $feeAmount = $contract->calculateFee($totalDirectCosts);

            // Calculate cumulative costs
            $previousInvoices = CostPlusInvoice::where('cost_plus_contract_id', $contract->id)
                ->where('status', '!=', 'rejected')
                ->sum('total_direct_costs');
            $cumulativeCosts = $previousInvoices + $totalDirectCosts;

            // Check GMP
            $gmpStatus = $contract->checkGMPStatus();

            // Create invoice
            $invoice = CostPlusInvoice::create([
                'invoice_number' => $validated['invoice_number'],
                'cost_plus_contract_id' => $contract->id,
                'project_id' => $validated['project_id'],
                'invoice_date' => now(),
                'period_from' => $validated['period_from'],
                'period_to' => $validated['period_to'],
                'material_costs' => $materialCosts,
                'labor_costs' => $laborCosts,
                'equipment_costs' => $equipmentCosts,
                'subcontract_costs' => $subcontractCosts,
                'overhead_costs' => $overheadCosts,
                'other_costs' => $otherCosts,
                'total_direct_costs' => $totalDirectCosts,
                'fee_amount' => $feeAmount,
                'incentive_amount' => 0,
                'vat_percentage' => $validated['vat_percentage'] ?? 16,
                'cumulative_costs' => $cumulativeCosts,
                'gmp_remaining' => $gmpStatus['remaining'],
                'gmp_exceeded' => $gmpStatus['exceeded'],
                'prepared_by' => auth()->id(),
                'currency' => $contract->currency,
            ]);

            // Calculate totals
            $invoice->calculateTotals();
            $invoice->save();

            // Create invoice items
            foreach ($transactions as $transaction) {
                CostPlusInvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'transaction_id' => $transaction->id,
                    'amount' => $transaction->net_amount,
                ]);

                // Update transaction status
                $transaction->update(['status' => 'invoiced']);
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json($invoice, 201);
            }

            return redirect()->route('cost-plus.invoices.show', $invoice)
                ->with('success', 'Invoice generated successfully');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            \Log::error('Invoice generation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate invoice: Database error');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Invoice generation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate invoice: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        $invoice = CostPlusInvoice::findOrFail($id);

        if ($invoice->status !== 'submitted') {
            return back()->with('error', 'Can only approve submitted invoices');
        }

        $invoice->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Invoice approved successfully']);
        }

        return back()->with('success', 'Invoice approved successfully');
    }

    public function export($id)
    {
        $invoice = CostPlusInvoice::with([
            'costPlusContract',
            'project',
            'items.transaction'
        ])->findOrFail($id);

        // Here you would implement PDF generation
        // For now, return JSON
        return response()->json($invoice);
    }
}
