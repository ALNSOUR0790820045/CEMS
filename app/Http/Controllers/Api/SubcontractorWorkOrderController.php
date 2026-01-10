<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubcontractorWorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubcontractorWorkOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = SubcontractorWorkOrder::with(['subcontractor', 'project', 'agreement'])
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
            'work_order_date' => 'required|date',
            'subcontractor_agreement_id' => 'required|exists:subcontractor_agreements,id',
            'subcontractor_id' => 'required|exists:subcontractors,id',
            'project_id' => 'required|exists:projects,id',
            'work_description' => 'required|string',
            'order_value' => 'required|numeric|min:0',
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

        $workOrder = SubcontractorWorkOrder::create($data);

        return response()->json($workOrder->load(['subcontractor', 'project', 'agreement']), 201);
    }

    public function show(string $id)
    {
        $workOrder = SubcontractorWorkOrder::with([
            'subcontractor',
            'project',
            'agreement',
            'currency'
        ])->findOrFail($id);

        if ($workOrder->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($workOrder);
    }

    public function update(Request $request, string $id)
    {
        $workOrder = SubcontractorWorkOrder::findOrFail($id);

        if ($workOrder->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'work_order_date' => 'required|date',
            'work_description' => 'required|string',
            'order_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $workOrder->update($request->all());

        return response()->json($workOrder->load(['subcontractor', 'project', 'agreement']));
    }

    public function destroy(string $id)
    {
        $workOrder = SubcontractorWorkOrder::findOrFail($id);

        if ($workOrder->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $workOrder->delete();

        return response()->json(['message' => 'Work order deleted successfully']);
    }
}
