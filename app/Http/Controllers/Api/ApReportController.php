<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApInvoice;
use App\Models\ApPayment;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApReportController extends Controller
{
    /**
     * Generate aging report (30/60/90 days).
     */
    public function aging(Request $request)
    {
        $companyId = $request->user()->company_id;
        $asOfDate = $request->as_of_date ?? now()->toDateString();

        $invoices = ApInvoice::with(['vendor'])
            ->where('company_id', $companyId)
            ->whereIn('status', ['approved', 'partially_paid'])
            ->where('balance', '>', 0)
            ->get();

        $aging = [
            'as_of_date' => $asOfDate,
            'current' => 0,
            '1_30_days' => 0,
            '31_60_days' => 0,
            '61_90_days' => 0,
            'over_90_days' => 0,
            'total' => 0,
            'by_vendor' => [],
        ];

        foreach ($invoices as $invoice) {
            $daysOverdue = now()->diffInDays($invoice->due_date, false);
            $balance = $invoice->balance;

            if ($daysOverdue >= 0) {
                $aging['current'] += $balance;
                $category = 'current';
            } elseif ($daysOverdue > -31) {
                $aging['1_30_days'] += $balance;
                $category = '1_30_days';
            } elseif ($daysOverdue > -61) {
                $aging['31_60_days'] += $balance;
                $category = '31_60_days';
            } elseif ($daysOverdue > -91) {
                $aging['61_90_days'] += $balance;
                $category = '61_90_days';
            } else {
                $aging['over_90_days'] += $balance;
                $category = 'over_90_days';
            }

            $aging['total'] += $balance;

            // Group by vendor
            $vendorId = $invoice->vendor_id;
            if (!isset($aging['by_vendor'][$vendorId])) {
                $aging['by_vendor'][$vendorId] = [
                    'vendor_name' => $invoice->vendor->name,
                    'current' => 0,
                    '1_30_days' => 0,
                    '31_60_days' => 0,
                    '61_90_days' => 0,
                    'over_90_days' => 0,
                    'total' => 0,
                ];
            }

            $aging['by_vendor'][$vendorId][$category] += $balance;
            $aging['by_vendor'][$vendorId]['total'] += $balance;
        }

        // Convert by_vendor to array
        $aging['by_vendor'] = array_values($aging['by_vendor']);

        return response()->json($aging);
    }

    /**
     * Generate vendor balance report.
     */
    public function vendorBalance(Request $request)
    {
        $companyId = $request->user()->company_id;

        $vendors = Vendor::where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        $balances = [];

        foreach ($vendors as $vendor) {
            $invoiceBalance = ApInvoice::where('vendor_id', $vendor->id)
                ->where('company_id', $companyId)
                ->whereIn('status', ['approved', 'partially_paid'])
                ->sum(DB::raw('subtotal + tax_amount - discount_amount - paid_amount'));

            if ($invoiceBalance > 0) {
                $balances[] = [
                    'vendor_id' => $vendor->id,
                    'vendor_name' => $vendor->name,
                    'vendor_code' => $vendor->vendor_code,
                    'balance' => $invoiceBalance,
                    'invoice_count' => ApInvoice::where('vendor_id', $vendor->id)
                        ->where('company_id', $companyId)
                        ->whereIn('status', ['approved', 'partially_paid'])
                        ->count(),
                ];
            }
        }

        // Sort by balance descending
        usort($balances, function ($a, $b) {
            return $b['balance'] <=> $a['balance'];
        });

        return response()->json([
            'balances' => $balances,
            'total_balance' => array_sum(array_column($balances, 'balance')),
        ]);
    }

    /**
     * Generate payment history report.
     */
    public function paymentHistory(Request $request)
    {
        $companyId = $request->user()->company_id;

        $query = ApPayment::with(['vendor', 'currency', 'allocations.apInvoice'])
            ->where('company_id', $companyId);

        // Apply filters
        if ($request->has('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->has('from_date')) {
            $query->where('payment_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('payment_date', '<=', $request->to_date);
        }

        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->orderBy('payment_date', 'desc')->get();

        $summary = [
            'total_payments' => $payments->count(),
            'total_amount' => $payments->sum('amount'),
            'by_method' => [],
        ];

        // Group by payment method
        foreach ($payments->groupBy('payment_method') as $method => $methodPayments) {
            $summary['by_method'][$method] = [
                'count' => $methodPayments->count(),
                'amount' => $methodPayments->sum('amount'),
            ];
        }

        return response()->json([
            'payments' => $payments,
            'summary' => $summary,
        ]);
    }

    /**
     * Generate cash flow forecast.
     */
    public function cashFlowForecast(Request $request)
    {
        $companyId = $request->user()->company_id;
        $months = $request->months ?? 3;

        $forecast = [];

        for ($i = 0; $i < $months; $i++) {
            $startDate = now()->addMonths($i)->startOfMonth();
            $endDate = now()->addMonths($i)->endOfMonth();

            $duePaidInvoices = ApInvoice::where('company_id', $companyId)
                ->whereIn('status', ['approved', 'partially_paid'])
                ->whereBetween('due_date', [$startDate, $endDate])
                ->sum(DB::raw('subtotal + tax_amount - discount_amount - paid_amount'));

            $forecast[] = [
                'month' => $startDate->format('Y-m'),
                'month_name' => $startDate->format('F Y'),
                'expected_payable' => $duePaidInvoices,
            ];
        }

        return response()->json([
            'forecast' => $forecast,
            'total_expected' => array_sum(array_column($forecast, 'expected_payable')),
        ]);
    }
}
