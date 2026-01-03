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
use App\Services\Reports\CashFlowReportService;
use App\Services\Reports\AccountTransactionsReportService;
use App\Services\Reports\VendorStatementReportService;
use App\Services\Reports\CustomerStatementReportService;
use App\Services\Reports\ProjectProfitabilityReportService;
use App\Services\Reports\CostCenterReportService;
use App\Services\Reports\BudgetVsActualReportService;
use App\Services\Reports\PaymentAnalysisReportService;
use App\Services\Reports\TaxReportService;
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

    public function cashFlow(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'method' => 'nullable|in:direct,indirect',
        ]);

        $company = $this->getCompany($request);
        $service = new CashFlowReportService($company, $validated);
        
        $report = $service->getReport();

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function accountTransactions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'account_id' => 'required|integer',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'transaction_type' => 'nullable|string',
        ]);

        $company = $this->getCompany($request);
        $service = new AccountTransactionsReportService($company, $validated);
        
        $report = $service->getReport();

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function vendorStatement(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vendor_id' => 'required|integer',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $company = $this->getCompany($request);
        $service = new VendorStatementReportService($company, $validated);
        
        $report = $service->getReport();

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function customerStatement(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $company = $this->getCompany($request);
        $service = new CustomerStatementReportService($company, $validated);
        
        $report = $service->getReport();

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function projectProfitability(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|integer',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        $company = $this->getCompany($request);
        $service = new ProjectProfitabilityReportService($company, $validated);
        
        $report = $service->getReport();

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function costCenter(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cost_center_id' => 'required|integer',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $company = $this->getCompany($request);
        $service = new CostCenterReportService($company, $validated);
        
        $report = $service->getReport();

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function budgetVsActual(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'breakdown' => 'nullable|in:total,monthly,quarterly,yearly',
            'account_id' => 'nullable|integer',
            'cost_center_id' => 'nullable|integer',
        ]);

        $company = $this->getCompany($request);
        $service = new BudgetVsActualReportService($company, $validated);
        
        $report = $service->getReport();

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function paymentAnalysis(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'payment_method' => 'nullable|string',
            'party_type' => 'nullable|in:vendor,customer',
        ]);

        $company = $this->getCompany($request);
        $service = new PaymentAnalysisReportService($company, $validated);
        
        $report = $service->getReport();

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function taxReport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'tax_type' => 'nullable|string',
        ]);

        $company = $this->getCompany($request);
        $service = new TaxReportService($company, $validated);
        
        $report = $service->getReport();

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    public function drillDown(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'account' => 'required|integer',
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);

        // Placeholder for drill-down logic
        return response()->json([
            'success' => true,
            'data' => [
                'account_id' => $validated['account'],
                'transactions' => [],
            ],
        ]);
    }

    private function getCompany(Request $request): Company
    {
        // Get company from authenticated user or request
        // For now, return first company as placeholder
        return Company::firstOrFail();
    }
}

