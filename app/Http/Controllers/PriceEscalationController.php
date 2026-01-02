<?php

namespace App\Http\Controllers;

use App\Models\PriceEscalationContract;
use App\Models\PriceEscalationCalculation;
use App\Models\Project;
use App\Models\DsiIndex;
use Illuminate\Http\Request;

class PriceEscalationController extends Controller
{
    public function index()
    {
        $contracts = PriceEscalationContract::with('project')->latest()->get();
        return view('price-escalation.index', compact('contracts'));
    }

    public function dashboard()
    {
        // Get KPIs
        $totalEscalation = PriceEscalationCalculation::where('applied', true)->sum('escalation_amount');
        $approvedClaims = PriceEscalationCalculation::where('status', 'approved')->sum('escalation_amount');
        $pendingClaims = PriceEscalationCalculation::where('status', 'pending_approval')->sum('escalation_amount');
        
        // Get latest DSI indices
        $latestDsi = DsiIndex::latest('index_date')->first();
        $previousDsi = DsiIndex::where('year', $latestDsi?->month == 1 ? $latestDsi->year - 1 : $latestDsi?->year)
            ->where('month', $latestDsi?->month == 1 ? 12 : ($latestDsi?->month - 1))
            ->first();
        
        // Calculate monthly changes
        $materialsChange = 0;
        $laborChange = 0;
        
        if ($latestDsi && $previousDsi) {
            $materialsChange = (($latestDsi->materials_index - $previousDsi->materials_index) / $previousDsi->materials_index) * 100;
            $laborChange = (($latestDsi->labor_index - $previousDsi->labor_index) / $previousDsi->labor_index) * 100;
        }
        
        // Get DSI trend data (last 12 months)
        $dsiTrend = DsiIndex::orderByDesc('year')
            ->orderByDesc('month')
            ->limit(12)
            ->get()
            ->reverse()
            ->values();
        
        // Get escalation over time
        $escalationOverTime = PriceEscalationCalculation::selectRaw('
                DATE_FORMAT(calculation_date, "%Y-%m") as month,
                SUM(escalation_amount) as total
            ')
            ->where('applied', true)
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();
        
        // Get claimed vs paid
        $claimedVsPaid = PriceEscalationCalculation::selectRaw('
                status,
                SUM(escalation_amount) as total
            ')
            ->whereIn('status', ['approved', 'paid'])
            ->where('applied', true)
            ->groupBy('status')
            ->get();
        
        return view('price-escalation.dashboard', compact(
            'totalEscalation',
            'approvedClaims',
            'pendingClaims',
            'materialsChange',
            'laborChange',
            'latestDsi',
            'dsiTrend',
            'escalationOverTime',
            'claimedVsPaid'
        ));
    }

    public function contractSetup($projectId = null)
    {
        $projects = Project::whereDoesntHave('priceEscalationContract')
            ->orWhere('id', $projectId)
            ->get();
        
        $contract = null;
        if ($projectId) {
            $contract = PriceEscalationContract::where('project_id', $projectId)->first();
        }
        
        $dsiIndices = DsiIndex::orderByDesc('year')->orderByDesc('month')->limit(24)->get();
        
        return view('price-escalation.contract-setup', compact('projects', 'contract', 'dsiIndices'));
    }

    public function storeContract(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id|unique:price_escalation_contracts,project_id',
            'contract_date' => 'required|date',
            'contract_amount' => 'required|numeric|min:0',
            'formula_type' => 'required|in:dsi,fixed_percentage,custom_indices,none',
            'materials_weight' => 'required|numeric|min:0|max:100',
            'labor_weight' => 'required|numeric|min:0|max:100',
            'fixed_portion' => 'required|numeric|min:0|max:100',
            'threshold_percentage' => 'required|numeric|min:0|max:100',
            'max_escalation_percentage' => 'nullable|numeric|min:0|max:100',
            'calculation_frequency' => 'required|in:monthly,quarterly,per_ipc,annual',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date|after:effective_from',
        ]);
        
        // Validate weights sum to 100%
        $totalWeight = $validated['materials_weight'] + $validated['labor_weight'] + $validated['fixed_portion'];
        if (abs($totalWeight - 100) > 0.01) {
            return back()->withErrors(['weights' => 'يجب أن يكون مجموع النسب (المواد + العمالة + الثابت) = 100%'])->withInput();
        }
        
        $contract = PriceEscalationContract::create($validated);
        
        // Set base indices from DSI
        if ($validated['formula_type'] === 'dsi') {
            $contract->setBaseIndices();
        }
        
        return redirect()->route('price-escalation.index')
            ->with('success', 'تم إنشاء عقد فروقات الأسعار بنجاح');
    }

    public function updateContract(Request $request, PriceEscalationContract $contract)
    {
        $validated = $request->validate([
            'contract_date' => 'required|date',
            'contract_amount' => 'required|numeric|min:0',
            'formula_type' => 'required|in:dsi,fixed_percentage,custom_indices,none',
            'materials_weight' => 'required|numeric|min:0|max:100',
            'labor_weight' => 'required|numeric|min:0|max:100',
            'fixed_portion' => 'required|numeric|min:0|max:100',
            'threshold_percentage' => 'required|numeric|min:0|max:100',
            'max_escalation_percentage' => 'nullable|numeric|min:0|max:100',
            'calculation_frequency' => 'required|in:monthly,quarterly,per_ipc,annual',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'is_active' => 'boolean',
        ]);
        
        // Validate weights sum to 100%
        $totalWeight = $validated['materials_weight'] + $validated['labor_weight'] + $validated['fixed_portion'];
        if (abs($totalWeight - 100) > 0.01) {
            return back()->withErrors(['weights' => 'يجب أن يكون مجموع النسب (المواد + العمالة + الثابت) = 100%'])->withInput();
        }
        
        $contract->update($validated);
        
        // Update base indices if contract date changed
        if ($validated['formula_type'] === 'dsi' && $contract->wasChanged('contract_date')) {
            $contract->setBaseIndices();
        }
        
        return redirect()->route('price-escalation.index')
            ->with('success', 'تم تحديث عقد فروقات الأسعار بنجاح');
    }

    public function destroy(PriceEscalationContract $contract)
    {
        $contract->delete();
        return redirect()->route('price-escalation.index')
            ->with('success', 'تم حذف عقد فروقات الأسعار بنجاح');
    }
}
