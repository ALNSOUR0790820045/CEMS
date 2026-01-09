<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Transaction;
use App\Models\AttendanceRecord;
use App\Models\LeaveRequest;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // ============================================
    // FINANCIAL REPORTS
    // ============================================

    /**
     * Get Trial Balance Report
     */
    public function trialBalance(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'company_id' => 'nullable|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth());
        $companyId = $request->input('company_id');
        $departmentId = $request->input('department_id');

        $query = Account::query()
            ->with('department')
            ->where('is_active', true);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $accounts = $query->get()->map(function ($account) use ($dateFrom, $dateTo) {
            $transactions = Transaction::where('account_id', $account->id)
                ->whereHas('journalEntry', function ($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'posted')
                        ->whereBetween('entry_date', [$dateFrom, $dateTo]);
                })
                ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
                ->first();

            $debit = (float) ($transactions->total_debit ?? 0);
            $credit = (float) ($transactions->total_credit ?? 0);
            $balance = $debit - $credit;

            return [
                'account_code' => $account->code,
                'account_name' => $account->name,
                'account_type' => $account->type,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $balance,
                'debit_formatted' => number_format($debit, 2),
                'credit_formatted' => number_format($credit, 2),
                'balance_formatted' => number_format($balance, 2),
            ];
        })->filter(function ($item) {
            return $item['debit'] != 0 || $item['credit'] != 0;
        })->values();

        $totalDebit = $accounts->sum('debit');
        $totalCredit = $accounts->sum('credit');

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'accounts' => $accounts,
                'totals' => [
                    'debit' => number_format($totalDebit, 2),
                    'credit' => number_format($totalCredit, 2),
                    'difference' => number_format($totalDebit - $totalCredit, 2),
                ],
            ],
        ]);
    }

    /**
     * Get Balance Sheet Report
     */
    public function balanceSheet(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'nullable|date',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $date = $request->input('date', Carbon::now());
        $companyId = $request->input('company_id');

        $query = Account::query()->where('is_active', true);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $accounts = $query->get();

        $assets = $this->calculateAccountTypeBalance($accounts->where('type', 'asset'), $date);
        $liabilities = $this->calculateAccountTypeBalance($accounts->where('type', 'liability'), $date);
        $equity = $this->calculateAccountTypeBalance($accounts->where('type', 'equity'), $date);

        return response()->json([
            'status' => 'success',
            'data' => [
                'date' => $date,
                'assets' => [
                    'current' => $assets['current'],
                    'non_current' => $assets['non_current'],
                    'total' => $assets['total'],
                ],
                'liabilities' => [
                    'current' => $liabilities['current'],
                    'non_current' => $liabilities['non_current'],
                    'total' => $liabilities['total'],
                ],
                'equity' => [
                    'total' => $equity['total'],
                ],
                'total_liabilities_and_equity' => number_format($liabilities['total'] + $equity['total'], 2),
            ],
        ]);
    }

    /**
     * Get Income Statement (P&L) Report
     */
    public function incomeStatement(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth());
        $companyId = $request->input('company_id');

        $query = Account::query()->where('is_active', true);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $accounts = $query->get();

        $revenue = $this->calculateAccountTypeBalance(
            $accounts->where('type', 'revenue'),
            $dateTo,
            $dateFrom
        );

        $expenses = $this->calculateAccountTypeBalance(
            $accounts->where('type', 'expense'),
            $dateTo,
            $dateFrom
        );

        $netIncome = $revenue['total'] - $expenses['total'];

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'revenue' => [
                    'operating' => $revenue['operating'] ?? 0,
                    'non_operating' => $revenue['non_operating'] ?? 0,
                    'total' => $revenue['total'],
                ],
                'expenses' => [
                    'operating' => $expenses['operating'] ?? 0,
                    'non_operating' => $expenses['non_operating'] ?? 0,
                    'total' => $expenses['total'],
                ],
                'net_income' => number_format($netIncome, 2),
            ],
        ]);
    }

    /**
     * Get Cash Flow Statement
     */
    public function cashFlow(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth());

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'operating_activities' => [
                    'cash_from_operations' => 0,
                    'cash_paid_to_suppliers' => 0,
                    'net_cash_from_operating' => 0,
                ],
                'investing_activities' => [
                    'purchase_of_assets' => 0,
                    'sale_of_assets' => 0,
                    'net_cash_from_investing' => 0,
                ],
                'financing_activities' => [
                    'loans_received' => 0,
                    'loans_repaid' => 0,
                    'net_cash_from_financing' => 0,
                ],
                'net_increase_in_cash' => 0,
                'cash_at_beginning' => 0,
                'cash_at_end' => 0,
            ],
        ]);
    }

    /**
     * Get General Ledger Report
     */
    public function generalLedger(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'account_id' => 'nullable|exists: accounts,id',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth());
        $accountId = $request->input('account_id');

        $query = Transaction::query()
            ->with(['account', 'journalEntry'])
            ->whereHas('journalEntry', function ($q) use ($dateFrom, $dateTo) {
                $q->where('status', 'posted')
                    ->whereBetween('entry_date', [$dateFrom, $dateTo]);
            });

        if ($accountId) {
            $query->where('account_id', $accountId);
        }

        $transactions = $query->orderBy('id')->get()->map(function ($transaction) {
            return [
                'date' => $transaction->journalEntry->entry_date,
                'entry_number' => $transaction->journalEntry->entry_number,
                'account' => $transaction->account->name,
                'description' => $transaction->description ??  $transaction->journalEntry->description,
                'debit' => number_format($transaction->debit, 2),
                'credit' => number_format($transaction->credit, 2),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'transactions' => $transactions,
            ],
        ]);
    }

    /**
     * Get Account Statement
     */
    public function accountStatement(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $accountId = $request->input('account_id');
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth());

        $account = Account::findOrFail($accountId);

        $transactions = Transaction::where('account_id', $accountId)
            ->with('journalEntry')
            ->whereHas('journalEntry', function ($q) use ($dateFrom, $dateTo) {
                $q->where('status', 'posted')
                    ->whereBetween('entry_date', [$dateFrom, $dateTo]);
            })
            ->orderBy('id')
            ->get();

        $balance = 0;
        $statement = $transactions->map(function ($transaction) use (&$balance) {
            $balance += $transaction->debit - $transaction->credit;

            return [
                'date' => $transaction->journalEntry->entry_date,
                'entry_number' => $transaction->journalEntry->entry_number,
                'description' => $transaction->description ?? $transaction->journalEntry->description,
                'debit' => number_format($transaction->debit, 2),
                'credit' => number_format($transaction->credit, 2),
                'balance' => number_format($balance, 2),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'account' => [
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                ],
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'statement' => $statement,
                'ending_balance' => number_format($balance, 2),
            ],
        ]);
    }

    /**
     * Get Audit Trail
     */
    public function auditTrail(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth());

        $entries = JournalEntry::with(['creator', 'approver'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($entry) {
                return [
                    'entry_number' => $entry->entry_number,
                    'date' => $entry->entry_date,
                    'type' => $entry->type,
                    'status' => $entry->status,
                    'created_by' => $entry->creator->name ??  null,
                    'approved_by' => $entry->approver->name ?? null,
                    'created_at' => $entry->created_at,
                    'approved_at' => $entry->approved_at,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'entries' => $entries,
            ],
        ]);
    }

    // ============================================
    // HR/ATTENDANCE REPORTS
    // ============================================

    /**
     * Get attendance summary report. 
     */
    public function attendanceSummary(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $query = AttendanceRecord::with(['employee. user'])
            ->forCompany($request->company_id)
            ->forDateRange($request->start_date, $request->end_date);

        if ($request->has('employee_id')) {
            $query->forEmployee($request->employee_id);
        }

        $records = $query->get();

        $summary = [
            'total_days' => $records->count(),
            'present_days' => $records->where('status', 'present')->count(),
            'absent_days' => $records->where('status', 'absent')->count(),
            'on_leave_days' => $records->where('status', 'on_leave')->count(),
            'half_days' => $records->where('status', 'half_day')->count(),
            'total_work_hours' => round($records->sum('work_hours'), 2),
            'total_overtime_hours' => round($records->sum('overtime_hours'), 2),
            'total_late_minutes' => $records->sum('late_minutes'),
            'total_early_leave_minutes' => $records->sum('early_leave_minutes'),
            'average_work_hours' => $records->count() > 0 ? round($records->avg('work_hours'), 2) : 0,
        ];

        return response()->json([
            'summary' => $summary,
            'records' => $records,
            'period' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]
        ]);
    }

    /**
     * Get daily attendance report.
     */
    public function dailyAttendance(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'date' => 'nullable|date',
        ]);

        $date = $request->date ?? Carbon::today()->format('Y-m-d');

        $records = AttendanceRecord::with(['employee.user', 'employee.shiftSchedule'])
            ->forCompany($request->company_id)
            ->forDate($date)
            ->get();

        $allEmployees = Employee::active()
            ->forCompany($request->company_id)
            ->with('user')
            ->get();

        $recordedEmployeeIds = $records->pluck('employee_id')->toArray();
        $absentEmployees = $allEmployees->whereNotIn('id', $recordedEmployeeIds);

        return response()->json([
            'date' => $date,
            'attendance_records' => $records,
            'total_employees' => $allEmployees->count(),
            'present_count' => $records->whereIn('status', ['present', 'half_day'])->count(),
            'absent_count' => $absentEmployees->count(),
            'on_leave_count' => $records->where('status', 'on_leave')->count(),
            'absent_employees' => $absentEmployees->values(),
        ]);
    }

    /**
     * Get monthly attendance summary.
     */
    public function monthlyAttendance(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $startDate = Carbon::create($request->year, $request->month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $records = AttendanceRecord::with(['employee.user'])
            ->forCompany($request->company_id)
            ->forDateRange($startDate, $endDate)
            ->get();

        $employeeStats = $records->groupBy('employee_id')->map(function ($employeeRecords) {
            $employee = $employeeRecords->first()->employee;
            return [
                'employee_id' => $employee->id,
                'employee_name' => $employee->user->name,
                'employee_number' => $employee->employee_number,
                'total_days' => $employeeRecords->count(),
                'present_days' => $employeeRecords->where('status', 'present')->count(),
                'absent_days' => $employeeRecords->where('status', 'absent')->count(),
                'on_leave_days' => $employeeRecords->where('status', 'on_leave')->count(),
                'half_days' => $employeeRecords->where('status', 'half_day')->count(),
                'total_work_hours' => round($employeeRecords->sum('work_hours'), 2),
                'total_overtime_hours' => round($employeeRecords->sum('overtime_hours'), 2),
                'total_late_minutes' => $employeeRecords->sum('late_minutes'),
                'average_work_hours' => round($employeeRecords->avg('work_hours'), 2),
            ];
        })->values();

        return response()->json([
            'period' => [
                'year' => $request->year,
                'month' => $request->month,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'employee_statistics' => $employeeStats,
            'overall_summary' => [
                'total_records' => $records->count(),
                'total_work_hours' => round($records->sum('work_hours'), 2),
                'total_overtime_hours' => round($records->sum('overtime_hours'), 2),
            ]
        ]);
    }

    /**
     * Get leave report.
     */
    public function leaveReport(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:pending,approved,rejected,cancelled',
        ]);

        $query = LeaveRequest::with(['employee.user', 'approvedBy'])
            ->forCompany($request->company_id);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('start_date', [$request->start_date, $request->end_date]);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $leaveRequests = $query->orderBy('start_date', 'desc')->get();

        $leaveTypeStats = $leaveRequests->groupBy('leave_type')->map(function ($leaves, $type) {
            return [
                'leave_type' => $type,
                'count' => $leaves->count(),
                'total_days' => $leaves->sum('total_days'),
                'approved_count' => $leaves->where('status', 'approved')->count(),
                'pending_count' => $leaves->where('status', 'pending')->count(),
                'rejected_count' => $leaves->where('status', 'rejected')->count(),
            ];
        })->values();

        return response()->json([
            'leave_requests' => $leaveRequests,
            'statistics' => [
                'total_requests' => $leaveRequests->count(),
                'total_days' => $leaveRequests->sum('total_days'),
                'by_status' => [
                    'pending' => $leaveRequests->where('status', 'pending')->count(),
                    'approved' => $leaveRequests->where('status', 'approved')->count(),
                    'rejected' => $leaveRequests->where('status', 'rejected')->count(),
                    'cancelled' => $leaveRequests->where('status', 'cancelled')->count(),
                ],
                'by_leave_type' => $leaveTypeStats,
            ]
        ]);
    }

    /**
     * Get overtime report.
     */
    public function overtimeReport(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists: companies,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $records = AttendanceRecord::with(['employee.user'])
            ->forCompany($request->company_id)
            ->forDateRange($request->start_date, $request->end_date)
            ->where('overtime_hours', '>', 0)
            ->get();

        $employeeOvertimeStats = $records->groupBy('employee_id')->map(function ($employeeRecords) {
            $employee = $employeeRecords->first()->employee;
            return [
                'employee_id' => $employee->id,
                'employee_name' => $employee->user->name,
                'employee_number' => $employee->employee_number,
                'department' => $employee->department,
                'total_overtime_hours' => round($employeeRecords->sum('overtime_hours'), 2),
                'overtime_days_count' => $employeeRecords->count(),
                'average_overtime_per_day' => round($employeeRecords->avg('overtime_hours'), 2),
            ];
        })->sortByDesc('total_overtime_hours')->values();

        return response()->json([
            'period' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ],
            'employee_overtime_statistics' => $employeeOvertimeStats,
            'overall_summary' => [
                'total_overtime_hours' => round($records->sum('overtime_hours'), 2),
                'total_employees_with_overtime' => $employeeOvertimeStats->count(),
                'average_overtime_per_employee' => $employeeOvertimeStats->count() > 0 
                    ? round($employeeOvertimeStats->avg('total_overtime_hours'), 2) 
                    : 0,
            ]
        ]);
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Helper:  Calculate balance for account types
     */
    private function calculateAccountTypeBalance($accounts, $dateTo, $dateFrom = null)
    {
        $result = [
            'current' => 0,
            'non_current' => 0,
            'operating' => 0,
            'non_operating' => 0,
            'total' => 0,
        ];

        foreach ($accounts as $account) {
            $query = Transaction::where('account_id', $account->id)
                ->whereHas('journalEntry', function ($q) use ($dateTo, $dateFrom) {
                    $q->where('status', 'posted')
                        ->where('entry_date', '<=', $dateTo);
                    if ($dateFrom) {
                        $q->where('entry_date', '>=', $dateFrom);
                    }
                })
                ->selectRaw('SUM(debit) - SUM(credit) as balance')
                ->first();

            $balance = abs($query->balance ??  0);

            if ($account->category) {
                $result[$account->category] += $balance;
            }

            $result['total'] += $balance;
        }

        foreach ($result as $key => $value) {
            $result[$key] = number_format($value, 2);
        }

        return $result;
    }
}