<?php

namespace App\Http\Controllers;

use App\Models\BOQHeader;
use App\Models\BOQSection;
use App\Models\BOQItem;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BOQController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $boqs = BOQHeader::with(['creator', 'sections', 'items'])
            ->latest()
            ->paginate(20);
        
        return view('boq.index', compact('boqs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $units = Unit::where('is_active', true)->get();
        return view('boq.create', compact('units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:tender,contract,variation',
            'currency' => 'required|string|max:3',
        ]);

        DB::beginTransaction();
        try {
            $validated['boq_number'] = $this->generateBOQNumber();
            $validated['created_by'] = Auth::id();
            
            $boq = BOQHeader::create($validated);

            DB::commit();
            
            return redirect()->route('boq.edit', $boq)
                ->with('success', 'تم إنشاء جدول الكميات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء جدول الكميات: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BOQHeader $boq)
    {
        $boq->load(['sections.items', 'creator', 'approver', 'revisions']);
        return view('boq.show', compact('boq'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BOQHeader $boq)
    {
        $boq->load(['sections.items.resources']);
        $units = Unit::where('is_active', true)->get();
        return view('boq.edit', compact('boq', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BOQHeader $boq)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'markup_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $boq->update($validated);
        $boq->recalculateTotals();

        return back()->with('success', 'تم تحديث جدول الكميات بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BOQHeader $boq)
    {
        $boq->delete();
        return redirect()->route('boq.index')
            ->with('success', 'تم حذف جدول الكميات بنجاح');
    }

    /**
     * Calculate BOQ totals
     */
    public function calculate(BOQHeader $boq)
    {
        $boq->recalculateTotals();
        
        return response()->json([
            'success' => true,
            'total_amount' => $boq->total_amount,
            'final_amount' => $boq->final_amount,
        ]);
    }

    /**
     * Duplicate BOQ
     */
    public function duplicate(BOQHeader $boq)
    {
        DB::beginTransaction();
        try {
            $newBoq = $boq->replicate();
            $newBoq->boq_number = $this->generateBOQNumber();
            $newBoq->status = 'draft';
            $newBoq->version = 1;
            $newBoq->approved_by = null;
            $newBoq->approved_at = null;
            $newBoq->created_by = Auth::id();
            $newBoq->save();

            // Copy sections and items
            foreach ($boq->sections as $section) {
                $newSection = $section->replicate();
                $newSection->boq_header_id = $newBoq->id;
                $newSection->save();

                foreach ($section->items as $item) {
                    $newItem = $item->replicate();
                    $newItem->boq_header_id = $newBoq->id;
                    $newItem->boq_section_id = $newSection->id;
                    $newItem->save();
                }
            }

            DB::commit();
            
            return redirect()->route('boq.edit', $newBoq)
                ->with('success', 'تم نسخ جدول الكميات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Approve BOQ
     */
    public function approve(BOQHeader $boq)
    {
        $boq->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'تم اعتماد جدول الكميات بنجاح');
    }

    /**
     * Cost analysis
     */
    public function costAnalysis(BOQHeader $boq)
    {
        $boq->load(['items.resources']);
        
        $materialCost = $boq->items->sum('material_cost');
        $laborCost = $boq->items->sum('labor_cost');
        $equipmentCost = $boq->items->sum('equipment_cost');
        $subcontractCost = $boq->items->sum('subcontract_cost');
        $overheadCost = $boq->items->sum('overhead_cost');

        return view('boq.cost-analysis', compact(
            'boq',
            'materialCost',
            'laborCost',
            'equipmentCost',
            'subcontractCost',
            'overheadCost'
        ));
    }

    /**
     * Generate BOQ number
     */
    private function generateBOQNumber(): string
    {
        $year = date('Y');
        $lastBoq = BOQHeader::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastBoq ? intval(substr($lastBoq->boq_number, -4)) + 1 : 1;
        
        return sprintf('BOQ-%s-%04d', $year, $number);
    }

    /**
     * Add section to BOQ
     */
    public function addSection(Request $request, BOQHeader $boq)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['boq_header_id'] = $boq->id;
        $validated['sort_order'] = $boq->sections()->max('sort_order') + 1;

        $section = BOQSection::create($validated);

        return response()->json([
            'success' => true,
            'section' => $section,
        ]);
    }

    /**
     * Add item to BOQ
     */
    public function addItem(Request $request, BOQHeader $boq)
    {
        $validated = $request->validate([
            'boq_section_id' => 'required|exists:boq_sections,id',
            'item_number' => 'required|string|max:50',
            'description' => 'required|string',
            'unit' => 'required|string|max:20',
            'quantity' => 'required|numeric|min:0',
            'unit_rate' => 'required|numeric|min:0',
        ]);

        $validated['boq_header_id'] = $boq->id;
        $validated['amount'] = $validated['quantity'] * $validated['unit_rate'];
        $validated['remaining_quantity'] = $validated['quantity'];

        $item = BOQItem::create($validated);

        // Recalculate section and BOQ totals
        $item->section->recalculateTotal();
        $boq->recalculateTotals();

        return response()->json([
            'success' => true,
            'item' => $item,
        ]);
    }

    /**
     * Update item in BOQ
     */
    public function updateItem(Request $request, BOQHeader $boq, BOQItem $item)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'unit' => 'required|string|max:20',
            'quantity' => 'required|numeric|min:0',
            'unit_rate' => 'required|numeric|min:0',
        ]);

        $validated['amount'] = $validated['quantity'] * $validated['unit_rate'];
        $item->update($validated);
        
        // Recalculate totals
        $item->section->recalculateTotal();
        $boq->recalculateTotals();

        return response()->json([
            'success' => true,
            'item' => $item->fresh(),
        ]);
    }

    /**
     * Delete item from BOQ
     */
    public function deleteItem(BOQHeader $boq, BOQItem $item)
    {
        $section = $item->section;
        $item->delete();
        
        // Recalculate totals
        $section->recalculateTotal();
        $boq->recalculateTotals();

        return response()->json([
            'success' => true,
        ]);
    }
}
