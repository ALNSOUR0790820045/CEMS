<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashForecast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CashForecastController extends Controller
{
    public function index(Request $request)
    {
        $query = CashForecast::query();

        if ($request->has('forecast_type')) {
            $query->where('forecast_type', $request->forecast_type);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('from_date')) {
            $query->where('forecast_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->where('forecast_date', '<=', $request->to_date);
        }

        $forecasts = $query->orderBy('forecast_date', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $forecasts
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'forecast_date' => 'required|date',
            'forecast_type' => 'required|in:inflow,outflow',
            'category' => 'required|in:receivables,payables,payroll,expenses,loans,other',
            'expected_amount' => 'required|numeric|min:0',
            'probability_percentage' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $forecastData = $validator->validated();
            $forecastData['company_id'] = auth()->user()->company_id;

            $forecast = CashForecast::create($forecastData);

            return response()->json([
                'success' => true,
                'message' => 'Cash forecast created successfully',
                'data' => $forecast
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create cash forecast',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $forecast = CashForecast::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $forecast
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'forecast_date' => 'date',
            'forecast_type' => 'in:inflow,outflow',
            'category' => 'in:receivables,payables,payroll,expenses,loans,other',
            'expected_amount' => 'numeric|min:0',
            'actual_amount' => 'nullable|numeric',
            'probability_percentage' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $forecast = CashForecast::findOrFail($id);
            $forecast->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Cash forecast updated successfully',
                'data' => $forecast
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cash forecast',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $forecast = CashForecast::findOrFail($id);
            $forecast->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cash forecast deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete cash forecast',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function summary(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth());
        $toDate = $request->input('to_date', now()->endOfMonth());

        $inflows = CashForecast::inflows()
            ->dateRange($fromDate, $toDate)
            ->sum('expected_amount');

        $outflows = CashForecast::outflows()
            ->dateRange($fromDate, $toDate)
            ->sum('expected_amount');

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'from' => $fromDate,
                    'to' => $toDate
                ],
                'total_inflows' => $inflows,
                'total_outflows' => $outflows,
                'net_cash_flow' => $inflows - $outflows
            ]
        ]);
    }
}
