<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DashboardWidget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WidgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DashboardWidget::with(['dashboard'])
            ->whereHas('dashboard', function($q) use ($request) {
                $q->where('company_id', $request->user()->company_id);
            });

        if ($request->has('dashboard_id')) {
            $query->where('dashboard_id', $request->dashboard_id);
        }

        if ($request->has('widget_type')) {
            $query->where('widget_type', $request->widget_type);
        }

        if ($request->has('is_visible')) {
            $query->where('is_visible', $request->boolean('is_visible'));
        }

        $widgets = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($widgets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dashboard_id' => 'required|exists:dashboards,id',
            'widget_type' => 'required|in:chart,kpi,table,counter,gauge',
            'title' => 'required|string|max:255',
            'data_source' => 'nullable|string',
            'config' => 'nullable|array',
            'position_x' => 'nullable|integer|min:0',
            'position_y' => 'nullable|integer|min:0',
            'width' => 'nullable|integer|min:1|max:12',
            'height' => 'nullable|integer|min:1',
            'refresh_interval' => 'nullable|integer|min:0',
            'is_visible' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $widget = DashboardWidget::create([
            'dashboard_id' => $request->dashboard_id,
            'widget_type' => $request->widget_type,
            'title' => $request->title,
            'data_source' => $request->data_source,
            'config' => $request->config,
            'position_x' => $request->position_x ?? 0,
            'position_y' => $request->position_y ?? 0,
            'width' => $request->width ?? 6,
            'height' => $request->height ?? 4,
            'refresh_interval' => $request->refresh_interval,
            'is_visible' => $request->is_visible ?? true,
        ]);

        return response()->json([
            'message' => 'Widget created successfully',
            'widget' => $widget->load('dashboard')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, DashboardWidget $widget)
    {
        // Check if widget belongs to user's company
        $widget->load('dashboard');
        if ($widget->dashboard->company_id !== $request->user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json($widget);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DashboardWidget $widget)
    {
        // Check if widget belongs to user's company
        $widget->load('dashboard');
        if ($widget->dashboard->company_id !== $request->user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'widget_type' => 'sometimes|required|in:chart,kpi,table,counter,gauge',
            'title' => 'sometimes|required|string|max:255',
            'data_source' => 'nullable|string',
            'config' => 'nullable|array',
            'position_x' => 'nullable|integer|min:0',
            'position_y' => 'nullable|integer|min:0',
            'width' => 'nullable|integer|min:1|max:12',
            'height' => 'nullable|integer|min:1',
            'refresh_interval' => 'nullable|integer|min:0',
            'is_visible' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $widget->update($request->only([
            'widget_type',
            'title',
            'data_source',
            'config',
            'position_x',
            'position_y',
            'width',
            'height',
            'refresh_interval',
            'is_visible',
        ]));

        return response()->json([
            'message' => 'Widget updated successfully',
            'widget' => $widget->load('dashboard')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, DashboardWidget $widget)
    {
        // Check if widget belongs to user's company
        $widget->load('dashboard');
        if ($widget->dashboard->company_id !== $request->user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $widget->delete();
        return response()->json(['message' => 'Widget deleted successfully']);
    }

    /**
     * Get widget data based on its data source.
     */
    public function getData(Request $request, DashboardWidget $widget)
    {
        // Check if widget belongs to user's company
        $widget->load('dashboard');
        if ($widget->dashboard->company_id !== $request->user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // This would be implemented based on the data_source field
        // For now, returning a simple response
        return response()->json([
            'widget_id' => $widget->id,
            'title' => $widget->title,
            'data' => [], // Actual data would be fetched based on data_source
            'config' => $widget->config,
        ]);
    }

    /**
     * Refresh widget data.
     */
    public function refresh(Request $request, DashboardWidget $widget)
    {
        // Check if widget belongs to user's company
        $widget->load('dashboard');
        if ($widget->dashboard->company_id !== $request->user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // This would trigger a refresh of the widget's data
        // For now, returning a simple response
        return response()->json([
            'message' => 'Widget refreshed successfully',
            'widget' => $widget,
            'refreshed_at' => now(),
        ]);
    }
}
