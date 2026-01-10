<?php

namespace App\Http\Controllers;

use App\Models\PriceList;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PriceListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $priceLists = PriceList::with(['region', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('prices.lists.index', compact('priceLists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $regions = Region::where('is_active', true)->get();
        return view('prices.lists.create', compact('regions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:price_lists,code',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'type' => 'required|in:material,labor,equipment,subcontract,composite',
            'source' => 'required|in:internal,ministry,syndicate,market,vendor',
            'effective_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'currency' => 'required|string|size:3',
            'region_id' => 'nullable|exists:regions,id',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $priceList = PriceList::create($validated);

        return redirect()->route('price-lists.show', $priceList)
            ->with('success', 'تم إنشاء قائمة الأسعار بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(PriceList $priceList)
    {
        $priceList->load(['region', 'creator', 'items.material', 'items.laborCategory', 'items.equipmentCategory']);
        return view('prices.lists.show', compact('priceList'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PriceList $priceList)
    {
        $regions = Region::where('is_active', true)->get();
        return view('prices.lists.edit', compact('priceList', 'regions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PriceList $priceList)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:price_lists,code,' . $priceList->id,
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'type' => 'required|in:material,labor,equipment,subcontract,composite',
            'source' => 'required|in:internal,ministry,syndicate,market,vendor',
            'effective_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'currency' => 'required|string|size:3',
            'region_id' => 'nullable|exists:regions,id',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $priceList->update($validated);

        return redirect()->route('price-lists.show', $priceList)
            ->with('success', 'تم تحديث قائمة الأسعار بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PriceList $priceList)
    {
        $priceList->delete();
        
        return redirect()->route('price-lists.index')
            ->with('success', 'تم حذف قائمة الأسعار بنجاح');
    }

    /**
     * Get items for a price list
     */
    public function items(PriceList $priceList)
    {
        $items = $priceList->items()
            ->with(['material', 'laborCategory', 'equipmentCategory'])
            ->paginate(50);
            
        return view('prices.items.index', compact('priceList', 'items'));
    }
}
