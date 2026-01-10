<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgedReportController extends Controller
{
    /**
     * Get Accounts Payable Aging Report
     */
    public function accountsPayableAging(Request $request): JsonResponse
    {
        $request->validate([
            'as_of_date' => 'nullable|date',
            'company_id' => 'nullable|exists:companies,id',
            'vendor_id' => 'nullable|integer',
        ]);

        $asOfDate = $request->input('as_of_date', Carbon::now());

        $aging = $this->calculateAging('payable', $asOfDate);

        return response()->json([
            'status' => 'success',
            'data' => [
                'as_of_date' => $asOfDate,
                'aging' => $aging,
                'summary' => [
                    'current' => array_sum(array_column($aging, 'current')),
                    '30_days' => array_sum(array_column($aging, '30_days')),
                    '60_days' => array_sum(array_column($aging, '60_days')),
                    '90_days' => array_sum(array_column($aging, '90_days')),
                    'over_120' => array_sum(array_column($aging, 'over_120')),
                    'total' => array_sum(array_column($aging, 'total')),
                ],
            ],
        ]);
    }

    /**
     * Get Accounts Receivable Aging Report
     */
    public function accountsReceivableAging(Request $request): JsonResponse
    {
        $request->validate([
            'as_of_date' => 'nullable|date',
            'company_id' => 'nullable|exists:companies,id',
            'customer_id' => 'nullable|integer',
        ]);

        $asOfDate = $request->input('as_of_date', Carbon::now());

        $aging = $this->calculateAging('receivable', $asOfDate);

        return response()->json([
            'status' => 'success',
            'data' => [
                'as_of_date' => $asOfDate,
                'aging' => $aging,
                'summary' => [
                    'current' => array_sum(array_column($aging, 'current')),
                    '30_days' => array_sum(array_column($aging, '30_days')),
                    '60_days' => array_sum(array_column($aging, '60_days')),
                    '90_days' => array_sum(array_column($aging, '90_days')),
                    'over_120' => array_sum(array_column($aging, 'over_120')),
                    'total' => array_sum(array_column($aging, 'total')),
                ],
            ],
        ]);
    }

    /**
     * Get Vendor Outstanding Report
     */
    public function vendorOutstanding(Request $request): JsonResponse
    {
        $request->validate([
            'as_of_date' => 'nullable|date',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $asOfDate = $request->input('as_of_date', Carbon::now());

        return response()->json([
            'status' => 'success',
            'data' => [
                'as_of_date' => $asOfDate,
                'vendors' => [],
                'total_outstanding' => 0,
            ],
        ]);
    }

    /**
     * Get Customer Outstanding Report
     */
    public function customerOutstanding(Request $request): JsonResponse
    {
        $request->validate([
            'as_of_date' => 'nullable|date',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $asOfDate = $request->input('as_of_date', Carbon::now());

        return response()->json([
            'status' => 'success',
            'data' => [
                'as_of_date' => $asOfDate,
                'customers' => [],
                'total_outstanding' => 0,
            ],
        ]);
    }

    /**
     * Helper: Calculate aging buckets
     */
    private function calculateAging(string $type, $asOfDate): array
    {
        $companyId = auth()->user()->company_id ?? null;
        
        if ($type === 'payable') {
            $query = \App\Models\ApInvoice::with('vendor')
                ->where('status', '!=', 'paid');
            
            if ($companyId) {
                $query->where('company_id', $companyId);
            }
            
            $invoices = $query->get();
            $entityKey = 'vendor';
        } else {
            $query = \App\Models\ARInvoice::with('client')
                ->where('status', '!=', 'paid');
            
            if ($companyId) {
                $query->where('company_id', $companyId);
            }
            
            $invoices = $query->get();
            $entityKey = 'client';
        }
        
        $aging = [];
        $asOfDate = Carbon::parse($asOfDate);
        
        foreach ($invoices as $invoice) {
            $entityId = $invoice->{$entityKey . '_id'};
            $entity = $invoice->{$entityKey};
            
            if (!$entity) {
                continue;
            }
            
            $entityName = $entity->name;
            
            if (!isset($aging[$entityId])) {
                $aging[$entityId] = [
                    'entity_id' => $entityId,
                    'entity_name' => $entityName,
                    'current' => 0,
                    '30_days' => 0,
                    '60_days' => 0,
                    '90_days' => 0,
                    'over_120' => 0,
                    'total' => 0,
                ];
            }
            
            // Calculate days overdue (negative means overdue)
            $daysOverdue = $asOfDate->diffInDays(Carbon::parse($invoice->due_date), false);
            $balance = $invoice->balance ?? 0;
            
            // Categorize into aging buckets
            if ($daysOverdue >= 0) {
                // Not yet due
                $aging[$entityId]['current'] += $balance;
            } elseif ($daysOverdue > -30) {
                // 1-30 days overdue
                $aging[$entityId]['30_days'] += $balance;
            } elseif ($daysOverdue > -60) {
                // 31-60 days overdue
                $aging[$entityId]['60_days'] += $balance;
            } elseif ($daysOverdue > -90) {
                // 61-90 days overdue
                $aging[$entityId]['90_days'] += $balance;
            } else {
                // Over 90 days overdue
                $aging[$entityId]['over_120'] += $balance;
            }
            
            $aging[$entityId]['total'] += $balance;
        }
        
        return array_values($aging);
    }
}
