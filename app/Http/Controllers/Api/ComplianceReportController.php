<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Models\ComplianceCheck;
use App\Models\ComplianceRequirement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ComplianceReportController extends Controller
{
    /**
     * Get certification register report.
     */
    public function certificationRegister(Request $request): JsonResponse
    {
        $query = Certification::with(['company', 'currency', 'renewals']);

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('from_date')) {
            $query->where('issue_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('issue_date', '<=', $request->to_date);
        }

        $certifications = $query->latest()->get();

        $summary = [
            'total' => $certifications->count(),
            'active' => $certifications->where('status', 'active')->count(),
            'expired' => $certifications->where('status', 'expired')->count(),
            'pending_renewal' => $certifications->where('status', 'pending_renewal')->count(),
            'total_cost' => $certifications->sum('cost'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'certifications' => $certifications,
                'summary' => $summary,
            ],
        ]);
    }

    /**
     * Get compliance status report.
     */
    public function complianceStatus(Request $request): JsonResponse
    {
        $query = ComplianceCheck::with(['complianceRequirement', 'project', 'checkedBy']);

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('from_date')) {
            $query->where('check_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('check_date', '<=', $request->to_date);
        }

        $checks = $query->latest()->get();

        $summary = [
            'total_checks' => $checks->count(),
            'passed' => $checks->where('status', 'passed')->count(),
            'failed' => $checks->where('status', 'failed')->count(),
            'pending' => $checks->where('status', 'pending')->count(),
            'waived' => $checks->where('status', 'waived')->count(),
            'overdue' => $checks->filter(fn($check) => $check->is_overdue)->count(),
            'pass_rate' => $checks->count() > 0 ? 
                round(($checks->where('status', 'passed')->count() / $checks->count()) * 100, 2) : 0,
        ];

        // Group by requirement
        $byRequirement = $checks->groupBy('compliance_requirement_id')->map(function ($group) {
            return [
                'requirement' => $group->first()->complianceRequirement->name ?? 'Unknown',
                'total' => $group->count(),
                'passed' => $group->where('status', 'passed')->count(),
                'failed' => $group->where('status', 'failed')->count(),
                'pending' => $group->where('status', 'pending')->count(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'checks' => $checks,
                'summary' => $summary,
                'by_requirement' => $byRequirement,
            ],
        ]);
    }

    /**
     * Get expiry calendar report.
     */
    public function expiryCalendar(Request $request): JsonResponse
    {
        $months = $request->get('months', 12);
        $companyId = $request->get('company_id');

        $query = Certification::with(['company', 'currency']);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $query->where('expiry_date', '>=', Carbon::now())
            ->where('expiry_date', '<=', Carbon::now()->addMonths($months))
            ->where('status', 'active');

        $certifications = $query->orderBy('expiry_date')->get();

        // Group by month
        $calendar = $certifications->groupBy(function ($cert) {
            return $cert->expiry_date->format('Y-m');
        })->map(function ($group, $month) {
            return [
                'month' => $month,
                'count' => $group->count(),
                'certifications' => $group->map(function ($cert) {
                    return [
                        'id' => $cert->id,
                        'name' => $cert->name,
                        'certification_number' => $cert->certification_number,
                        'type' => $cert->type,
                        'expiry_date' => $cert->expiry_date,
                        'days_until_expiry' => $cert->days_until_expiry,
                    ];
                }),
            ];
        })->values();

        $summary = [
            'total_expiring' => $certifications->count(),
            'expiring_30_days' => $certifications->filter(fn($c) => $c->days_until_expiry <= 30)->count(),
            'expiring_60_days' => $certifications->filter(fn($c) => $c->days_until_expiry <= 60)->count(),
            'expiring_90_days' => $certifications->filter(fn($c) => $c->days_until_expiry <= 90)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'calendar' => $calendar,
                'summary' => $summary,
            ],
        ]);
    }
}
