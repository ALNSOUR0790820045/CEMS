<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ExpenseCategory::with(['company', 'glAccount'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $categories = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:expense_categories,code',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'gl_account_id' => 'nullable|exists:gl_accounts,id',
            'spending_limit' => 'nullable|numeric|min:0',
            'requires_receipt' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = ExpenseCategory::create([
            'code' => $request->code,
            'name' => $request->name,
            'name_en' => $request->name_en,
            'gl_account_id' => $request->gl_account_id,
            'spending_limit' => $request->spending_limit,
            'requires_receipt' => $request->requires_receipt ?? false,
            'is_active' => $request->is_active ?? true,
            'company_id' => $request->user()->company_id,
        ]);

        return response()->json($category->load(['company', 'glAccount']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = ExpenseCategory::with(['company', 'glAccount', 'pettyCashTransactions'])
            ->findOrFail($id);

        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = ExpenseCategory::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:expense_categories,code,' . $id,
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'gl_account_id' => 'nullable|exists:gl_accounts,id',
            'spending_limit' => 'nullable|numeric|min:0',
            'requires_receipt' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category->update($request->all());

        return response()->json($category->load(['company', 'glAccount']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = ExpenseCategory::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Expense category deleted successfully']);
    }
}
