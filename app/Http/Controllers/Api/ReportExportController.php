<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ReportHistory;
use App\Services\Exports\PdfExportService;
use App\Services\Exports\ExcelExportService;
use App\Services\Exports\CsvExportService;
use App\Services\Reports\TrialBalanceReportService;
use App\Services\Reports\BalanceSheetReportService;
use App\Services\Reports\IncomeStatementReportService;
use App\Services\Reports\GeneralLedgerReportService;
use App\Services\Reports\AccountsPayableAgingReportService;
use App\Services\Reports\AccountsReceivableAgingReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportExportController extends Controller
{
    private PdfExportService $pdfExporter;
    private ExcelExportService $excelExporter;
    private CsvExportService $csvExporter;

    public function __construct()
    {
        $this->pdfExporter = new PdfExportService();
        $this->excelExporter = new ExcelExportService();
        $this->csvExporter = new CsvExportService();
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|string',
            'format' => 'required|in:pdf,excel,csv',
            'parameters' => 'nullable|array',
        ]);

        $reportType = $validated['report_type'];
        $format = $validated['format'];
        $parameters = $validated['parameters'] ?? [];

        $company = $this->getCompany($request);
        
        // Generate report data
        $service = $this->getReportService($reportType, $company, $parameters);
        $reportData = $service->getReport();
        
        // Convert to export format
        $exportData = $this->prepareExportData($reportData);

        // Export based on format
        $filePath = match($format) {
            'pdf' => $this->pdfExporter->export($exportData, $reportType),
            'excel' => $this->excelExporter->export($exportData, $reportType),
            'csv' => $this->csvExporter->export($exportData, $reportType),
        };

        // Save to history
        $service->saveToHistory($filePath, $format, auth()->id() ?? 1);

        return response()->json([
            'success' => true,
            'message' => 'Report exported successfully',
            'file_path' => $filePath,
            'download_url' => Storage::url($filePath),
        ]);
    }

    public function download(Request $request, int $historyId)
    {
        $history = ReportHistory::findOrFail($historyId);
        
        if (!Storage::exists($history->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'Report file not found',
            ], 404);
        }

        return Storage::download($history->file_path);
    }

    private function getReportService(string $reportType, Company $company, array $parameters)
    {
        return match($reportType) {
            'trial_balance' => new TrialBalanceReportService($company, $parameters),
            'balance_sheet' => new BalanceSheetReportService($company, $parameters),
            'income_statement' => new IncomeStatementReportService($company, $parameters),
            'general_ledger' => new GeneralLedgerReportService($company, $parameters),
            'ap_aging' => new AccountsPayableAgingReportService($company, $parameters),
            'ar_aging' => new AccountsReceivableAgingReportService($company, $parameters),
            default => throw new \InvalidArgumentException("Unknown report type: {$reportType}"),
        };
    }

    private function prepareExportData(array $reportData): array
    {
        // Convert report data to a format suitable for export
        $exportData = [
            'title' => $reportData['title'] ?? 'Financial Report',
            'headers' => $reportData['headers'] ?? [],
            'rows' => [],
        ];

        // Handle different report structures
        if (isset($reportData['accounts'])) {
            foreach ($reportData['accounts'] as $account) {
                $exportData['rows'][] = array_values($account);
            }
        } elseif (isset($reportData['transactions'])) {
            foreach ($reportData['transactions'] as $transaction) {
                $exportData['rows'][] = array_values($transaction);
            }
        } elseif (isset($reportData['vendors'])) {
            foreach ($reportData['vendors'] as $vendor) {
                $exportData['rows'][] = array_values($vendor);
            }
        } elseif (isset($reportData['customers'])) {
            foreach ($reportData['customers'] as $customer) {
                $exportData['rows'][] = array_values($customer);
            }
        }

        return $exportData;
    }

    private function getCompany(Request $request): Company
    {
        // Get company from authenticated user or request
        return Company::firstOrFail();
    }
}

