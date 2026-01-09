<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Client;
use App\Models\Currency;
use App\Models\GLAccount;
use App\Models\User;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Contract::with(['client', 'currency', 'contractManager', 'company'])
            ->where('company_id', Auth::user()->company_id);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('contract_status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('contract_code', 'like', "%{$search}%")
                  ->orWhere('contract_number', 'like', "%{$search}%")
                  ->orWhere('contract_title', 'like', "%{$search}%");
            });
        }

        $contracts = $query->latest()->paginate(20);

        // KPI calculations
        $activeContracts = Contract::where('company_id', Auth::user()->company_id)
            ->where('contract_status', 'active')
            ->count();

        $totalContractValue = Contract::where('company_id', Auth::user()->company_id)
            ->whereIn('contract_status', ['active', 'signed'])
            ->sum('current_contract_value');

        $pendingChangeOrders = Contract::where('company_id', Auth::user()->company_id)
            ->whereHas('changeOrders', function ($q) {
                $q->whereIn('status', ['submitted', 'under_review']);
            })
            ->count();

        $expiringSoon = Contract::where('company_id', Auth::user()->company_id)
            ->expiringSoon(30)
            ->count();

        return view('contracts.index', compact(
            'contracts',
            'activeContracts',
            'totalContractValue',
            'pendingChangeOrders',
            'expiringSoon'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();

        $currencies = Currency::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();

        $glAccounts = GLAccount::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();

        $users = User::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();

        $contractCode = Contract::generateContractCode();

        return view('contracts.create', compact(
            'clients',
            'currencies',
            'glAccounts',
            'users',
            'contractCode'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContractRequest $request)
    {
        $validated = $request->validated();
        $validated['company_id'] = Auth::user()->company_id;

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('contracts', 'public');
            $validated['attachment_path'] = $path;
        }

        $contract = Contract::create($validated);

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'تم إنشاء العقد بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract)
    {
        // Authorization check
        if ($contract->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $contract->load([
            'client',
            'currency',
            'contractManager',
            'projectManager',
            'glRevenueAccount',
            'glReceivableAccount',
            'changeOrders.submittedBy',
            'changeOrders.approvedBy',
            'amendments.approvedBy',
            'clauses',
            'milestones.responsiblePerson'
        ]);

        return view('contracts.show', compact('contract'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contract $contract)
    {
        // Authorization check
        if ($contract->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        // Only allow editing of draft or under_negotiation contracts
        if (!in_array($contract->contract_status, ['draft', 'under_negotiation'])) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'لا يمكن تعديل العقد في هذه الحالة');
        }

        $clients = Client::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();

        $currencies = Currency::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();

        $glAccounts = GLAccount::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();

        $users = User::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();

        return view('contracts.edit', compact(
            'contract',
            'clients',
            'currencies',
            'glAccounts',
            'users'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContractRequest $request, Contract $contract)
    {
        // Authorization check
        if ($contract->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        // Only allow editing of draft or under_negotiation contracts
        if (!in_array($contract->contract_status, ['draft', 'under_negotiation'])) {
            return redirect()->route('contracts.show', $contract)
                ->with('error', 'لا يمكن تعديل العقد في هذه الحالة');
        }

        $validated = $request->validated();

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old file if exists
            if ($contract->attachment_path) {
                Storage::disk('public')->delete($contract->attachment_path);
            }
            $path = $request->file('attachment')->store('contracts', 'public');
            $validated['attachment_path'] = $path;
        }

        $contract->update($validated);

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'تم تحديث العقد بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contract $contract)
    {
        // Authorization check
        if ($contract->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        // Only allow deletion of draft contracts
        if ($contract->contract_status !== 'draft') {
            return redirect()->route('contracts.index')
                ->with('error', 'لا يمكن حذف العقد إلا في حالة المسودة');
        }

        // Delete attachment file if exists
        if ($contract->attachment_path) {
            Storage::disk('public')->delete($contract->attachment_path);
        }

        $contract->delete();

        return redirect()->route('contracts.index')
            ->with('success', 'تم حذف العقد بنجاح');
    }

    /**
     * Clone an existing contract
     */
    public function clone(Contract $contract)
    {
        // Authorization check
        if ($contract->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $newContract = $contract->replicate();
        $newContract->contract_code = null; // Will be auto-generated
        $newContract->contract_status = 'draft';
        $newContract->contract_number = $contract->contract_number . '-COPY';
        $newContract->save();

        return redirect()->route('contracts.edit', $newContract)
            ->with('success', 'تم نسخ العقد بنجاح');
    }

    /**
     * Generate new contract code
     */
    public function generateCode()
    {
        return response()->json([
            'contract_code' => Contract::generateContractCode()
        ]);
    }
}
