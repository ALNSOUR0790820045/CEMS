<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subcontractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubcontractorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Subcontractor::with(['country', 'city', 'currency', 'company'])
            ->where('company_id', Auth::user()->company_id);

        // Filters
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->has('is_approved')) {
            $query->where('is_approved', $request->is_approved);
        }

        if ($request->has('is_blacklisted')) {
            $query->where('is_blacklisted', $request->is_blacklisted);
        }

        if ($request->has('trade_category')) {
            $query->where('trade_category', $request->trade_category);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('subcontractor_code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $subcontractors = $query->latest()->paginate($perPage);

        return response()->json($subcontractors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'subcontractor_type' => 'required|in:specialized,general,labor_only,materials_labor',
            'trade_category' => 'required|in:civil,electrical,mechanical,plumbing,finishing,landscaping,other',
            'commercial_registration' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255|unique:subcontractors,tax_number',
            'license_number' => 'nullable|string|max:255',
            'license_expiry' => 'nullable|date',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'payment_terms' => 'required|in:cod,7_days,15_days,30_days,45_days,60_days',
            'credit_limit' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'retention_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['company_id'] = Auth::user()->company_id;
        $data['created_by_id'] = Auth::id();

        $subcontractor = Subcontractor::create($data);

        return response()->json($subcontractor->load(['country', 'city', 'currency']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subcontractor = Subcontractor::with([
            'country',
            'city',
            'currency',
            'glAccount',
            'contacts',
            'agreements',
            'createdBy',
            'approvedBy'
        ])->findOrFail($id);

        // Check company ownership
        if ($subcontractor->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($subcontractor);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $subcontractor = Subcontractor::findOrFail($id);

        // Check company ownership
        if ($subcontractor->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'subcontractor_type' => 'required|in:specialized,general,labor_only,materials_labor',
            'trade_category' => 'required|in:civil,electrical,mechanical,plumbing,finishing,landscaping,other',
            'tax_number' => 'nullable|string|max:255|unique:subcontractors,tax_number,' . $id,
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'payment_terms' => 'required|in:cod,7_days,15_days,30_days,45_days,60_days',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $subcontractor->update($request->all());

        return response()->json($subcontractor->load(['country', 'city', 'currency']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subcontractor = Subcontractor::findOrFail($id);

        // Check company ownership
        if ($subcontractor->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $subcontractor->delete();

        return response()->json(['message' => 'Subcontractor deleted successfully']);
    }

    /**
     * Approve a subcontractor.
     */
    public function approve(string $id)
    {
        $subcontractor = Subcontractor::findOrFail($id);

        // Check company ownership
        if ($subcontractor->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $subcontractor->update([
            'is_approved' => true,
            'approved_by_id' => Auth::id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Subcontractor approved successfully',
            'subcontractor' => $subcontractor
        ]);
    }

    /**
     * Blacklist a subcontractor.
     */
    public function blacklist(Request $request, string $id)
    {
        $subcontractor = Subcontractor::findOrFail($id);

        // Check company ownership
        if ($subcontractor->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'blacklist_reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $subcontractor->update([
            'is_blacklisted' => true,
            'blacklist_reason' => $request->blacklist_reason,
        ]);

        return response()->json([
            'message' => 'Subcontractor blacklisted successfully',
            'subcontractor' => $subcontractor
        ]);
    }
}
