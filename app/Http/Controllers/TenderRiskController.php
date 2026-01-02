<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use App\Models\TenderRisk;
use App\Models\TenderContingencyReserve;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TenderRiskController extends Controller
{
    public function dashboard($tenderId)
    {
        $tender = Tender::with('risks')->findOrFail($tenderId);
        
        $kpis = [
            'total' => $tender->risks()->count(),
            'critical' => $tender->getCriticalRisksCount(),
            'high' => $tender->getHighRisksCount(),
            'medium' => $tender->getMediumRisksCount(),
            'low' => $tender->getLowRisksCount(),
        ];

        // Risk Matrix Data
        $matrixData = [];
        for ($prob = 1; $prob <= 5; $prob++) {
            for ($impact = 1; $impact <= 5; $impact++) {
                $count = $tender->risks()
                    ->where('probability_score', $prob)
                    ->where('impact_score', $impact)
                    ->count();
                $matrixData[$prob][$impact] = $count;
            }
        }

        return view('tender-risks.dashboard', compact('tender', 'kpis', 'matrixData'));
    }

    public function index($tenderId, Request $request)
    {
        $tender = Tender::findOrFail($tenderId);
        
        $query = $tender->risks()->with('owner');

        // Filters
        if ($request->filled('risk_level')) {
            $query->where('risk_level', $request->risk_level);
        }
        if ($request->filled('risk_category')) {
            $query->where('risk_category', $request->risk_category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort by risk score descending
        $risks = $query->orderBy('risk_score', 'desc')->paginate(20);

        return view('tender-risks.index', compact('tender', 'risks'));
    }

    public function create($tenderId)
    {
        $tender = Tender::findOrFail($tenderId);
        $users = User::all();
        
        // Generate next risk code
        $riskCode = $this->generateNextRiskCode($tenderId);

        return view('tender-risks.create', compact('tender', 'users', 'riskCode'));
    }

    /**
     * Generate the next risk code for a tender
     */
    private function generateNextRiskCode($tenderId): string
    {
        $lastRisk = TenderRisk::where('tender_id', $tenderId)
            ->orderBy('id', 'desc')
            ->first();
        
        if (!$lastRisk) {
            return 'RISK-001';
        }
        
        // Extract number from last risk code (e.g., RISK-001 -> 001)
        preg_match('/(\d+)$/', $lastRisk->risk_code, $matches);
        $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
        $nextNumber = $lastNumber + 1;
        
        return 'RISK-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function store($tenderId, Request $request)
    {
        $validated = $request->validate([
            'risk_code' => [
                'required',
                'string',
                Rule::unique('tender_risks')->where(function ($query) use ($tenderId) {
                    return $query->where('tender_id', $tenderId);
                }),
            ],
            'risk_category' => 'required|in:technical,financial,contractual,schedule,resources,external,safety,quality,political,environmental,other',
            'risk_title' => 'required|string|max:255',
            'risk_description' => 'required|string',
            'probability' => 'required|in:very_low,low,medium,high,very_high',
            'probability_score' => 'required|integer|min:1|max:5',
            'impact' => 'required|in:very_low,low,medium,high,very_high',
            'impact_score' => 'required|integer|min:1|max:5',
            'cost_impact_min' => 'nullable|numeric|min:0',
            'cost_impact_max' => 'nullable|numeric|min:0',
            'cost_impact_expected' => 'nullable|numeric|min:0',
            'schedule_impact_days' => 'nullable|integer|min:0',
            'response_strategy' => 'nullable|in:avoid,mitigate,transfer,accept',
            'response_plan' => 'nullable|string',
            'response_cost' => 'nullable|numeric|min:0',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        $validated['tender_id'] = $tenderId;

        TenderRisk::create($validated);

        return redirect()->route('tender-risks.index', $tenderId)
            ->with('success', 'تم تسجيل المخاطرة بنجاح');
    }

    public function edit($tenderId, $id)
    {
        $tender = Tender::findOrFail($tenderId);
        $risk = TenderRisk::where('tender_id', $tenderId)->findOrFail($id);
        $users = User::all();

        return view('tender-risks.edit', compact('tender', 'risk', 'users'));
    }

    public function update($tenderId, $id, Request $request)
    {
        $risk = TenderRisk::where('tender_id', $tenderId)->findOrFail($id);

        $validated = $request->validate([
            'risk_code' => [
                'required',
                'string',
                Rule::unique('tender_risks')->where(function ($query) use ($tenderId, $id) {
                    return $query->where('tender_id', $tenderId)->where('id', '!=', $id);
                }),
            ],
            'risk_category' => 'required|in:technical,financial,contractual,schedule,resources,external,safety,quality,political,environmental,other',
            'risk_title' => 'required|string|max:255',
            'risk_description' => 'required|string',
            'probability' => 'required|in:very_low,low,medium,high,very_high',
            'probability_score' => 'required|integer|min:1|max:5',
            'impact' => 'required|in:very_low,low,medium,high,very_high',
            'impact_score' => 'required|integer|min:1|max:5',
            'cost_impact_min' => 'nullable|numeric|min:0',
            'cost_impact_max' => 'nullable|numeric|min:0',
            'cost_impact_expected' => 'nullable|numeric|min:0',
            'schedule_impact_days' => 'nullable|integer|min:0',
            'response_strategy' => 'nullable|in:avoid,mitigate,transfer,accept',
            'response_plan' => 'nullable|string',
            'response_cost' => 'nullable|numeric|min:0',
            'status' => 'required|in:identified,assessed,planned,monitored,closed',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        $risk->update($validated);

        return redirect()->route('tender-risks.index', $tenderId)
            ->with('success', 'تم تحديث المخاطرة بنجاح');
    }

    public function destroy($tenderId, $id)
    {
        $risk = TenderRisk::where('tender_id', $tenderId)->findOrFail($id);
        $risk->delete();

        return redirect()->route('tender-risks.index', $tenderId)
            ->with('success', 'تم حذف المخاطرة بنجاح');
    }

    public function matrix($tenderId)
    {
        $tender = Tender::with('risks')->findOrFail($tenderId);
        
        // Group risks by probability and impact
        $matrixData = [];
        foreach ($tender->risks as $risk) {
            $matrixData[$risk->probability_score][$risk->impact_score][] = $risk;
        }

        return view('tender-risks.matrix', compact('tender', 'matrixData'));
    }

    public function contingency($tenderId)
    {
        $tender = Tender::with('risks', 'contingencyReserve')->findOrFail($tenderId);
        
        // Calculate total risk exposure
        $totalExposure = $tender->calculateTotalRiskExposure();
        
        // Get or create contingency reserve
        $reserve = $tender->contingencyReserve;
        if (!$reserve) {
            $reserve = new TenderContingencyReserve([
                'tender_id' => $tenderId,
                'total_risk_exposure' => $totalExposure,
                'contingency_percentage' => 10.00,
            ]);
        } else {
            $reserve->total_risk_exposure = $totalExposure;
        }
        
        return view('tender-risks.contingency', compact('tender', 'reserve', 'totalExposure'));
    }

    public function updateContingency($tenderId, Request $request)
    {
        $tender = Tender::findOrFail($tenderId);
        
        $validated = $request->validate([
            'contingency_percentage' => 'required|numeric|min:0|max:100',
            'justification' => 'nullable|string',
        ]);

        $totalExposure = $tender->calculateTotalRiskExposure();
        $validated['total_risk_exposure'] = $totalExposure;

        $reserve = $tender->contingencyReserve()->updateOrCreate(
            ['tender_id' => $tenderId],
            $validated
        );

        return redirect()->route('tender-risks.contingency', $tenderId)
            ->with('success', 'تم تحديث احتياطي المخاطر بنجاح');
    }

    public function responsePlan($tenderId)
    {
        $tender = Tender::with(['risks' => function ($query) {
            $query->whereNotNull('response_strategy')
                ->orderBy('risk_score', 'desc');
        }, 'risks.owner'])->findOrFail($tenderId);

        return view('tender-risks.response-plan', compact('tender'));
    }
}
