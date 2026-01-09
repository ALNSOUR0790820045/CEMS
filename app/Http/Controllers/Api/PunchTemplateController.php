<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PunchTemplate;
use App\Models\PunchList;
use App\Models\PunchItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PunchTemplateController extends Controller
{
    public function index()
    {
        $templates = PunchTemplate::where('is_active', true)
            ->latest()
            ->paginate(20);

        return response()->json($templates);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'discipline' => 'nullable|string',
            'category' => 'nullable|string',
            'items' => 'required|array',
            'items.*.description' => 'required|string',
            'items.*.severity' => 'required|in:minor,major,critical',
            'items.*.category' => 'required|in:defect,incomplete,damage,missing,wrong',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $template = PunchTemplate::create($validated);

        return response()->json([
            'message' => 'Template created successfully',
            'data' => $template
        ], 201);
    }

    public function show($id)
    {
        $template = PunchTemplate::findOrFail($id);
        return response()->json($template);
    }

    public function update(Request $request, $id)
    {
        $template = PunchTemplate::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'discipline' => 'nullable|string',
            'category' => 'nullable|string',
            'items' => 'sometimes|array',
            'items.*.description' => 'required|string',
            'items.*.severity' => 'required|in:minor,major,critical',
            'items.*.category' => 'required|in:defect,incomplete,damage,missing,wrong',
            'is_active' => 'sometimes|boolean',
        ]);

        $template->update($validated);

        return response()->json([
            'message' => 'Template updated successfully',
            'data' => $template->fresh()
        ]);
    }

    public function destroy($id)
    {
        $template = PunchTemplate::findOrFail($id);
        $template->delete();

        return response()->json(['message' => 'Template deleted successfully']);
    }

    public function applyTemplate(Request $request, $listId, $templateId)
    {
        $list = PunchList::findOrFail($listId);
        $template = PunchTemplate::findOrFail($templateId);

        if (!$template->is_active) {
            return response()->json(['error' => 'Template is not active'], 400);
        }

        DB::beginTransaction();
        try {
            $items = $template->items ?? [];
            $sequence = $list->items()->count();

            foreach ($items as $templateItem) {
                $sequence++;
                // Use consistent format with PunchItemController
                PunchItem::create([
                    'punch_list_id' => $list->id,
                    'item_number' => $list->list_number.'-'.str_pad($sequence, 3, '0', STR_PAD_LEFT),
                    'description' => $templateItem['description'],
                    'severity' => $templateItem['severity'],
                    'category' => $templateItem['category'],
                    'status' => 'open',
                    'priority' => 'medium',
                    'discipline' => $template->discipline ?? null,
                ]);
            }

            // Update list statistics
            $list->updateStatistics();

            DB::commit();

            return response()->json([
                'message' => 'Template applied successfully',
                'items_created' => count($items)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to apply template', 'message' => $e->getMessage()], 500);
        }
    }
}
