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
     *
     * TODO: This is a placeholder implementation that returns an empty array.
     * A complete implementation requires:
     * 1. Invoice/Bill models and tables
     * 2. Query unpaid invoices/bills based on type (payable/receivable)
     * 3. Calculate days outstanding: (as_of_date - invoice_date)
     * 4. Categorize into aging buckets: current (0-30), 30-60, 60-90, 90-120, 120+
     * 5. Group and sum by vendor/customer
     */
    private function calculateAging(string $type, $asOfDate): array
    {
        // TODO: Implement actual aging calculations
        // This requires invoice/bill tracking which is not yet implemented

        return [
            // Example structure for reference:
            // [
            //     'entity_name' => 'Vendor/Customer Name',
            //     'current' => 0,      // 0-30 days
            //     '30_days' => 0,      // 31-60 days
            //     '60_days' => 0,      // 61-90 days
            //     '90_days' => 0,      // 91-120 days
            //     'over_120' => 0,     // 120+ days
            //     'total' => 0,
            // ]
        ];
    }
}
