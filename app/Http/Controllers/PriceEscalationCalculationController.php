<?php

namespace App\Http\Controllers;

use App\Models\PriceEscalationContract;
use App\Models\PriceEscalationCalculation;
use App\Models\MainIpc;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PriceEscalationCalculationController extends Controller
{
    public function index()
    {
        $calculations = PriceEscalationCalculation::with(['contract.project', 'ipc'])
            ->latest()
            ->paginate(20);
        
        return view('price-escalation.calculations', compact('calculations'));
    }

    public function create()
    {
        $contracts = PriceEscalationContract::with('project')
            ->where('is_active', true)
            ->get();
        
        $ipcs = MainIpc::with('project')
            ->whereDoesntHave('priceEscalationCalculations')
            ->latest()
            ->get();
        
        return view('price-escalation.calculate', compact('contracts', 'ipcs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'price_escalation_contract_id' => 'required|exists:price_escalation_contracts,id',
            'main_ipc_id' => 'nullable|exists:main_ipcs,id',
            'period_from' => 'required|date',
            'period_to' => 'required|date|after:period_from',
            'calculation_date' => 'required|date',
        ]);
        
        try {
            $contract = PriceEscalationContract::findOrFail($validated['price_escalation_contract_id']);
            
            $ipcAmount = $request->input('ipc_amount');
            $ipc = null;
            
            if ($validated['main_ipc_id']) {
                $ipc = MainIpc::findOrFail($validated['main_ipc_id']);
                $ipcAmount = $ipc->amount;
            } else {
                if (!$ipcAmount) {
                    return back()->withErrors(['ipc_amount' => 'يجب إدخال مبلغ المستخلص'])->withInput();
                }
            }
            
            // Create a temporary IPC object if no IPC selected
            if (!$ipc) {
                $ipc = new MainIpc();
                $ipc->amount = $ipcAmount;
            }
            
            // Calculate escalation
            $calcData = PriceEscalationCalculation::calculateForIpc(
                $contract,
                $ipc,
                $validated['calculation_date']
            );
            
            // Create calculation record
            $calculation = PriceEscalationCalculation::create([
                'price_escalation_contract_id' => $contract->id,
                'main_ipc_id' => $validated['main_ipc_id'],
                'calculation_number' => PriceEscalationCalculation::generateCalculationNumber(),
                'calculation_date' => $validated['calculation_date'],
                'period_from' => $validated['period_from'],
                'period_to' => $validated['period_to'],
                ...$calcData,
                'status' => 'calculated',
            ]);
            
            return redirect()->route('price-escalation.calculations.show', $calculation)
                ->with('success', 'تم حساب فروقات الأسعار بنجاح');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'خطأ في الحساب: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(PriceEscalationCalculation $calculation)
    {
        $calculation->load(['contract.project', 'ipc', 'approvedBy']);
        
        return view('price-escalation.show', compact('calculation'));
    }

    public function approve(Request $request, PriceEscalationCalculation $calculation)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);
        
        $calculation->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'notes' => $validated['notes'] ?? $calculation->notes,
        ]);
        
        return back()->with('success', 'تم اعتماد فروقات الأسعار بنجاح');
    }

    public function reject(Request $request, PriceEscalationCalculation $calculation)
    {
        $validated = $request->validate([
            'notes' => 'required|string',
        ]);
        
        $calculation->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'notes' => $validated['notes'],
        ]);
        
        return back()->with('success', 'تم رفض فروقات الأسعار');
    }

    public function destroy(PriceEscalationCalculation $calculation)
    {
        if ($calculation->status === 'paid') {
            return back()->withErrors(['error' => 'لا يمكن حذف حساب تم دفعه']);
        }
        
        $calculation->delete();
        return redirect()->route('price-escalation.calculations')
            ->with('success', 'تم حذف الحساب بنجاح');
    }

    public function preview(Request $request)
    {
        try {
            $contract = PriceEscalationContract::findOrFail($request->input('contract_id'));
            $ipcAmount = (float) $request->input('ipc_amount');
            $calculationDate = $request->input('calculation_date');
            
            // Create temporary IPC
            $tempIpc = new MainIpc();
            $tempIpc->amount = $ipcAmount;
            
            $calcData = PriceEscalationCalculation::calculateForIpc(
                $contract,
                $tempIpc,
                $calculationDate
            );
            
            return response()->json([
                'success' => true,
                'data' => $calcData,
                'threshold_percentage' => $contract->threshold_percentage,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
