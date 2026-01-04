<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\KpiService;
use App\Models\DashboardLayout;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    protected $kpiService;

    public function __construct(KpiService $kpiService)
    {
        $this->kpiService = $kpiService;
    }

    /**
     * Get executive dashboard data
     */
    public function executive(): JsonResponse
    {
        $kpis = $this->kpiService->getAllKpis();

        return response()->json([
            'success' => true,
            'data' => [
                'kpis' => $kpis,
                'timestamp' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Get project-specific dashboard data
     */
    public function project(int $id): JsonResponse
    {
        try {
            $projectKpis = $this->kpiService->getProjectSpecificKpis($id);

            return response()->json([
                'success' => true,
                'data' => $projectKpis,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found',
            ], 404);
        }
    }

    /**
     * Get financial dashboard data
     */
    public function financial(): JsonResponse
    {
        $financialKpis = $this->kpiService->getFinancialKpis();

        return response()->json([
            'success' => true,
            'data' => [
                'financial_kpis' => $financialKpis,
                'timestamp' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Save dashboard layout configuration
     */
    public function saveLayout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dashboard_type' => 'required|string',
            'layout_config' => 'required|array',
        ]);

        $layout = DashboardLayout::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'dashboard_type' => $validated['dashboard_type'],
            ],
            [
                'layout_config' => $validated['layout_config'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Dashboard layout saved successfully',
            'data' => $layout,
        ]);
    }
}
