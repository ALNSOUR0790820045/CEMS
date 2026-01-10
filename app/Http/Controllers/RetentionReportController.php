<?php

namespace App\Http\Controllers;

use App\Models\Retention;
use App\Models\AdvancePayment;
use App\Models\DefectsLiability;
use App\Models\RetentionGuarantee;
use Illuminate\Http\Request;

class RetentionReportController extends Controller
{
    public function summary(Request $request)
    {
        $query = Retention::with(['project', 'contract', 'currency']);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $retentions = $query->get();

        $summary = [
            'total_retentions' => $retentions->count(),
            'total_retention_amount' => $retentions->sum('total_retention_amount'),
            'total_released_amount' => $retentions->sum('released_amount'),
            'total_balance_amount' => $retentions->sum('balance_amount'),
            'by_status' => [
                'accumulating' => $retentions->where('status', 'accumulating')->count(),
                'held' => $retentions->where('status', 'held')->count(),
                'partially_released' => $retentions->where('status', 'partially_released')->count(),
                'fully_released' => $retentions->where('status', 'fully_released')->count(),
                'forfeited' => $retentions->where('status', 'forfeited')->count(),
            ],
            'by_type' => [
                'performance' => $retentions->where('retention_type', 'performance')->count(),
                'defects_liability' => $retentions->where('retention_type', 'defects_liability')->count(),
                'advance_payment' => $retentions->where('retention_type', 'advance_payment')->count(),
                'materials' => $retentions->where('retention_type', 'materials')->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    public function aging(Request $request)
    {
        $retentions = Retention::with(['project', 'contract'])
            ->where('status', '!=', 'fully_released')
            ->get();

        $aged = $retentions->map(function ($retention) {
            $daysHeld = $retention->created_at->diffInDays(now());
            
            return [
                'retention_number' => $retention->retention_number,
                'project' => $retention->project->name ?? null,
                'contract' => $retention->contract->contract_number ?? null,
                'balance_amount' => $retention->balance_amount,
                'days_held' => $daysHeld,
                'aging_category' => $this->getAgingCategory($daysHeld),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $aged
        ]);
    }

    public function advanceBalance(Request $request)
    {
        $query = AdvancePayment::with(['project', 'contract', 'currency']);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $advances = $query->where('status', '!=', 'fully_recovered')->get();

        $summary = [
            'total_advances' => $advances->count(),
            'total_advance_amount' => $advances->sum('advance_amount'),
            'total_recovered_amount' => $advances->sum('recovered_amount'),
            'total_balance_amount' => $advances->sum('balance_amount'),
            'advances' => $advances,
        ];

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    public function dlpStatus(Request $request)
    {
        $query = DefectsLiability::with(['project', 'contract']);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $dlps = $query->get();

        $summary = [
            'total_dlps' => $dlps->count(),
            'active' => $dlps->where('status', 'active')->count(),
            'extended' => $dlps->where('status', 'extended')->count(),
            'completed' => $dlps->where('status', 'completed')->count(),
            'total_defects_reported' => $dlps->sum('defects_reported'),
            'total_defects_rectified' => $dlps->sum('defects_rectified'),
            'dlps' => $dlps->map(function ($dlp) {
                return [
                    'id' => $dlp->id,
                    'project' => $dlp->project->name ?? null,
                    'contract' => $dlp->contract->contract_number ?? null,
                    'dlp_end_date' => $dlp->dlp_end_date,
                    'days_remaining' => $dlp->dlp_end_date->diffInDays(now(), false),
                    'status' => $dlp->status,
                    'defects_reported' => $dlp->defects_reported,
                    'defects_rectified' => $dlp->defects_rectified,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    public function guaranteeExpiry(Request $request)
    {
        $days = $request->input('days', 60);
        $expiryDate = now()->addDays($days);

        $guarantees = RetentionGuarantee::with(['retention', 'currency'])
            ->where('status', 'active')
            ->where('expiry_date', '<=', $expiryDate)
            ->where('expiry_date', '>=', now())
            ->orderBy('expiry_date')
            ->get();

        $summary = [
            'total_expiring' => $guarantees->count(),
            'total_amount' => $guarantees->sum('amount'),
            'guarantees' => $guarantees->map(function ($guarantee) {
                return [
                    'guarantee_number' => $guarantee->guarantee_number,
                    'retention_number' => $guarantee->retention->retention_number ?? null,
                    'guarantee_type' => $guarantee->guarantee_type,
                    'amount' => $guarantee->amount,
                    'expiry_date' => $guarantee->expiry_date,
                    'days_to_expiry' => $guarantee->expiry_date->diffInDays(now()),
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    public function releaseForecast(Request $request)
    {
        $months = $request->input('months', 6);
        $forecastDate = now()->addMonths($months);

        $retentions = Retention::with(['project', 'contract'])
            ->where('status', '!=', 'fully_released')
            ->whereNotNull('dlp_end_date')
            ->where('dlp_end_date', '<=', $forecastDate)
            ->orderBy('dlp_end_date')
            ->get();

        $forecast = $retentions->map(function ($retention) {
            return [
                'retention_number' => $retention->retention_number,
                'project' => $retention->project->name ?? null,
                'balance_amount' => $retention->balance_amount,
                'expected_release_date' => $retention->dlp_end_date,
                'months_until_release' => $retention->dlp_end_date->diffInMonths(now()),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'total_forecast_releases' => $forecast->count(),
                'total_forecast_amount' => $retentions->sum('balance_amount'),
                'forecast' => $forecast,
            ]
        ]);
    }

    private function getAgingCategory($days)
    {
        if ($days <= 90) {
            return '0-90 days';
        } elseif ($days <= 180) {
            return '91-180 days';
        } elseif ($days <= 365) {
            return '181-365 days';
        } else {
            return 'Over 1 year';
        }
    }
}
