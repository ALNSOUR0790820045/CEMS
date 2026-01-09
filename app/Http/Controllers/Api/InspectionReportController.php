<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use App\Models\InspectionAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InspectionReportController extends Controller
{
    public function summary(Request $request, $projectId): JsonResponse
    {
        $query = Inspection::where('project_id', $projectId);

        if ($request->has('date_from')) {
            $query->where('inspection_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('inspection_date', '<=', $request->date_to);
        }

        $total = $query->count();
        $passed = (clone $query)->where('result', 'pass')->count();
        $failed = (clone $query)->where('result', 'fail')->count();
        $conditional = (clone $query)->where('result', 'conditional')->count();
        
        $byType = (clone $query)->select('inspection_type_id', DB::raw('count(*) as count'))
            ->groupBy('inspection_type_id')
            ->with('inspectionType')
            ->get();

        $byStatus = (clone $query)->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_inspections' => $total,
                'passed' => $passed,
                'failed' => $failed,
                'conditional' => $conditional,
                'pass_rate' => $total > 0 ? round(($passed / $total) * 100, 2) : 0,
                'by_type' => $byType,
                'by_status' => $byStatus,
            ],
        ]);
    }

    public function log(Request $request, $projectId): JsonResponse
    {
        $query = Inspection::where('project_id', $projectId)
            ->with(['inspectionType', 'inspector', 'witness']);

        if ($request->has('date_from')) {
            $query->where('inspection_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('inspection_date', '<=', $request->date_to);
        }

        if ($request->has('inspection_type_id')) {
            $query->where('inspection_type_id', $request->inspection_type_id);
        }

        $inspections = $query->latest('inspection_date')->get();

        return response()->json([
            'success' => true,
            'data' => $inspections,
        ]);
    }

    public function passRate(Request $request, $projectId): JsonResponse
    {
        $query = Inspection::where('project_id', $projectId)
            ->whereNotNull('result');

        if ($request->has('date_from')) {
            $query->where('inspection_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('inspection_date', '<=', $request->date_to);
        }

        $byMonth = $query->select(
            DB::raw('YEAR(inspection_date) as year'),
            DB::raw('MONTH(inspection_date) as month'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN result = "pass" THEN 1 ELSE 0 END) as passed')
        )
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get()
        ->map(function ($item) {
            $item->pass_rate = $item->total > 0 ? round(($item->passed / $item->total) * 100, 2) : 0;
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $byMonth,
        ]);
    }

    public function pendingActions(Request $request, $projectId): JsonResponse
    {
        $actions = InspectionAction::whereHas('inspection', function ($query) use ($projectId) {
            $query->where('project_id', $projectId);
        })
        ->whereIn('status', ['pending', 'in_progress'])
        ->with(['inspection.inspectionType', 'assignedTo', 'inspectionItem'])
        ->orderBy('due_date')
        ->get();

        $overdue = $actions->filter(function ($action) {
            return $action->due_date < now();
        });

        return response()->json([
            'success' => true,
            'data' => [
                'total_pending' => $actions->count(),
                'overdue' => $overdue->count(),
                'actions' => $actions,
            ],
        ]);
    }

    public function inspectorPerformance(Request $request): JsonResponse
    {
        $query = Inspection::query();

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('date_from')) {
            $query->where('inspection_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('inspection_date', '<=', $request->date_to);
        }

        $performance = $query->select(
            'inspector_id',
            DB::raw('COUNT(*) as total_inspections'),
            DB::raw('AVG(overall_score) as avg_score'),
            DB::raw('SUM(CASE WHEN result = "pass" THEN 1 ELSE 0 END) as passed'),
            DB::raw('SUM(defects_found) as total_defects')
        )
        ->groupBy('inspector_id')
        ->with('inspector')
        ->get()
        ->map(function ($item) {
            $item->pass_rate = $item->total_inspections > 0 
                ? round(($item->passed / $item->total_inspections) * 100, 2) 
                : 0;
            $item->avg_score = round($item->avg_score, 2);
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $performance,
        ]);
    }

    public function defectAnalysis(Request $request, $projectId): JsonResponse
    {
        $query = Inspection::where('project_id', $projectId)
            ->where('defects_found', '>', 0);

        if ($request->has('date_from')) {
            $query->where('inspection_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('inspection_date', '<=', $request->date_to);
        }

        $totalDefects = (clone $query)->sum('defects_found');
        
        $byType = (clone $query)->select('inspection_type_id', DB::raw('SUM(defects_found) as total_defects'))
            ->groupBy('inspection_type_id')
            ->with('inspectionType')
            ->get();

        $byMonth = $query->select(
            DB::raw('YEAR(inspection_date) as year'),
            DB::raw('MONTH(inspection_date) as month'),
            DB::raw('SUM(defects_found) as total_defects'),
            DB::raw('COUNT(*) as inspections_count')
        )
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_defects' => $totalDefects,
                'by_type' => $byType,
                'by_month' => $byMonth,
            ],
        ]);
    }
}
