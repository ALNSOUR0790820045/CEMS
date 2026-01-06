<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource (API).
     */
    public function index()
    {
        $units = Unit::where('is_active', true)->get();
        return response()->json($units);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:units,code|max:50',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'category' => 'required|in:length,area,volume,weight,count,time,other',
        ]);

        $unit = Unit::create($validated);

        return response()->json([
            'success' => true,
            'unit' => $unit,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        return response()->json($unit);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:units,code,' . $unit->id,
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'category' => 'required|in:length,area,volume,weight,count,time,other',
            'is_active' => 'boolean',
        ]);

        $unit->update($validated);

        return response()->json([
            'success' => true,
            'unit' => $unit,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
