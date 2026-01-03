<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

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
        // Placeholder implementation
        // In a real implementation, this would query invoices/bills
        // and categorize them by age
        
        return [
            // Example structure
            // [
            //     'entity_name' => 'Vendor/Customer Name',
            //     'current' => 0,
            //     '30_days' => 0,
            //     '60_days' => 0,
            //     '90_days' => 0,
            //     'over_120' => 0,
            //     'total' => 0,
            // ]
        ];
    }
}
