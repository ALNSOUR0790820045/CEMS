<?php

namespace App\Http\Controllers;

use App\Models\PaymentTerm;
use Illuminate\Http\Request;

class PaymentTermController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PaymentTerm::query();
        
        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $paymentTerms = $query->latest()->get();
        return view('payment-terms.index', compact('paymentTerms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('payment-terms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'days' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        PaymentTerm::create($validated);

        return redirect()->route('payment-terms.index')
            ->with('success', 'تم إضافة شرط الدفع بنجاح');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentTerm $paymentTerm)
    {
        return view('payment-terms.edit', compact('paymentTerm'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentTerm $paymentTerm)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'days' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $paymentTerm->update($validated);

        return redirect()->route('payment-terms.index')
            ->with('success', 'تم تحديث شرط الدفع بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentTerm $paymentTerm)
    {
        $paymentTerm->delete();
        return redirect()->route('payment-terms.index')
            ->with('success', 'تم حذف شرط الدفع بنجاح');
    }
}
