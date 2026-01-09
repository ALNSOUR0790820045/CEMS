<?php

namespace App\Http\Controllers;

use App\Models\Risk;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiskReportController extends Controller
{
    public function summary($projectId): JsonResponse
    {
        $project = Project::findOrFail($projectId);
        
        $risks = Risk::where('project_id', $projectId)->get();
        
        $summary = [
            'total_risks' => $risks->count(),
            'critical_risks' => $risks->where('risk_level', 'critical')->count(),
            'high_risks' => $risks->where('risk_level', 'high')->count(),
            'medium_risks' => $risks->where('risk_level', 'medium')->count(),
            'low_risks' => $risks->where('risk_level', 'low')->count(),
            'by_status' => $risks->groupBy('status')->map->count(),
            'by_category' => $risks->groupBy('category')->map->count(),
            'total_cost_exposure' => $risks->sum('cost_impact_expected'),
            'total_schedule_impact' => $risks->sum('schedule_impact_days'),
        ];

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    public function riskMatrix($projectId): JsonResponse
    {
        $risks = Risk::where('project_id', $projectId)
            ->select('probability_score', 'impact_score', 'risk_level', DB::raw('count(*) as count'))
            ->groupBy('probability_score', 'impact_score', 'risk_level')
            ->get();

        $matrix = [];
        for ($p = 1; $p <= 5; $p++) {
            for ($i = 1; $i <= 5; $i++) {
                $cell = $risks->first(function($r) use ($p, $i) {
                    return $r->probability_score == $p && $r->impact_score == $i;
                });
                
                $matrix[$p][$i] = [
                    'count' => $cell ? $cell->count : 0,
                    'level' => $cell ? $cell->risk_level : 'low',
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $matrix,
        ]);
    }

    public function heatMap($projectId): JsonResponse
    {
        $risks = Risk::where('project_id', $projectId)
            ->select('risk_number', 'title', 'probability_score', 'impact_score', 'risk_level', 'risk_score')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $risks,
        ]);
    }

    public function trend($projectId): JsonResponse
    {
        $risks = Risk::where('project_id', $projectId)
            ->select(
                DB::raw('DATE(created_at) as date'),
                'risk_level',
                DB::raw('count(*) as count')
            )
            ->groupBy('date', 'risk_level')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $risks,
        ]);
    }

    public function topRisks($projectId): JsonResponse
    {
        $risks = Risk::where('project_id', $projectId)
            ->with(['owner'])
            ->orderBy('risk_score', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $risks,
        ]);
    }

    public function exposure($projectId): JsonResponse
    {
        $risks = Risk::where('project_id', $projectId)
            ->select(
                'category',
                DB::raw('SUM(cost_impact_expected) as total_cost'),
                DB::raw('SUM(schedule_impact_days) as total_days'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('category')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $risks,
        ]);
    }

    public function responseStatus($projectId): JsonResponse
    {
        $risks = Risk::where('project_id', $projectId)
            ->with(['responses' => function($query) {
                $query->select('risk_id', 'status', DB::raw('count(*) as count'))
                    ->groupBy('risk_id', 'status');
            }])
            ->get();

        $statusCounts = [
            'planned' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'cancelled' => 0,
        ];

        foreach ($risks as $risk) {
            foreach ($risk->responses as $response) {
                $statusCounts[$response->status] = ($statusCounts[$response->status] ?? 0) + 1;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $statusCounts,
        ]);
    }
}
