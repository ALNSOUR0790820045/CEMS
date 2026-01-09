<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banks = Bank::latest()->get();
        return view('banks.index', compact('banks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('banks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:banks,code',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);
        
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        
        Bank::create($validated);
        
        return redirect()->route('banks.index')
            ->with('success', 'تم إضافة البنك بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bank $bank)
    {
        $bank->load('guarantees');
        return view('banks.show', compact('bank'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bank $bank)
    {
        return view('banks.edit', compact('bank'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bank $bank)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:banks,code,' . $bank->id,
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);
        
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        
        $bank->update($validated);
        
        return redirect()->route('banks.index')
            ->with('success', 'تم تحديث البنك بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bank $bank)
    {
        $bank->delete();
        
        return redirect()->route('banks.index')
            ->with('success', 'تم حذف البنك بنجاح');
    }
}
