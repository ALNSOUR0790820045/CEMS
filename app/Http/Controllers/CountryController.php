<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Country::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $countries = $query->latest()->get();
        return view('countries.index', compact('countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('countries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'code' => 'required|string|size:2|unique:countries,code',
            'phone_code' => 'required|string|max:10',
            'currency_code' => 'nullable|string|size:3',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        Country::create($validated);

        return redirect()->route('countries.index')
            ->with('success', 'تم إضافة الدولة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Country $country)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Country $country)
    {
        return view('countries.edit', compact('country'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'code' => 'required|string|size:2|unique:countries,code,' . $country->id,
            'phone_code' => 'required|string|max:10',
            'currency_code' => 'nullable|string|size:3',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $country->update($validated);

        return redirect()->route('countries.index')
            ->with('success', 'تم تحديث الدولة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        $country->delete();
        return redirect()->route('countries.index')
            ->with('success', 'تم حذف الدولة بنجاح');
    }
}
