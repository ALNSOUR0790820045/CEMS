<?php

namespace App\Http\Controllers;

use App\Models\RiskRegister;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiskRegisterController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = RiskRegister::with(['project', 'preparedBy', 'approvedBy']);

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $registers = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $registers,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'version' => 'nullable|string',
            'review_frequency' => 'required|in:weekly,monthly,quarterly',
            'company_id' => 'required|exists:companies,id',
        ]);

        $validated['prepared_by_id'] = Auth::id();
        $validated['status'] = 'draft';

        $register = RiskRegister::create($validated);
        $register->load(['project', 'preparedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Risk register created successfully',
            'data' => $register,
        ], 201);
    }

    public function show(RiskRegister $riskRegister): JsonResponse
    {
        $riskRegister->load(['project', 'preparedBy', 'approvedBy', 'risks']);

        return response()->json([
            'success' => true,
            'data' => $riskRegister,
        ]);
    }

    public function update(Request $request, RiskRegister $riskRegister): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'version' => 'nullable|string',
            'review_frequency' => 'sometimes|in:weekly,monthly,quarterly',
        ]);

        $riskRegister->update($validated);
        $riskRegister->load(['project', 'preparedBy', 'approvedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Risk register updated successfully',
            'data' => $riskRegister,
        ]);
    }

    public function destroy(RiskRegister $riskRegister): JsonResponse
    {
        $riskRegister->delete();

        return response()->json([
            'success' => true,
            'message' => 'Risk register deleted successfully',
        ]);
    }

    public function byProject($projectId): JsonResponse
    {
        $registers = RiskRegister::where('project_id', $projectId)
            ->with(['preparedBy', 'approvedBy'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $registers,
        ]);
    }

    public function approve(Request $request, $id): JsonResponse
    {
        $register = RiskRegister::findOrFail($id);
        $register->approve(Auth::user());

        return response()->json([
            'success' => true,
            'message' => 'Risk register approved successfully',
            'data' => $register,
        ]);
    }
}
