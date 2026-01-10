<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = Account::with('children')->whereNull('parent_id')->orderBy('code')->get();
        return view('accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentAccounts = Account::where('is_parent', true)->orderBy('code')->get();
        return view('accounts.create', compact('parentAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:accounts',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:accounts,id',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'nature' => 'required|in:debit,credit',
            'level' => 'nullable|integer|min:1',
            'is_parent' => 'boolean',
            'is_active' => 'boolean',
            'opening_balance' => 'nullable|numeric',
            'current_balance' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        // Set default values
        $validated['is_parent'] = $request->has('is_parent') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        // Calculate level based on parent
        if ($validated['parent_id']) {
            $parent = Account::find($validated['parent_id']);
            $validated['level'] = $parent->level + 1;
        } else {
            $validated['level'] = 1;
        }

        Account::create($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'تم إضافة الحساب بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        $account->load(['parent', 'children']);
        return view('accounts.show', compact('account'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        $parentAccounts = Account::where('is_parent', true)
            ->where('id', '!=', $account->id)
            ->orderBy('code')
            ->get();
        return view('accounts.edit', compact('account', 'parentAccounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:accounts,code,' . $account->id,
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:accounts,id',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'nature' => 'required|in:debit,credit',
            'level' => 'nullable|integer|min:1',
            'is_parent' => 'boolean',
            'is_active' => 'boolean',
            'opening_balance' => 'nullable|numeric',
            'current_balance' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        // Set default values
        $validated['is_parent'] = $request->has('is_parent') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        // Calculate level based on parent
        if ($validated['parent_id']) {
            $parent = Account::find($validated['parent_id']);
            $validated['level'] = $parent->level + 1;
        } else {
            $validated['level'] = 1;
        }

        $account->update($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'تم تحديث الحساب بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        // Check if account has children
        if ($account->children()->count() > 0) {
            return redirect()->route('accounts.index')
                ->with('error', 'لا يمكن حذف الحساب لأنه يحتوي على حسابات فرعية');
        }

        $account->delete();
        return redirect()->route('accounts.index')
            ->with('success', 'تم حذف الحساب بنجاح');
    }
}
