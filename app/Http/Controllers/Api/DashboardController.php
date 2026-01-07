<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dashboard;
use App\Models\DashboardWidget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Dashboard::with(['createdBy', 'company', 'widgets'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_public')) {
            $query->where('is_public', $request->boolean('is_public'));
        }

        if ($request->has('is_default')) {
            $query->where('is_default', $request->boolean('is_default'));
        }

        $dashboards = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($dashboards);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:executive,project,financial,hr,operations',
            'layout' => 'nullable|array',
            'is_default' => 'nullable|boolean',
            'is_public' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // If setting as default, unset other defaults of same type
            if ($request->boolean('is_default')) {
                Dashboard::where('company_id', $request->user()->company_id)
                    ->where('type', $request->type)
                    ->update(['is_default' => false]);
            }

            $dashboard = Dashboard::create([
                'name' => $request->name,
                'name_en' => $request->name_en,
                'description' => $request->description,
                'type' => $request->type,
                'layout' => $request->layout,
                'is_default' => $request->boolean('is_default'),
                'is_public' => $request->boolean('is_public'),
                'created_by_id' => $request->user()->id,
                'company_id' => $request->user()->company_id,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Dashboard created successfully',
                'dashboard' => $dashboard->load('createdBy', 'widgets')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create dashboard: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Dashboard $dashboard)
    {
        $dashboard->load(['createdBy', 'company', 'widgets']);
        return response()->json($dashboard);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dashboard $dashboard)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|required|in:executive,project,financial,hr,operations',
            'layout' => 'nullable|array',
            'is_default' => 'nullable|boolean',
            'is_public' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // If setting as default, unset other defaults of same type
            if ($request->has('is_default') && $request->boolean('is_default')) {
                Dashboard::where('company_id', $dashboard->company_id)
                    ->where('type', $request->type ?? $dashboard->type)
                    ->where('id', '!=', $dashboard->id)
                    ->update(['is_default' => false]);
            }

            $dashboard->update($request->only([
                'name',
                'name_en',
                'description',
                'type',
                'layout',
                'is_default',
                'is_public',
            ]));

            DB::commit();

            return response()->json([
                'message' => 'Dashboard updated successfully',
                'dashboard' => $dashboard->load('createdBy', 'widgets')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update dashboard: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dashboard $dashboard)
    {
        $dashboard->delete();
        return response()->json(['message' => 'Dashboard deleted successfully']);
    }

    /**
     * Get widgets for a specific dashboard.
     */
    public function widgets(Dashboard $dashboard)
    {
        $widgets = $dashboard->widgets()->visible()->get();
        return response()->json($widgets);
    }

    /**
     * Add a widget to a dashboard.
     */
    public function addWidget(Request $request, Dashboard $dashboard)
    {
        $validator = Validator::make($request->all(), [
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

        $widget = $dashboard->widgets()->create([
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
            'message' => 'Widget added successfully',
            'widget' => $widget
        ], 201);
    }

    /**
     * Update dashboard layout.
     */
    public function updateLayout(Request $request, Dashboard $dashboard)
    {
        $validator = Validator::make($request->all(), [
            'layout' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dashboard->update(['layout' => $request->layout]);

        return response()->json([
            'message' => 'Dashboard layout updated successfully',
            'dashboard' => $dashboard
        ]);
    }
}
