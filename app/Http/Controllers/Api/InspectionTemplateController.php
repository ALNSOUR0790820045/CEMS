<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InspectionTemplate;
use App\Models\TemplateItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InspectionTemplateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = InspectionTemplate::where('company_id', $user->company_id)
            ->with('inspectionType');

        if ($request->has('inspection_type_id')) {
            $query->where('inspection_type_id', $request->inspection_type_id);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $templates = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'inspection_type_id' => 'nullable|exists:inspection_types,id',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $user = Auth::user();
        $validated['company_id'] = $user->company_id;
        $validated['template_number'] = $this->generateTemplateNumber();

        $template = InspectionTemplate::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Inspection template created successfully',
            'data' => $template,
        ], 201);
    }

    public function show(InspectionTemplate $inspectionTemplate): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $inspectionTemplate->load(['inspectionType', 'items']),
        ]);
    }

    public function update(Request $request, InspectionTemplate $inspectionTemplate): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'name_en' => 'nullable|string|max:255',
            'inspection_type_id' => 'nullable|exists:inspection_types,id',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $inspectionTemplate->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Inspection template updated successfully',
            'data' => $inspectionTemplate,
        ]);
    }

    public function destroy(InspectionTemplate $inspectionTemplate): JsonResponse
    {
        $inspectionTemplate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inspection template deleted successfully',
        ]);
    }

    public function getItems($id): JsonResponse
    {
        $template = InspectionTemplate::findOrFail($id);
        $items = $template->items()->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function duplicate(Request $request, $id): JsonResponse
    {
        $originalTemplate = InspectionTemplate::with('items')->findOrFail($id);
        
        $user = Auth::user();
        $newTemplate = $originalTemplate->replicate();
        $newTemplate->template_number = $this->generateTemplateNumber();
        $newTemplate->name = $originalTemplate->name . ' (Copy)';
        $newTemplate->version = '1.0';
        $newTemplate->save();

        // Duplicate items
        foreach ($originalTemplate->items as $item) {
            $newItem = $item->replicate();
            $newItem->inspection_template_id = $newTemplate->id;
            $newItem->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Template duplicated successfully',
            'data' => $newTemplate->load('items'),
        ], 201);
    }

    private function generateTemplateNumber(): string
    {
        $year = date('Y');
        $lastTemplate = InspectionTemplate::where('template_number', 'like', "TPL-{$year}-%")
            ->latest('id')
            ->first();

        if ($lastTemplate) {
            $lastNumber = (int) substr($lastTemplate->template_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "TPL-{$year}-{$newNumber}";
    }
}
