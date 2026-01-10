<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubcontractorAgreement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubcontractorAgreementController extends Controller
{
    public function index(Request $request)
    {
        $query = SubcontractorAgreement::with(['subcontractor', 'project', 'currency'])
            ->where('company_id', Auth::user()->company_id);

        if ($request->has('subcontractor_id')) {
            $query->where('subcontractor_id', $request->subcontractor_id);
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        return response()->json($query->latest()->paginate($perPage));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agreement_date' => 'required|date',
            'subcontractor_id' => 'required|exists:subcontractors,id',
            'project_id' => 'required|exists:projects,id',
            'agreement_type' => 'required|in:lump_sum,unit_rate,time_material,cost_plus',
            'scope_of_work' => 'required|string',
            'contract_value' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['company_id'] = Auth::user()->company_id;
        $data['created_by_id'] = Auth::id();

        $agreement = SubcontractorAgreement::create($data);

        return response()->json($agreement->load(['subcontractor', 'project', 'currency']), 201);
    }

    public function show(string $id)
    {
        $agreement = SubcontractorAgreement::with([
            'subcontractor',
            'project',
            'contract',
            'currency',
            'workOrders',
            'ipcs'
        ])->findOrFail($id);

        if ($agreement->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($agreement);
    }

    public function update(Request $request, string $id)
    {
        $agreement = SubcontractorAgreement::findOrFail($id);

        if ($agreement->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'agreement_date' => 'required|date',
            'scope_of_work' => 'required|string',
            'contract_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $agreement->update($request->all());

        return response()->json($agreement->load(['subcontractor', 'project', 'currency']));
    }

    public function destroy(string $id)
    {
        $agreement = SubcontractorAgreement::findOrFail($id);

        if ($agreement->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $agreement->delete();

        return response()->json(['message' => 'Agreement deleted successfully']);
    }
}
