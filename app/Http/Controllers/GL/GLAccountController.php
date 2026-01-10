<?php

namespace App\Http\Controllers\GL;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGLAccountRequest;
use App\Http\Requests\UpdateGLAccountRequest;
use App\Models\GLAccount;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GLAccountController extends Controller
{
    /**
     * Display a listing of GL accounts (Chart of Accounts).
     */
    public function index(Request $request)
    {
        $query = GLAccount::with(['parentAccount', 'currency'])
            ->where('company_id', Auth::user()->company_id);
        
        // Filter by account type
        if ($request->filled('account_type')) {
            $query->where('account_type', $request->account_type);
        }
        
        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('account_code', 'LIKE', "%{$request->search}%")
                  ->orWhere('account_name', 'LIKE', "%{$request->search}%");
            });
        }
        
        // Get tree structure (main accounts with children)
        $accounts = $query->orderBy('account_code')->get();
        
        // Build tree structure
        $tree = $this->buildTree($accounts);
        
        return view('gl.accounts.index', compact('tree', 'accounts'));
    }

    /**
     * Show the form for creating a new GL account.
     */
    public function create(Request $request)
    {
        $parentAccounts = GLAccount::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();
            
        $currencies = Currency::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();
        
        $parentId = $request->query('parent_id');
        
        return view('gl.accounts.create', compact('parentAccounts', 'currencies', 'parentId'));
    }

    /**
     * Store a newly created GL account.
     */
    public function store(StoreGLAccountRequest $request)
    {
        $account = GLAccount::create([
            ...$request->validated(),
            'company_id' => Auth::user()->company_id,
        ]);
        
        return redirect()->route('gl.accounts.index')
            ->with('success', 'GL Account created successfully.');
    }

    /**
     * Display the specified GL account.
     */
    public function show(GLAccount $account)
    {
        $account->load(['parentAccount', 'childAccounts', 'currency']);
        
        // Get recent transactions for this account
        $recentTransactions = $account->journalEntryLines()
            ->with('journalEntry')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('gl.accounts.show', compact('account', 'recentTransactions'));
    }

    /**
     * Show the form for editing the specified GL account.
     */
    public function edit(GLAccount $account)
    {
        $parentAccounts = GLAccount::where('company_id', Auth::user()->company_id)
            ->where('id', '!=', $account->id)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();
            
        $currencies = Currency::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();
        
        return view('gl.accounts.edit', compact('account', 'parentAccounts', 'currencies'));
    }

    /**
     * Update the specified GL account.
     */
    public function update(UpdateGLAccountRequest $request, GLAccount $account)
    {
        $account->update($request->validated());
        
        return redirect()->route('gl.accounts.index')
            ->with('success', 'GL Account updated successfully.');
    }

    /**
     * Remove the specified GL account (soft delete).
     */
    public function destroy(GLAccount $account)
    {
        // Check if account has transactions
        if ($account->journalEntryLines()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete an account with transactions.']);
        }
        
        // Check if account has child accounts
        if ($account->childAccounts()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete an account with sub-accounts.']);
        }
        
        $account->delete();
        
        return redirect()->route('gl.accounts.index')
            ->with('success', 'GL Account deleted successfully.');
    }
    
    /**
     * Show account ledger.
     */
    public function ledger(GLAccount $account, Request $request)
    {
        $query = $account->journalEntryLines()
            ->with(['journalEntry', 'costCenter', 'project'])
            ->whereHas('journalEntry', function ($q) {
                $q->where('status', 'posted');
            });
        
        // Date filters
        if ($request->filled('date_from')) {
            $query->whereHas('journalEntry', function ($q) use ($request) {
                $q->where('entry_date', '>=', $request->date_from);
            });
        }
        
        if ($request->filled('date_to')) {
            $query->whereHas('journalEntry', function ($q) use ($request) {
                $q->where('entry_date', '<=', $request->date_to);
            });
        }
        
        $transactions = $query->orderBy('created_at', 'desc')->paginate(50);
        
        // Calculate running balance
        $balance = $account->opening_balance;
        foreach ($transactions as $transaction) {
            if (in_array($account->account_type, ['asset', 'expense'])) {
                $balance += $transaction->debit_amount - $transaction->credit_amount;
            } else {
                $balance += $transaction->credit_amount - $transaction->debit_amount;
            }
            $transaction->running_balance = $balance;
        }
        
        return view('gl.accounts.ledger', compact('account', 'transactions'));
    }
    
    /**
     * Build tree structure from flat collection.
     */
    protected function buildTree($accounts, $parentId = null)
    {
        $branch = [];
        
        foreach ($accounts as $account) {
            if ($account->parent_account_id == $parentId) {
                $children = $this->buildTree($accounts, $account->id);
                if ($children) {
                    $account->children_tree = $children;
                }
                $branch[] = $account;
            }
        }
        
        return $branch;
    }
}

