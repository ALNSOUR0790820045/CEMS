<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ARInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ARReportController extends Controller
{
    public function aging(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $asOfDate = $request->input('as_of_date', now()->format('Y-m-d'));

        $invoices = ARInvoice::with(['client', 'currency'])
            ->where('company_id', $companyId)
            ->whereIn('status', ['sent', 'overdue', 'partially_paid'])
            ->where('balance', '>', 0)
            ->get();

        $agingReport = [];

        foreach ($invoices as $invoice) {
            $daysOverdue = now()->diffInDays($invoice->due_date, false);
            
            $aging = 'current';
            if ($daysOverdue < -60) {
                $aging = 'over_60';
            } elseif ($daysOverdue < -30) {
                $aging = '31_60';
            } elseif ($daysOverdue < 0) {
                $aging = '1_30';
            }

            if (!isset($agingReport[$invoice->client_id])) {
                $agingReport[$invoice->client_id] = [
                    'client_id' => $invoice->client_id,
                    'client_name' => $invoice->client->name,
                    'current' => 0,
                    '1_30' => 0,
                    '31_60' => 0,
                    'over_60' => 0,
                    'total' => 0,
                ];
            }

            $agingReport[$invoice->client_id][$aging] += $invoice->balance;
            $agingReport[$invoice->client_id]['total'] += $invoice->balance;
        }

        return response()->json([
            'as_of_date' => $asOfDate,
            'data' => array_values($agingReport),
        ]);
    }

    public function clientBalance(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $clientId = $request->input('client_id');

        $query = ARInvoice::select(
            'client_id',
            DB::raw('SUM(total_amount) as total_invoiced'),
            DB::raw('SUM(received_amount) as total_received'),
            DB::raw('SUM(balance) as total_balance')
        )
        ->where('company_id', $companyId)
        ->whereIn('status', ['sent', 'overdue', 'partially_paid', 'paid'])
        ->groupBy('client_id');

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        $balances = $query->get();
        
        // Load clients separately
        $clientIds = $balances->pluck('client_id')->toArray();
        $clients = \App\Models\Client::whereIn('id', $clientIds)->get()->keyBy('id');

        return response()->json([
            'data' => $balances->map(function ($balance) use ($clients) {
                return [
                    'client_id' => $balance->client_id,
                    'client_name' => $clients[$balance->client_id]->name ?? 'Unknown',
                    'total_invoiced' => $balance->total_invoiced,
                    'total_received' => $balance->total_received,
                    'total_balance' => $balance->total_balance,
                ];
            }),
        ]);
    }

    public function collectionForecast(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $months = $request->input('months', 3);

        $forecast = [];
        
        for ($i = 0; $i < $months; $i++) {
            $startDate = now()->addMonths($i)->startOfMonth();
            $endDate = now()->addMonths($i)->endOfMonth();

            $expected = ARInvoice::where('company_id', $companyId)
                ->whereBetween('due_date', [$startDate, $endDate])
                ->whereIn('status', ['sent', 'overdue', 'partially_paid'])
                ->sum('balance');

            $forecast[] = [
                'month' => $startDate->format('Y-m'),
                'expected_collection' => $expected,
            ];
        }

        return response()->json([
            'data' => $forecast,
        ]);
    }

    public function dso(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $days = $request->input('days', 90);

        $startDate = now()->subDays($days);
        
        $totalSales = ARInvoice::where('company_id', $companyId)
            ->where('invoice_date', '>=', $startDate)
            ->sum('total_amount');

        $totalReceivables = ARInvoice::where('company_id', $companyId)
            ->whereIn('status', ['sent', 'overdue', 'partially_paid'])
            ->sum('balance');

        $dso = $totalSales > 0 ? ($totalReceivables / $totalSales) * $days : 0;

        return response()->json([
            'period_days' => $days,
            'total_sales' => $totalSales,
            'total_receivables' => $totalReceivables,
            'days_sales_outstanding' => round($dso, 2),
        ]);
    }
}
