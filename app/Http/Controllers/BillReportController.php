<?php

namespace App\Http\Controllers;

use App\Models\ProgressBill;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BillReportController extends Controller
{
    public function billingSummary(int $projectId): JsonResponse
    {
        $project = Project::findOrFail($projectId);
        
        $bills = ProgressBill::where('project_id', $projectId)
            ->with(['contract', 'currency'])
            ->orderBy('bill_sequence')
            ->get();

        $summary = [
            'project' => $project,
            'total_bills' => $bills->count(),
            'total_billed' => $bills->sum('current_amount'),
            'total_retention' => $bills->sum('retention_amount'),
            'total_deductions' => $bills->sum('other_deductions'),
            'total_paid' => $bills->where('status', 'paid')->sum('total_payable'),
            'total_pending' => $bills->whereIn('status', ['submitted', 'reviewed', 'certified', 'approved'])->sum('total_payable'),
            'bills' => $bills,
        ];

        return response()->json($summary);
    }

    public function paymentStatus(int $projectId): JsonResponse
    {
        $bills = ProgressBill::where('project_id', $projectId)
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_payable) as total'))
            ->groupBy('status')
            ->get();

        return response()->json($bills);
    }

    public function retentionSummary(int $projectId): JsonResponse
    {
        $bills = ProgressBill::where('project_id', $projectId)
            ->whereIn('status', ['approved', 'paid'])
            ->get();

        $summary = [
            'total_retention_held' => $bills->sum('retention_amount'),
            'cumulative_retention' => $bills->sum('cumulative_retention'),
            'retention_released' => $bills->where('bill_type', 'retention_release')->sum('total_payable'),
            'retention_pending' => $bills->sum('retention_amount') - $bills->where('bill_type', 'retention_release')->sum('total_payable'),
            'bills' => $bills,
        ];

        return response()->json($summary);
    }

    public function billingForecast(int $projectId): JsonResponse
    {
        $project = Project::with('contract')->findOrFail($projectId);
        
        $bills = ProgressBill::where('project_id', $projectId)
            ->whereIn('status', ['approved', 'paid'])
            ->get();

        $totalBilled = $bills->sum('current_amount');
        $contractValue = $project->contract->contract_value ?? 0;
        
        $forecast = [
            'contract_value' => $contractValue,
            'total_billed' => $totalBilled,
            'percentage_billed' => $contractValue > 0 ? ($totalBilled / $contractValue) * 100 : 0,
            'remaining_value' => $contractValue - $totalBilled,
            'average_bill_amount' => $bills->count() > 0 ? $totalBilled / $bills->count() : 0,
            'estimated_bills_remaining' => $bills->count() > 0 && $totalBilled > 0 
                ? ceil(($contractValue - $totalBilled) / ($totalBilled / $bills->count())) 
                : 0,
        ];

        return response()->json($forecast);
    }

    public function cashFlow(int $projectId): JsonResponse
    {
        $bills = ProgressBill::where('project_id', $projectId)
            ->orderBy('bill_date')
            ->get();

        $cashFlow = $bills->map(function ($bill) {
            return [
                'date' => $bill->bill_date,
                'bill_number' => $bill->bill_number,
                'status' => $bill->status,
                'gross_amount' => $bill->current_amount,
                'retention' => $bill->retention_amount,
                'deductions' => $bill->other_deductions + $bill->advance_recovery_amount,
                'net_amount' => $bill->net_amount,
                'vat' => $bill->vat_amount,
                'total_payable' => $bill->total_payable,
                'paid_amount' => $bill->status === 'paid' ? $bill->total_payable : 0,
            ];
        });

        $summary = [
            'total_billed' => $bills->sum('current_amount'),
            'total_retention' => $bills->sum('retention_amount'),
            'total_deductions' => $bills->sum('other_deductions') + $bills->sum('advance_recovery_amount'),
            'total_payable' => $bills->sum('total_payable'),
            'total_paid' => $bills->where('status', 'paid')->sum('total_payable'),
            'total_outstanding' => $bills->whereIn('status', ['approved', 'certified'])->sum('total_payable'),
            'cash_flow' => $cashFlow,
        ];

        return response()->json($summary);
    }

    public function agingReport(): JsonResponse
    {
        $bills = ProgressBill::whereIn('status', ['approved', 'certified'])
            ->with(['project', 'contract'])
            ->orderBy('approved_at')
            ->get();

        $aging = $bills->map(function ($bill) {
            $daysOutstanding = $bill->approved_at ? now()->diffInDays($bill->approved_at) : 0;
            
            if ($daysOutstanding <= 30) {
                $ageBucket = '0-30 days';
            } elseif ($daysOutstanding <= 60) {
                $ageBucket = '31-60 days';
            } elseif ($daysOutstanding <= 90) {
                $ageBucket = '61-90 days';
            } else {
                $ageBucket = '90+ days';
            }

            return [
                'bill_number' => $bill->bill_number,
                'project' => $bill->project->name ?? 'N/A',
                'approved_at' => $bill->approved_at,
                'days_outstanding' => $daysOutstanding,
                'age_bucket' => $ageBucket,
                'amount' => $bill->total_payable,
                'status' => $bill->status,
            ];
        });

        $summary = [
            '0-30 days' => $aging->where('age_bucket', '0-30 days')->sum('amount'),
            '31-60 days' => $aging->where('age_bucket', '31-60 days')->sum('amount'),
            '61-90 days' => $aging->where('age_bucket', '61-90 days')->sum('amount'),
            '90+ days' => $aging->where('age_bucket', '90+ days')->sum('amount'),
            'total_outstanding' => $aging->sum('amount'),
            'bills' => $aging,
        ];

        return response()->json($summary);
    }
}
