<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChartService;
use Illuminate\Http\JsonResponse;

class ChartController extends Controller
{
    protected $chartService;

    public function __construct(ChartService $chartService)
    {
        $this->chartService = $chartService;
    }

    /**
     * Get chart data by type
     */
    public function show(string $chartType): JsonResponse
    {
        $chartData = $this->chartService->getChartByType($chartType);

        if (isset($chartData['error'])) {
            return response()->json([
                'success' => false,
                'message' => $chartData['error'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $chartData,
        ]);
    }
}
