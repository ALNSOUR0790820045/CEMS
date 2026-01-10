<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Currency::query();

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        // Search by name or code
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $currencies = $query->latest()->get();
        return view('currencies.index', compact('currencies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('currencies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'code' => 'required|string|size:3|unique:currencies,code',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
        ]);

        $validated['is_base'] = $request->has('is_base') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        // If this currency is marked as base, unset all other base currencies
        if ($validated['is_base']) {
            Currency::where('is_base', true)->update(['is_base' => false]);
        }

        // Ensure at least one base currency exists
        if (!$validated['is_base'] && Currency::where('is_base', true)->count() === 0) {
            $validated['is_base'] = true;
        }

        Currency::create($validated);

        return redirect()->route('currencies.index')
            ->with('success', 'تم إضافة العملة بنجاح');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Currency $currency)
    {
        return view('currencies.edit', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'code' => 'required|string|size:3|unique:currencies,code,' . $currency->id,
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
        ]);

        $validated['is_base'] = $request->has('is_base') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        // If this currency is marked as base, unset all other base currencies
        if ($validated['is_base']) {
            Currency::where('is_base', true)->where('id', '!=', $currency->id)->update(['is_base' => false]);
        }

        // Prevent removing the last base currency
        if (!$validated['is_base'] && $currency->is_base && Currency::where('is_base', true)->count() === 1) {
            return redirect()->back()
                ->withErrors(['is_base' => 'يجب أن تكون هناك عملة أساسية واحدة على الأقل'])
                ->withInput();
        }

        $currency->update($validated);

        return redirect()->route('currencies.index')
            ->with('success', 'تم تحديث العملة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Currency $currency)
    {
        // Prevent deleting the base currency
        if ($currency->is_base) {
            return redirect()->route('currencies.index')
                ->withErrors(['error' => 'لا يمكن حذف العملة الأساسية']);
        }

        $currency->delete();
        return redirect()->route('currencies.index')
            ->with('success', 'تم حذف العملة بنجاح');
    }
}
