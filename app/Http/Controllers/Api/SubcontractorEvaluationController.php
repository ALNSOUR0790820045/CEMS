<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubcontractorEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubcontractorEvaluationController extends Controller
{
    public function index(Request $request)
    {
        $query = SubcontractorEvaluation::with(['subcontractor', 'project', 'evaluatedBy'])
            ->where('company_id', Auth::user()->company_id);

        if ($request->has('subcontractor_id')) {
            $query->where('subcontractor_id', $request->subcontractor_id);
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $perPage = $request->get('per_page', 15);
        return response()->json($query->latest()->paginate($perPage));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subcontractor_id' => 'required|exists:subcontractors,id',
            'project_id' => 'nullable|exists:projects,id',
            'evaluation_date' => 'required|date',
            'evaluation_period_from' => 'nullable|date',
            'evaluation_period_to' => 'nullable|date|after:evaluation_period_from',
            'quality_score' => 'required|integer|min:1|max:5',
            'time_performance_score' => 'required|integer|min:1|max:5',
            'safety_score' => 'required|integer|min:1|max:5',
            'cooperation_score' => 'required|integer|min:1|max:5',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['company_id'] = Auth::user()->company_id;
        $data['evaluated_by_id'] = Auth::id();

        $evaluation = SubcontractorEvaluation::create($data);

        return response()->json($evaluation->load(['subcontractor', 'project']), 201);
    }

    public function show(string $id)
    {
        $evaluation = SubcontractorEvaluation::with([
            'subcontractor',
            'project',
            'evaluatedBy'
        ])->findOrFail($id);

        if ($evaluation->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($evaluation);
    }

    public function update(Request $request, string $id)
    {
        $evaluation = SubcontractorEvaluation::findOrFail($id);

        if ($evaluation->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'evaluation_date' => 'required|date',
            'quality_score' => 'required|integer|min:1|max:5',
            'time_performance_score' => 'required|integer|min:1|max:5',
            'safety_score' => 'required|integer|min:1|max:5',
            'cooperation_score' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $evaluation->update($request->all());

        return response()->json($evaluation->load(['subcontractor', 'project']));
    }

    public function destroy(string $id)
    {
        $evaluation = SubcontractorEvaluation::findOrFail($id);

        if ($evaluation->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $evaluation->delete();

        return response()->json(['message' => 'Evaluation deleted successfully']);
    }
}
