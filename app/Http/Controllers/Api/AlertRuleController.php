<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlertRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AlertRuleController extends Controller
{
    public function index(Request $request)
    {
        $query = AlertRule::with('company');

        // Filter by company if user has company
        if ($request->user() && $request->user()->company_id) {
            $query->forCompany($request->user()->company_id);
        }

        // Filter by active status
        if ($request->has('active_only') && $request->active_only) {
            $query->active();
        }

        // Filter by rule type
        if ($request->has('rule_type')) {
            $query->where('rule_type', $request->rule_type);
        }

        $alertRules = $query->orderBy('created_at', 'desc')->get();

        return response()->json($alertRules);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rule_name' => 'required|string|max:255',
            'rule_type' => [
                'required',
                Rule::in([
                    'approval_pending',
                    'document_expiring',
                    'invoice_overdue',
                    'budget_exceeded',
                    'stock_low',
                    'certification_expiring'
                ])
            ],
            'trigger_condition' => 'required|array',
            'notification_template' => 'required|string',
            'target_users' => 'nullable|array',
            'target_roles' => 'nullable|array',
            'is_active' => 'boolean',
            'company_id' => 'required|exists:companies,id',
        ]);

        $alertRule = AlertRule::create($validated);

        return response()->json([
            'message' => 'Alert rule created successfully',
            'alert_rule' => $alertRule->load('company'),
        ], 201);
    }

    public function show(AlertRule $alertRule)
    {
        return response()->json($alertRule->load('company'));
    }

    public function update(Request $request, AlertRule $alertRule)
    {
        $validated = $request->validate([
            'rule_name' => 'sometimes|string|max:255',
            'rule_type' => [
                'sometimes',
                Rule::in([
                    'approval_pending',
                    'document_expiring',
                    'invoice_overdue',
                    'budget_exceeded',
                    'stock_low',
                    'certification_expiring'
                ])
            ],
            'trigger_condition' => 'sometimes|array',
            'notification_template' => 'sometimes|string',
            'target_users' => 'nullable|array',
            'target_roles' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $alertRule->update($validated);

        return response()->json([
            'message' => 'Alert rule updated successfully',
            'alert_rule' => $alertRule->fresh()->load('company'),
        ]);
    }

    public function destroy(AlertRule $alertRule)
    {
        $alertRule->delete();

        return response()->json([
            'message' => 'Alert rule deleted successfully',
        ]);
    }
}
