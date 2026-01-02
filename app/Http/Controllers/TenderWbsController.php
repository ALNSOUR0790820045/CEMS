<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use App\Models\TenderWbs;
use App\Models\TenderBoqItem;
use Illuminate\Http\Request;

class TenderWbsController extends Controller
{
    public function index($tenderId)
    {
        $tender = Tender::with(['wbsItems' => function ($query) {
            $query->rootLevel()->with('children.children.children.children.children');
        }])->findOrFail($tenderId);

        return view('tender-wbs.index', compact('tender'));
    }

    public function create($tenderId)
    {
        $tender = Tender::findOrFail($tenderId);
        $parentWbsItems = TenderWbs::where('tender_id', $tenderId)
            ->where('level', '<', 5)
            ->orderBy('wbs_code')
            ->get();

        return view('tender-wbs.create', compact('tender', 'parentWbsItems'));
    }

    public function store(Request $request, $tenderId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:tender_wbs,id',
            'wbs_code' => [
                'required',
                'string',
                \Illuminate\Validation\Rule::unique('tender_wbs', 'wbs_code')->where(function ($query) use ($tenderId) {
                    return $query->where('tender_id', $tenderId);
                }),
            ],
            'level' => 'required|integer|min:1|max:5',
            'estimated_cost' => 'nullable|numeric|min:0',
            'materials_cost' => 'nullable|numeric|min:0',
            'labor_cost' => 'nullable|numeric|min:0',
            'equipment_cost' => 'nullable|numeric|min:0',
            'subcontractor_cost' => 'nullable|numeric|min:0',
            'estimated_duration_days' => 'nullable|integer|min:0',
            'weight_percentage' => 'nullable|numeric|min:0|max:100',
            'is_summary' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['tender_id'] = $tenderId;
        $validated['is_active'] = true;
        $validated['is_summary'] = $request->has('is_summary');

        $wbs = TenderWbs::create($validated);

        // Recalculate cost rollup for parent items
        if ($wbs->parent_id) {
            $parent = TenderWbs::find($wbs->parent_id);
            $parent->calculateCostRollup();
        }

        return redirect()->route('tender-wbs.index', $tenderId)
            ->with('success', 'تم إضافة عنصر WBS بنجاح');
    }

    public function edit($tenderId, $id)
    {
        $tender = Tender::findOrFail($tenderId);
        $wbs = TenderWbs::where('tender_id', $tenderId)->findOrFail($id);
        $parentWbsItems = TenderWbs::where('tender_id', $tenderId)
            ->where('level', '<', $wbs->level)
            ->where('id', '!=', $id)
            ->orderBy('wbs_code')
            ->get();

        return view('tender-wbs.edit', compact('tender', 'wbs', 'parentWbsItems'));
    }

    public function update(Request $request, $tenderId, $id)
    {
        $wbs = TenderWbs::where('tender_id', $tenderId)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:tender_wbs,id',
            'wbs_code' => [
                'required',
                'string',
                \Illuminate\Validation\Rule::unique('tender_wbs', 'wbs_code')->where(function ($query) use ($tenderId) {
                    return $query->where('tender_id', $tenderId);
                })->ignore($id),
            ],
            'level' => 'required|integer|min:1|max:5',
            'estimated_cost' => 'nullable|numeric|min:0',
            'materials_cost' => 'nullable|numeric|min:0',
            'labor_cost' => 'nullable|numeric|min:0',
            'equipment_cost' => 'nullable|numeric|min:0',
            'subcontractor_cost' => 'nullable|numeric|min:0',
            'estimated_duration_days' => 'nullable|integer|min:0',
            'weight_percentage' => 'nullable|numeric|min:0|max:100',
            'is_summary' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_summary'] = $request->has('is_summary');

        $wbs->update($validated);

        // Recalculate cost rollup for parent items
        if ($wbs->parent_id) {
            $parent = TenderWbs::find($wbs->parent_id);
            $parent->calculateCostRollup();
        }

        return redirect()->route('tender-wbs.index', $tenderId)
            ->with('success', 'تم تحديث عنصر WBS بنجاح');
    }

    public function destroy($tenderId, $id)
    {
        $wbs = TenderWbs::where('tender_id', $tenderId)->findOrFail($id);

        // Check if has children
        if ($wbs->children()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف عنصر يحتوي على عناصر فرعية');
        }

        $parentId = $wbs->parent_id;
        $wbs->delete();

        // Recalculate cost rollup for parent items
        if ($parentId) {
            $parent = TenderWbs::find($parentId);
            $parent->calculateCostRollup();
        }

        return redirect()->route('tender-wbs.index', $tenderId)
            ->with('success', 'تم حذف عنصر WBS بنجاح');
    }

    public function import($tenderId)
    {
        $tender = Tender::findOrFail($tenderId);
        return view('tender-wbs.import', compact('tender'));
    }

    public function updateSort(Request $request, $tenderId)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:tender_wbs,id',
            'items.*.sort_order' => 'required|integer',
        ]);

        foreach ($validated['items'] as $item) {
            TenderWbs::where('id', $item['id'])
                ->where('tender_id', $tenderId)
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true]);
    }
}
