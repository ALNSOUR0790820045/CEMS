<?php

namespace App\Http\Controllers;

use App\Models\PriceListItem;
use Illuminate\Http\Request;

class PriceSearchController extends Controller
{
    /**
     * Search prices across all price lists
     */
    public function search(Request $request)
    {
        $query = PriceListItem::with(['priceList', 'material', 'laborCategory', 'equipmentCategory'])
            ->where('is_active', true)
            ->whereHas('priceList', function($q) {
                $q->where('is_active', true);
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->whereHas('priceList', function($q) use ($request) {
                $q->where('type', $request->type);
            });
        }

        if ($request->filled('source')) {
            $query->whereHas('priceList', function($q) use ($request) {
                $q->where('source', $request->source);
            });
        }

        $items = $query->paginate(50);
        
        return view('prices.search', compact('items'));
    }

    /**
     * Search materials prices
     */
    public function materials(Request $request)
    {
        $items = PriceListItem::with(['priceList', 'material'])
            ->whereHas('priceList', function($q) {
                $q->where('type', 'material')->where('is_active', true);
            })
            ->where('is_active', true)
            ->when($request->filled('search'), function($q) use ($request) {
                $search = $request->search;
                $q->where(function($q) use ($search) {
                    $q->where('item_name', 'like', "%{$search}%")
                      ->orWhere('item_code', 'like', "%{$search}%");
                });
            })
            ->paginate(50);

        return response()->json($items);
    }

    /**
     * Search labor prices
     */
    public function labor(Request $request)
    {
        $items = PriceListItem::with(['priceList', 'laborCategory'])
            ->whereHas('priceList', function($q) {
                $q->where('type', 'labor')->where('is_active', true);
            })
            ->where('is_active', true)
            ->when($request->filled('search'), function($q) use ($request) {
                $search = $request->search;
                $q->where(function($q) use ($search) {
                    $q->where('item_name', 'like', "%{$search}%")
                      ->orWhere('item_code', 'like', "%{$search}%");
                });
            })
            ->paginate(50);

        return response()->json($items);
    }

    /**
     * Search equipment prices
     */
    public function equipment(Request $request)
    {
        $items = PriceListItem::with(['priceList', 'equipmentCategory'])
            ->whereHas('priceList', function($q) {
                $q->where('type', 'equipment')->where('is_active', true);
            })
            ->where('is_active', true)
            ->when($request->filled('search'), function($q) use ($request) {
                $search = $request->search;
                $q->where(function($q) use ($search) {
                    $q->where('item_name', 'like', "%{$search}%")
                      ->orWhere('item_code', 'like', "%{$search}%");
                });
            })
            ->paginate(50);

        return response()->json($items);
    }

    /**
     * Compare prices from different sources
     */
    public function compare(Request $request)
    {
        $search = $request->input('search');
        
        $items = PriceListItem::with(['priceList'])
            ->where('is_active', true)
            ->whereHas('priceList', function($q) {
                $q->where('is_active', true);
            })
            ->where(function($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%");
            })
            ->get()
            ->groupBy('item_code');

        return view('prices.compare', compact('items'));
    }
}
