<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\KpiService;
use Illuminate\Http\JsonResponse;

class KpiController extends Controller
{
    protected $kpiService;

    public function __construct(KpiService $kpiService)
    {
        $this->kpiService = $kpiService;
    }

    /**
     * Get all KPIs
     */
    public function index(): JsonResponse
    {
        $kpis = $this->kpiService->getAllKpis();

        return response()->json([
            'success' => true,
            'data' => $kpis,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
