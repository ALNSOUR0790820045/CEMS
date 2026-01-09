<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Branch;
use App\Models\City;
use App\Models\User;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Warehouse::with(['company', 'branch', 'manager', 'city']);

        // Filter by branch
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        $warehouses = $query->latest()->get();
        $branches = Branch::active()->get();

        return view('warehouses.index', compact('warehouses', 'branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::active()->get();
        $cities = City::all();
        $users = User::where('is_active', true)->get();

        return view('warehouses.create', compact('branches', 'cities', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:warehouses,code',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'manager_id' => 'nullable|exists:users,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city_id' => 'nullable|exists:cities,id',
            'is_main' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['is_main'] = $request->has('is_main') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        Warehouse::create($validated);

        return redirect()->route('warehouses.index')
            ->with('success', 'تم إضافة المستودع بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Warehouse $warehouse)
    {
        $warehouse->load(['company', 'branch', 'manager', 'city', 'stockMovements']);
        
        // Get latest stock movements
        $recentMovements = $warehouse->stockMovements()
            ->with('creator')
            ->latest()
            ->take(10)
            ->get();

        return view('warehouses.show', compact('warehouse', 'recentMovements'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Warehouse $warehouse)
    {
        $branches = Branch::active()->get();
        $cities = City::all();
        $users = User::where('is_active', true)->get();

        return view('warehouses.edit', compact('warehouse', 'branches', 'cities', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:warehouses,code,' . $warehouse->id,
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'manager_id' => 'nullable|exists:users,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city_id' => 'nullable|exists:cities,id',
            'is_main' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['is_main'] = $request->has('is_main') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $warehouse->update($validated);

        return redirect()->route('warehouses.index')
            ->with('success', 'تم تحديث المستودع بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        
        return redirect()->route('warehouses.index')
            ->with('success', 'تم حذف المستودع بنجاح');
    }
}
