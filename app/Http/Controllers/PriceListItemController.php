<?php

namespace App\Http\Controllers;

use App\Models\PriceList;
use App\Models\PriceListItem;
use App\Models\PriceHistory;
use App\Models\Material;
use App\Models\LaborCategory;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PriceListItemController extends Controller
{
    /**
     * Show the form for creating a new item.
     */
    public function create(PriceList $priceList)
    {
        $materials = Material::where('is_active', true)->get();
        $laborCategories = LaborCategory::where('is_active', true)->get();
        $equipmentCategories = EquipmentCategory::where('is_active', true)->get();
        
        return view('prices.items.create', compact('priceList', 'materials', 'laborCategories', 'equipmentCategories'));
    }

    /**
     * Store a newly created item.
     */
    public function store(Request $request, PriceList $priceList)
    {
        $validated = $request->validate([
            'item_code' => 'required|string',
            'item_name' => 'required|string|max:255',
            'item_name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'specifications' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'material_id' => 'nullable|exists:materials,id',
            'brand' => 'nullable|string|max:255',
            'origin' => 'nullable|string|max:255',
            'labor_category_id' => 'nullable|exists:labor_categories,id',
            'labor_rate_type' => 'nullable|in:hourly,daily,monthly',
            'equipment_category_id' => 'nullable|exists:equipment_categories,id',
            'equipment_rate_type' => 'nullable|in:hourly,daily,monthly',
            'includes_operator' => 'boolean',
            'includes_fuel' => 'boolean',
        ]);

        $validated['price_list_id'] = $priceList->id;
        $item = PriceListItem::create($validated);

        // Create history entry
        PriceHistory::create([
            'price_list_item_id' => $item->id,
            'effective_date' => now(),
            'new_price' => $item->unit_price,
            'change_reason' => 'other',
            'notes' => 'إنشاء البند',
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('price-lists.items', $priceList)
            ->with('success', 'تم إضافة البند بنجاح');
    }

    /**
     * Update the specified item.
     */
    public function update(Request $request, PriceListItem $item)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'item_name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'specifications' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'brand' => 'nullable|string|max:255',
            'origin' => 'nullable|string|max:255',
            'labor_rate_type' => 'nullable|in:hourly,daily,monthly',
            'equipment_rate_type' => 'nullable|in:hourly,daily,monthly',
            'includes_operator' => 'boolean',
            'includes_fuel' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Track price change
        if ($item->unit_price != $validated['unit_price']) {
            $changePercentage = (($validated['unit_price'] - $item->unit_price) / $item->unit_price) * 100;
            
            PriceHistory::create([
                'price_list_item_id' => $item->id,
                'effective_date' => now(),
                'old_price' => $item->unit_price,
                'new_price' => $validated['unit_price'],
                'change_percentage' => $changePercentage,
                'change_reason' => $request->input('change_reason', 'other'),
                'notes' => $request->input('change_notes'),
                'updated_by' => Auth::id(),
            ]);
        }

        $item->update($validated);

        return redirect()->route('price-lists.items', $item->price_list_id)
            ->with('success', 'تم تحديث البند بنجاح');
    }

    /**
     * Get history for an item.
     */
    public function history(PriceListItem $item)
    {
        $history = $item->history()
            ->with('updater')
            ->orderBy('effective_date', 'desc')
            ->paginate(20);
            
        return view('prices.history', compact('item', 'history'));
    }
}
