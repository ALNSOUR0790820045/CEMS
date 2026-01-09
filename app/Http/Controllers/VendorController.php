<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Http\Requests\StoreVendorRequest;
use App\Http\Requests\UpdateVendorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Vendor::with(['company']);

        // Filter by vendor type
        if ($request->filled('vendor_type')) {
            $query->where('vendor_type', $request->vendor_type);
        }

        // Filter by vendor category
        if ($request->filled('vendor_category')) {
            $query->where('vendor_category', $request->vendor_category);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by approved status
        if ($request->filled('is_approved')) {
            $query->where('is_approved', $request->is_approved);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('vendor_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('tax_number', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $vendors = $query->latest()->paginate(15);

        return view('vendors.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendorCode = Vendor::generateVendorCode();
        return view('vendors.create', compact('vendorCode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVendorRequest $request)
    {
        $validated = $request->validated();
        
        // Generate vendor code if not provided
        if (empty($validated['vendor_code'])) {
            $validated['vendor_code'] = Vendor::generateVendorCode();
        }

        // Set company_id (assuming single company for now, adjust for multi-tenancy)
        $validated['company_id'] = 1; // This should be set based on your tenancy logic

        $vendor = Vendor::create($validated);

        return redirect()->route('vendors.show', $vendor)
            ->with('success', 'تم إضافة المورد بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vendor $vendor)
    {
        $vendor->load(['contacts', 'bankAccounts', 'documents', 'materials', 'evaluations']);
        return view('vendors.show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVendorRequest $request, Vendor $vendor)
    {
        $validated = $request->validated();
        $vendor->update($validated);

        return redirect()->route('vendors.show', $vendor)
            ->with('success', 'تم تحديث المورد بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route('vendors.index')
            ->with('success', 'تم حذف المورد بنجاح');
    }

    /**
     * Approve a vendor
     */
    public function approve(Vendor $vendor)
    {
        $vendor->update([
            'is_approved' => true,
            'approved_by_id' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'تم اعتماد المورد بنجاح');
    }

    /**
     * Reject a vendor
     */
    public function reject(Vendor $vendor)
    {
        $vendor->update([
            'is_approved' => false,
            'approved_by_id' => null,
            'approved_at' => null,
        ]);

        return back()->with('success', 'تم إلغاء اعتماد المورد');
    }

    /**
     * Generate a new vendor code
     */
    public function generateCode()
    {
        return response()->json([
            'vendor_code' => Vendor::generateVendorCode()
        ]);
    }
}
