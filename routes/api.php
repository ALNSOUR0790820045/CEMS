<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApInvoiceController;
use App\Http\Controllers\Api\ApPaymentController;
use App\Http\Controllers\Api\ApReportController;
use App\Http\Controllers\Api\ARInvoiceController;
use App\Http\Controllers\Api\ARReceiptController;
use App\Http\Controllers\Api\ARReportController;
use App\Http\Controllers\Api\GRNController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\StockTransferController;
use App\Http\Controllers\Api\InventoryReportController;
use App\Http\Controllers\PayrollPeriodController;
use App\Http\Controllers\PayrollEntryController;
use App\Http\Controllers\EmployeeLoanController;
use App\Http\Controllers\WpsExportController;
use App\Http\Controllers\VariationOrderController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\UserRoleController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\WarehouseLocationController;
use App\Http\Controllers\Api\WarehouseStockController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\ShiftScheduleController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\AgedReportController;
use App\Http\Controllers\Api\CustomReportController;
use App\Http\Controllers\Api\ProjectReportController;
use App\Http\Controllers\Api\CertificationController;
use App\Http\Controllers\Api\ComplianceRequirementController;
use App\Http\Controllers\Api\ComplianceCheckController;
use App\Http\Controllers\Api\ComplianceReportController;

Route::middleware('auth: sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    
    // AP Invoices
    Route::prefix('ap-invoices')->group(function () {
        Route::get('/', [ApInvoiceController::class, 'index']);
        Route::post('/', [ApInvoiceController::class, 'store']);
        Route::get('/{invoice}', [ApInvoiceController::class, 'show']);
        Route::put('/{invoice}', [ApInvoiceController::class, 'update']);
        Route::delete('/{invoice}', [ApInvoiceController::class, 'destroy']);
        Route::post('/{invoice}/approve', [ApInvoiceController::class, 'approve']);
    });

    // AP Payments
    Route::prefix('ap-payments')->group(function () {
        Route::get('/', [ApPaymentController:: class, 'index']);
        Route::post('/', [ApPaymentController::class, 'store']);
        Route::get('/{payment}', [ApPaymentController::class, 'show']);
        Route::put('/{payment}', [ApPaymentController::class, 'update']);
        Route::delete('/{payment}', [ApPaymentController:: class, 'destroy']);
        Route::post('/{payment}/allocate', [ApPaymentController::class, 'allocate']);
    });

    // AP Reports
    Route::prefix('ap-reports')->group(function () {
        Route::get('/aging', [ApReportController::class, 'aging']);
        Route::get('/vendor-balance', [ApReportController::class, 'vendorBalance']);
        Route::get('/payment-history', [ApReportController::class, 'paymentHistory']);
        Route::get('/cash-flow-forecast', [ApReportController::class, 'cashFlowForecast']);
    });

    // AR Invoices
    Route::apiResource('ar-invoices', ARInvoiceController:: class);
    Route::post('ar-invoices/{id}/send', [ARInvoiceController::class, 'send']);

    // AR Receipts
    Route:: apiResource('ar-receipts', ARReceiptController::class);
    Route::post('ar-receipts/{id}/allocate', [ARReceiptController::class, 'allocate']);

    // AR Reports
    Route::get('ar-reports/aging', [ARReportController::class, 'aging']);
    Route::get('ar-reports/client-balance', [ARReportController::class, 'clientBalance']);
    Route::get('ar-reports/collection-forecast', [ARReportController::class, 'collectionForecast']);
    Route::get('ar-reports/dso', [ARReportController::class, 'dso']);

    // GRN Routes
    Route::get('grns/pending-inspection', [GRNController::class, 'pendingInspection']);
    Route::post('grns/{id}/inspect', [GRNController::class, 'inspect']);
    Route::post('grns/{id}/accept', [GRNController::class, 'accept']);
    Route::apiResource('grns', GRNController::class);
    
    // Roles Management
    Route::middleware('permission:manage-roles')->group(function () {
        Route::get('/roles', [RoleController::class, 'index']);
        Route::post('/roles', [RoleController::class, 'store']);
        Route::get('/roles/{id}', [RoleController::class, 'show']);
        Route::put('/roles/{id}', [RoleController::class, 'update']);
        Route::post('/roles/{id}/permissions', [RoleController::class, 'assignPermissions']);
    });

    // Permissions Management
    Route::middleware('permission: manage-permissions')->group(function () {
        Route::get('/permissions', [PermissionController::class, 'index']);
        Route::post('/permissions', [PermissionController::class, 'store']);
    });

    // User Role Assignment
    Route::middleware('permission:assign-roles')->group(function () {
        Route:: post('/users/{id}/assign-role', [UserRoleController::class, 'assignRole']);
    });

    // Documents Management
    Route:: prefix('documents')->group(function () {
        Route::get('/', [DocumentController::class, 'index']);
        Route::post('/', [DocumentController::class, 'store']);
        Route::get('/search', [DocumentController:: class, 'search']);
        Route::get('/{document}', [DocumentController::class, 'show']);
        Route::put('/{document}', [DocumentController::class, 'update']);
        Route::patch('/{document}', [DocumentController::class, 'update']);
        Route::delete('/{document}', [DocumentController:: class, 'destroy']);
        Route::post('/{document}/upload-version', [DocumentController:: class, 'uploadVersion']);
        Route::get('/{document}/versions', [DocumentController:: class, 'versions']);
        Route::post('/{document}/grant-access', [DocumentController::class, 'grantAccess']);
    });
    
    // Warehouse Management
    Route:: apiResource('warehouses', WarehouseController::class);
    Route::apiResource('warehouse-locations', WarehouseLocationController:: class);
    
    Route::get('warehouse-stock', [WarehouseStockController:: class, 'index']);
    Route::get('warehouse-stock/availability', [WarehouseStockController:: class, 'availability']);
    Route::post('warehouse-stock/transfer', [WarehouseStockController:: class, 'transfer']);
    
    // Branch Management
    Route:: apiResource('branches', BranchController:: class);
    Route::get('branches/{branch}/users', [BranchController::class, 'users']);
    
    // Employee Management
    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'index']);
        Route::post('/', [EmployeeController::class, 'store']);
        Route::get('/{id}', [EmployeeController::class, 'show']);
        Route::put('/{id}', [EmployeeController:: class, 'update']);
        Route::delete('/{id}', [EmployeeController::class, 'destroy']);
    });
    
    // Attendance Management
    Route:: prefix('attendance')->group(function () {
        Route::get('/', [AttendanceController::class, 'index']);
        Route::post('/', [AttendanceController:: class, 'store']);
        Route::get('/{id}', [AttendanceController::class, 'show']);
        Route::put('/{id}', [AttendanceController::class, 'update']);
        Route::delete('/{id}', [AttendanceController::class, 'destroy']);
        Route::post('/check-in', [AttendanceController::class, 'checkIn']);
        Route::post('/check-out', [AttendanceController::class, 'checkOut']);
    });

    // Leave Requests
    Route:: prefix('leave-requests')->group(function () {
        Route::get('/', [LeaveRequestController::class, 'index']);
        Route::post('/', [LeaveRequestController::class, 'store']);
        Route::get('/{id}', [LeaveRequestController::class, 'show']);
        Route::put('/{id}', [LeaveRequestController::class, 'update']);
        Route::delete('/{id}', [LeaveRequestController::class, 'destroy']);
        Route::post('/{id}/approve', [LeaveRequestController:: class, 'approve']);
        Route::post('/{id}/reject', [LeaveRequestController::class, 'reject']);
        Route::post('/{id}/cancel', [LeaveRequestController::class, 'cancel']);
    });

    // Shift Schedules
    Route::prefix('shift-schedules')->group(function () {
        Route:: get('/', [ShiftScheduleController:: class, 'index']);
        Route::post('/', [ShiftScheduleController:: class, 'store']);
        Route::get('/{id}', [ShiftScheduleController::class, 'show']);
        Route::put('/{id}', [ShiftScheduleController::class, 'update']);
        Route::delete('/{id}', [ShiftScheduleController::class, 'destroy']);
    });

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/attendance-summary', [ReportController::class, 'attendanceSummary']);
        Route::get('/daily-attendance', [ReportController::class, 'dailyAttendance']);
        Route::get('/monthly-attendance', [ReportController::class, 'monthlyAttendance']);
        Route::get('/leave-report', [ReportController::class, 'leaveReport']);
        Route::get('/overtime-report', [ReportController::class, 'overtimeReport']);
        Route::get('/trial-balance', [ReportController::class, 'trialBalance']);
        Route::get('/balance-sheet', [ReportController::class, 'balanceSheet']);
        Route::get('/income-statement', [ReportController::class, 'incomeStatement']);
        Route::get('/cash-flow', [ReportController:: class, 'cashFlow']);
        Route::get('/general-ledger', [ReportController::class, 'generalLedger']);
        Route::get('/account-statement', [ReportController::class, 'accountStatement']);
        Route::get('/ap-aging', [AgedReportController:: class, 'accountsPayableAging']);
        Route::get('/ar-aging', [AgedReportController:: class, 'accountsReceivableAging']);
        Route::get('/vendor-outstanding', [AgedReportController:: class, 'vendorOutstanding']);
        Route::get('/customer-outstanding', [AgedReportController:: class, 'customerOutstanding']);
        Route::get('/project-profitability', [ProjectReportController::class, 'profitability']);
        Route::get('/project-cost-analysis', [ProjectReportController::class, 'costAnalysis']);
        Route::get('/budget-vs-actual', [ProjectReportController::class, 'budgetVsActual']);
        Route::get('/project-cash-flow', [ProjectReportController::class, 'cashFlow']);
        Route::get('/cost-performance-index', [ProjectReportController::class, 'costPerformanceIndex']);
        Route::get('/executive-dashboard', [ReportController::class, 'executiveDashboard']);
        Route::get('/kpi-metrics', [ReportController::class, 'kpiMetrics']);
        Route::get('/revenue-analysis', [ReportController::class, 'revenueAnalysis']);
        Route::get('/expense-analysis', [ReportController::class, 'expenseAnalysis']);
        Route::get('/profitability-analysis', [ReportController::class, 'profitabilityAnalysis']);
        Route::get('/vat-report', [ReportController::class, 'vatReport']);
        Route::get('/withholding-tax-report', [ReportController::class, 'withholdingTaxReport']);
        Route::get('/audit-trail', [ReportController::class, 'auditTrail']);
        Route::post('/custom', [CustomReportController::class, 'generate']);
    });
    
    // Variation Orders
    Route:: get('/variation-orders/statistics', [VariationOrderController::class, 'statistics']);
    Route::get('/variation-orders', [VariationOrderController::class, 'index']);
    Route::post('/variation-orders', [VariationOrderController::class, 'store']);
    Route::get('/variation-orders/{variationOrder}', [VariationOrderController::class, 'show']);
    Route::put('/variation-orders/{variationOrder}', [VariationOrderController::class, 'update']);
    Route::delete('/variation-orders/{variationOrder}', [VariationOrderController::class, 'destroy']);
    Route::post('/variation-orders/{variationOrder}/submit', [VariationOrderController::class, 'submit']);
    Route::post('/variation-orders/{variationOrder}/approve', [VariationOrderController::class, 'approve']);
    Route::post('/variation-orders/{variationOrder}/reject', [VariationOrderController::class, 'reject']);
    Route::get('/variation-orders/{variationOrder}/export', [VariationOrderController::class, 'export']);
    Route::get('/projects/{project}/variation-orders', [VariationOrderController::class, 'byProject']);

    // Payroll
    Route::apiResource('payroll-periods', PayrollPeriodController::class);
    Route::post('payroll-periods/{payrollPeriod}/calculate', [PayrollPeriodController::class, 'calculate']);
    Route::post('payroll-periods/{payrollPeriod}/approve', [PayrollPeriodController::class, 'approve']);

    Route::apiResource('payroll-entries', PayrollEntryController:: class)->except(['destroy']);
    Route::get('payroll-entries/{payrollEntry}/payslip', [PayrollEntryController::class, 'payslip']);

    Route::apiResource('employee-loans', EmployeeLoanController::class);
    Route::post('employee-loans/{employeeLoan}/cancel', [EmployeeLoanController::class, 'cancel']);

    Route::post('payroll/wps-export', [WpsExportController::class, 'export']);
    Route::post('payroll/bank-transfer-list', [WpsExportController:: class, 'generateBankTransferList']);

    // Inventory
    Route::get('/inventory/balance', [InventoryController::class, 'getBalance']);
    Route::get('/inventory/transactions', [InventoryController::class, 'getTransactions']);
    Route::post('/inventory/transactions', [InventoryController::class, 'createTransaction']);

    // Stock Transfers
    Route:: get('/stock-transfers', [StockTransferController::class, 'index']);
    Route::post('/stock-transfers', [StockTransferController:: class, 'store']);
    Route::get('/stock-transfers/{id}', [StockTransferController::class, 'show']);
    Route::post('/stock-transfers/{id}/approve', [StockTransferController::class, 'approve']);
    Route::post('/stock-transfers/{id}/receive', [StockTransferController::class, 'receive']);
    Route::post('/stock-transfers/{id}/cancel', [StockTransferController:: class, 'cancel']);

    // Inventory Reports
    Route::get('/inventory/reports/valuation', [InventoryReportController:: class, 'valuation']);
    Route::get('/inventory/reports/stock-status', [InventoryReportController:: class, 'stockStatus']);
    Route::get('/inventory/reports/movement', [InventoryReportController::class, 'movement']);
    Route::get('/inventory/reports/low-stock', [InventoryReportController::class, 'lowStock']);

    // Certifications
    Route::prefix('certifications')->group(function () {
        Route::get('/expiring', [CertificationController::class, 'expiring']);
        Route::get('/expired', [CertificationController::class, 'expired']);
        Route::post('/{id}/renew', [CertificationController::class, 'renew']);
    });
    Route::apiResource('certifications', CertificationController::class);

    // Compliance Requirements
    Route::apiResource('compliance-requirements', ComplianceRequirementController::class);

    // Compliance Checks
    Route::prefix('compliance-checks')->group(function () {
        Route::post('/{id}/pass', [ComplianceCheckController::class, 'pass']);
        Route::post('/{id}/fail', [ComplianceCheckController::class, 'fail']);
    });
    Route::apiResource('compliance-checks', ComplianceCheckController::class);

    // Compliance Reports
    Route::get('/reports/certification-register', [ComplianceReportController::class, 'certificationRegister']);
    Route::get('/reports/compliance-status', [ComplianceReportController::class, 'complianceStatus']);
    Route::get('/reports/expiry-calendar', [ComplianceReportController::class, 'expiryCalendar']);
});