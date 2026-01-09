<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PettyCashReplenishment;
use App\Models\PettyCashAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PettyCashReplenishmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PettyCashReplenishment::with([
                'pettyCashAccount', 'requestedBy', 'approvedBy'
            ])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('petty_cash_account_id')) {
            $query->where('petty_cash_account_id', $request->petty_cash_account_id);
        }

        $replenishments = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($replenishments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'replenishment_date' => 'required|date',
            'petty_cash_account_id' => 'required|exists:petty_cash_accounts,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,check,transfer',
            'reference_number' => 'nullable|string',
            'from_account_type' => 'nullable|in:cash,bank',
            'from_account_id' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $replenishment = DB::transaction(function () use ($request) {
            // Generate replenishment number
            $year = date('Y');
            $lastReplenishment = PettyCashReplenishment::where('replenishment_number', 'like', "PCR-{$year}-%")
                ->orderBy('id', 'desc')
                ->first();
            
            if ($lastReplenishment) {
                $lastNumber = (int) substr($lastReplenishment->replenishment_number, -4);
                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '0001';
            }
            
            $replenishmentNumber = "PCR-{$year}-{$newNumber}";

            $replenishment = PettyCashReplenishment::create([
                'replenishment_number' => $replenishmentNumber,
                'replenishment_date' => $request->replenishment_date,
                'petty_cash_account_id' => $request->petty_cash_account_id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'from_account_type' => $request->from_account_type,
                'from_account_id' => $request->from_account_id,
                'status' => 'pending',
                'requested_by_id' => $request->user()->id,
                'notes' => $request->notes,
                'company_id' => $request->user()->company_id,
            ]);

            return $replenishment;
        });

        return response()->json($replenishment->load([
            'pettyCashAccount', 'requestedBy'
        ]), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $replenishment = PettyCashReplenishment::with([
                'pettyCashAccount', 'requestedBy', 'approvedBy'
            ])
            ->findOrFail($id);

        return response()->json($replenishment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $replenishment = PettyCashReplenishment::findOrFail($id);

        // Only allow update if status is pending
        if ($replenishment->status !== 'pending') {
            return response()->json([
                'error' => 'Cannot update replenishment that is not pending'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'replenishment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,check,transfer',
            'reference_number' => 'nullable|string',
            'from_account_type' => 'nullable|in:cash,bank',
            'from_account_id' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $replenishment->update($request->all());

        return response()->json($replenishment->load([
            'pettyCashAccount', 'requestedBy'
        ]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $replenishment = PettyCashReplenishment::findOrFail($id);

        // Only allow delete if status is pending
        if ($replenishment->status !== 'pending') {
            return response()->json([
                'error' => 'Cannot delete replenishment that is not pending'
            ], 422);
        }

        $replenishment->delete();

        return response()->json(['message' => 'Replenishment deleted successfully']);
    }

    /**
     * Approve a replenishment.
     */
    public function approve(Request $request, string $id)
    {
        $replenishment = PettyCashReplenishment::findOrFail($id);

        if ($replenishment->status !== 'pending') {
            return response()->json([
                'error' => 'Replenishment is not pending approval'
            ], 422);
        }

        $replenishment->update([
            'status' => 'approved',
            'approved_by_id' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return response()->json($replenishment->load([
            'pettyCashAccount', 'approvedBy'
        ]));
    }

    /**
     * Complete a replenishment (add funds to petty cash).
     */
    public function complete(Request $request, string $id)
    {
        $replenishment = PettyCashReplenishment::findOrFail($id);

        if ($replenishment->status !== 'approved') {
            return response()->json([
                'error' => 'Replenishment must be approved before completion'
            ], 422);
        }

        DB::transaction(function () use ($replenishment) {
            $replenishment->update([
                'status' => 'completed',
            ]);

            // Update account balance
            $account = $replenishment->pettyCashAccount;
            $account->increment('current_balance', $replenishment->amount);
        });

        return response()->json($replenishment->load([
            'pettyCashAccount', 'approvedBy'
        ]));
    }
}
