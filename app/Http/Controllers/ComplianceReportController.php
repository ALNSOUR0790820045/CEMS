<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\ComplianceRequirement;
use App\Models\ComplianceTracking;
use Illuminate\Http\Request;

class ComplianceReportController extends Controller
{
    /**
     * Get compliance dashboard data.
     */
    public function dashboard(Request $request)
    {
        $companyId = $request->input('company_id');

        // Certifications statistics
        $certificationsQuery = Certification::query();
        if ($companyId) {
            $certificationsQuery->where('company_id', $companyId);
        }

        $certifications = [
            'total' => $certificationsQuery->count(),
            'active' => (clone $certificationsQuery)->active()->count(),
            'expiring_soon' => (clone $certificationsQuery)->expiring(30)->count(),
            'expired' => (clone $certificationsQuery)->expired()->count(),
            'by_type' => (clone $certificationsQuery)->selectRaw('certification_type, COUNT(*) as count')
                ->groupBy('certification_type')
                ->pluck('count', 'certification_type'),
        ];

        // Compliance tracking statistics
        $trackingsQuery = ComplianceTracking::query();
        if ($companyId) {
            $trackingsQuery->where('company_id', $companyId);
        }

        $compliance = [
            'total' => $trackingsQuery->count(),
            'pending' => (clone $trackingsQuery)->pending()->count(),
            'in_progress' => (clone $trackingsQuery)->inProgress()->count(),
            'completed' => (clone $trackingsQuery)->completed()->count(),
            'overdue' => (clone $trackingsQuery)->overdue()->count(),
            'by_status' => (clone $trackingsQuery)->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
        ];

        // Requirements statistics
        $requirementsQuery = ComplianceRequirement::query();
        if ($companyId) {
            $requirementsQuery->where('company_id', $companyId);
        }

        $requirements = [
            'total' => $requirementsQuery->count(),
            'mandatory' => (clone $requirementsQuery)->mandatory()->count(),
            'by_type' => (clone $requirementsQuery)->selectRaw('requirement_type, COUNT(*) as count')
                ->groupBy('requirement_type')
                ->pluck('count', 'requirement_type'),
            'by_frequency' => (clone $requirementsQuery)->selectRaw('frequency, COUNT(*) as count')
                ->groupBy('frequency')
                ->pluck('count', 'frequency'),
        ];

        // Recent expiring certifications
        $recentExpiringQuery = Certification::with(['company'])
            ->expiring(30);
        if ($companyId) {
            $recentExpiringQuery->where('company_id', $companyId);
        }
        $recentExpiring = $recentExpiringQuery->orderBy('expiry_date', 'asc')->limit(10)->get();

        // Recent overdue trackings
        $recentOverdueQuery = ComplianceTracking::with(['company', 'complianceRequirement', 'responsiblePerson'])
            ->overdue();
        if ($companyId) {
            $recentOverdueQuery->where('company_id', $companyId);
        }
        $recentOverdue = $recentOverdueQuery->orderBy('due_date', 'asc')->limit(10)->get();

        return response()->json([
            'certifications' => $certifications,
            'compliance' => $compliance,
            'requirements' => $requirements,
            'recent_expiring_certifications' => $recentExpiring,
            'recent_overdue_trackings' => $recentOverdue,
        ]);
    }

    /**
     * Get certification register report.
     */
    public function certificationRegister(Request $request)
    {
        $query = Certification::with(['company'])
            ->when($request->company_id, function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            })
            ->when($request->certification_type, function ($q) use ($request) {
                $q->where('certification_type', $request->certification_type);
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->from_date, function ($q) use ($request) {
                $q->whereDate('issue_date', '>=', $request->from_date);
            })
            ->when($request->to_date, function ($q) use ($request) {
                $q->whereDate('issue_date', '<=', $request->to_date);
            })
            ->orderBy('issue_date', 'desc');

        $certifications = $query->get();

        return response()->json([
            'data' => $certifications,
            'summary' => [
                'total_count' => $certifications->count(),
                'active_count' => $certifications->where('status', 'active')->count(),
                'expired_count' => $certifications->where('status', 'expired')->count(),
            ]
        ]);
    }

    /**
     * Get compliance status report.
     */
    public function complianceStatus(Request $request)
    {
        $query = ComplianceTracking::with(['company', 'complianceRequirement', 'responsiblePerson'])
            ->when($request->company_id, function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->from_date, function ($q) use ($request) {
                $q->whereDate('due_date', '>=', $request->from_date);
            })
            ->when($request->to_date, function ($q) use ($request) {
                $q->whereDate('due_date', '<=', $request->to_date);
            })
            ->orderBy('due_date', 'asc');

        $trackings = $query->get();

        return response()->json([
            'data' => $trackings,
            'summary' => [
                'total_count' => $trackings->count(),
                'completed_count' => $trackings->where('status', 'completed')->count(),
                'overdue_count' => $trackings->where('status', 'overdue')->count(),
                'pending_count' => $trackings->where('status', 'pending')->count(),
            ]
        ]);
    }
}
