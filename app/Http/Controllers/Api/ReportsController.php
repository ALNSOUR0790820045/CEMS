<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\Reports\TrialBalanceReportService;
use App\Services\Reports\BalanceSheetReportService;
use App\Services\Reports\IncomeStatementReportService;
use App\Services\Reports\GeneralLedgerReportService;
use App\Services\Reports\AccountsPayableAgingReportService;
use App\Services\Reports\AccountsReceivableAgingReportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReportsController extends Controller
{
    public function trialBalance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'account_type' => 'nullable|string',
            'cost_center' => 'nullable|string',
        ]);

        $company = $this->getCompany($request);
        $service = new TrialBalanceReportService($company, $validated);
        
        $report = $service->getReport();

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function balanceSheet(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'as_of_date' => 'required|date',
            'comparative' => 'nullable|boolean',
        ]);

        $company = $this->getCompany($request);
        $service = new BalanceSheetReportService($company, $validated);
        
        $report = $service->getReport();

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function incomeStatement(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'breakdown' => 'nullable|in:total,monthly,quarterly,yearly',
        ]);

        $company = $this->getCompany($request);
        $service = new IncomeStatementReportService($company, $validated);
        
        $report = $service->getReport();

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function generalLedger(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'account_id' => 'required|integer',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $company = $this->getCompany($request);
        $service = new GeneralLedgerReportService($company, $validated);
        
        $report = $service->getReport();

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function apAging(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'as_of_date' => 'required|date',
        ]);

        $company = $this->getCompany($request);
        $service = new AccountsPayableAgingReportService($company, $validated);
        
        $report = $service->getReport();

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function arAging(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'as_of_date' => 'required|date',
        ]);

        $company = $this->getCompany($request);
        $service = new AccountsReceivableAgingReportService($company, $validated);
        
        $report = $service->getReport();

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    private function getCompany(Request $request): Company
    {
        // Get company from authenticated user or request
        // For now, return first company as placeholder
        return Company::firstOrFail();
    }
}

