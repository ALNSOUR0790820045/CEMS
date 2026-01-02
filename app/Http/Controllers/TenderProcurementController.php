<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use App\Models\TenderProcurementPackage;
use App\Models\TenderProcurementSupplier;
use App\Models\TenderLongLeadItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;

class TenderProcurementController extends Controller
{
    /**
     * Display a listing of procurement packages for a tender.
     */
    public function index(Request $request, $tenderId)
    {
        $tender = Tender::findOrFail($tenderId);
        
        $query = TenderProcurementPackage::where('tender_id', $tenderId)
            ->with(['responsible', 'suppliers']);

        // Filters
        if ($request->filled('procurement_type')) {
            $query->where('procurement_type', $request->procurement_type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $packages = $query->orderBy('created_at', 'desc')->get();
        $totalValue = $packages->sum('estimated_value');

        return view('tender-procurement.index', compact('tender', 'packages', 'totalValue'));
    }

    /**
     * Show the form for creating a new procurement package.
     */
    public function create($tenderId)
    {
        $tender = Tender::findOrFail($tenderId);
        $users = User::where('is_active', true)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->limit(100)
            ->get();

        return view('tender-procurement.create', compact('tender', 'users'));
    }

    /**
     * Store a newly created procurement package.
     */
    public function store(Request $request, $tenderId)
    {
        $tender = Tender::findOrFail($tenderId);

        $validated = $request->validate([
            'package_code' => 'required|string|unique:tender_procurement_packages,package_code',
            'package_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'procurement_type' => 'required|in:materials,equipment,subcontract,services,rental',
            'category' => 'nullable|in:civil,structural,architectural,electrical,mechanical,plumbing,finishing,other',
            'scope_of_work' => 'nullable|string',
            'quantities' => 'nullable|array',
            'estimated_value' => 'required|numeric|min:0',
            'required_by_date' => 'nullable|date',
            'lead_time_days' => 'nullable|integer|min:0',
            'procurement_start' => 'nullable|date',
            'strategy' => 'required|in:competitive_bidding,direct_purchase,framework_agreement,preferred_supplier',
            'requires_technical_specs' => 'boolean',
            'requires_samples' => 'boolean',
            'requires_warranty' => 'boolean',
            'warranty_months' => 'nullable|integer|min:0',
            'responsible_id' => 'nullable|exists:users,id',
        ]);

        $validated['tender_id'] = $tender->id;

        TenderProcurementPackage::create($validated);

        return redirect()->route('tender-procurement.index', $tenderId)
            ->with('success', 'تم إضافة حزمة الشراء بنجاح');
    }

    /**
     * Display the specified procurement package.
     */
    public function show($tenderId, $id)
    {
        $tender = Tender::findOrFail($tenderId);
        $package = TenderProcurementPackage::with(['responsible', 'suppliers', 'procurementSuppliers.supplier'])
            ->findOrFail($id);

        return view('tender-procurement.package-details', compact('tender', 'package'));
    }

    /**
     * Show the form for editing the specified procurement package.
     */
    public function edit($tenderId, $id)
    {
        $tender = Tender::findOrFail($tenderId);
        $package = TenderProcurementPackage::findOrFail($id);
        $users = User::where('is_active', true)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->limit(100)
            ->get();

        return view('tender-procurement.edit', compact('tender', 'package', 'users'));
    }

    /**
     * Update the specified procurement package.
     */
    public function update(Request $request, $tenderId, $id)
    {
        $tender = Tender::findOrFail($tenderId);
        $package = TenderProcurementPackage::findOrFail($id);

        $validated = $request->validate([
            'package_code' => 'required|string|unique:tender_procurement_packages,package_code,' . $id,
            'package_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'procurement_type' => 'required|in:materials,equipment,subcontract,services,rental',
            'category' => 'nullable|in:civil,structural,architectural,electrical,mechanical,plumbing,finishing,other',
            'scope_of_work' => 'nullable|string',
            'quantities' => 'nullable|array',
            'estimated_value' => 'required|numeric|min:0',
            'required_by_date' => 'nullable|date',
            'lead_time_days' => 'nullable|integer|min:0',
            'procurement_start' => 'nullable|date',
            'strategy' => 'required|in:competitive_bidding,direct_purchase,framework_agreement,preferred_supplier',
            'requires_technical_specs' => 'boolean',
            'requires_samples' => 'boolean',
            'requires_warranty' => 'boolean',
            'warranty_months' => 'nullable|integer|min:0',
            'status' => 'required|in:planned,rfq_prepared,quotations_received,evaluated,approved',
            'responsible_id' => 'nullable|exists:users,id',
        ]);

        $package->update($validated);

        return redirect()->route('tender-procurement.show', [$tenderId, $id])
            ->with('success', 'تم تحديث حزمة الشراء بنجاح');
    }

    /**
     * Remove the specified procurement package.
     */
    public function destroy($tenderId, $id)
    {
        $tender = Tender::findOrFail($tenderId);
        $package = TenderProcurementPackage::findOrFail($id);
        
        $package->delete();

        return redirect()->route('tender-procurement.index', $tenderId)
            ->with('success', 'تم حذف حزمة الشراء بنجاح');
    }

    /**
     * Display suppliers comparison for a package.
     */
    public function suppliers($tenderId, $packageId)
    {
        $tender = Tender::findOrFail($tenderId);
        $package = TenderProcurementPackage::with(['procurementSuppliers.supplier'])->findOrFail($packageId);
        $availableSuppliers = Supplier::where('is_active', true)->get();

        return view('tender-procurement.suppliers', compact('tender', 'package', 'availableSuppliers'));
    }

    /**
     * Add a supplier to a procurement package.
     */
    public function addSupplier(Request $request, $tenderId, $packageId)
    {
        $package = TenderProcurementPackage::findOrFail($packageId);

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'quoted_price' => 'nullable|numeric|min:0',
            'delivery_days' => 'nullable|integer|min:0',
            'payment_terms' => 'nullable|string',
            'technical_compliance' => 'nullable|string',
            'score' => 'nullable|integer|min:0|max:100',
            'is_recommended' => 'boolean',
        ]);

        $validated['tender_procurement_package_id'] = $packageId;

        TenderProcurementSupplier::create($validated);

        return redirect()->route('tender-procurement.suppliers', [$tenderId, $packageId])
            ->with('success', 'تم إضافة المورد بنجاح');
    }

    /**
     * Update supplier information for a package.
     */
    public function updateSupplier(Request $request, $tenderId, $packageId, $supplierId)
    {
        $procurementSupplier = TenderProcurementSupplier::where('tender_procurement_package_id', $packageId)
            ->where('id', $supplierId)
            ->firstOrFail();

        $validated = $request->validate([
            'quoted_price' => 'nullable|numeric|min:0',
            'delivery_days' => 'nullable|integer|min:0',
            'payment_terms' => 'nullable|string',
            'technical_compliance' => 'nullable|string',
            'score' => 'nullable|integer|min:0|max:100',
            'is_recommended' => 'boolean',
        ]);

        $procurementSupplier->update($validated);

        return redirect()->route('tender-procurement.suppliers', [$tenderId, $packageId])
            ->with('success', 'تم تحديث بيانات المورد بنجاح');
    }

    /**
     * Display procurement timeline.
     */
    public function timeline($tenderId)
    {
        $tender = Tender::findOrFail($tenderId);
        $packages = TenderProcurementPackage::where('tender_id', $tenderId)
            ->with('responsible')
            ->orderBy('required_by_date')
            ->get();

        return view('tender-procurement.timeline', compact('tender', 'packages'));
    }

    /**
     * Display long lead items.
     */
    public function longLeadItems($tenderId)
    {
        $tender = Tender::findOrFail($tenderId);
        $longLeadItems = TenderLongLeadItem::where('tender_id', $tenderId)
            ->with('procurementPackage')
            ->orderBy('must_order_by')
            ->get();

        return view('tender-procurement.long-lead-items', compact('tender', 'longLeadItems'));
    }

    /**
     * Store a new long lead item.
     */
    public function storeLongLeadItem(Request $request, $tenderId)
    {
        $tender = Tender::findOrFail($tenderId);

        $validated = $request->validate([
            'tender_procurement_package_id' => 'nullable|exists:tender_procurement_packages,id',
            'item_name' => 'required|string|max:255',
            'description' => 'required|string',
            'lead_time_weeks' => 'required|integer|min:1',
            'must_order_by' => 'required|date',
            'estimated_cost' => 'required|numeric|min:0',
            'is_critical' => 'boolean',
            'mitigation_plan' => 'nullable|string',
        ]);

        $validated['tender_id'] = $tender->id;

        TenderLongLeadItem::create($validated);

        return redirect()->route('tender-procurement.long-lead-items', $tenderId)
            ->with('success', 'تم إضافة البند بنجاح');
    }
}
