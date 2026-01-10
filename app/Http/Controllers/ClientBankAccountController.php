<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientBankAccount;
use App\Http\Requests\StoreClientBankAccountRequest;
use Illuminate\Http\Request;

class ClientBankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Client $client)
    {
        $bankAccounts = $client->bankAccounts()->latest()->get();
        return response()->json($bankAccounts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientBankAccountRequest $request, Client $client)
    {
        $data = $request->validated();
        $data['client_id'] = $client->id;
        $data['is_primary'] = $request->has('is_primary') ? 1 : 0;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        
        $bankAccount = ClientBankAccount::create($data);

        return redirect()->route('clients.show', $client)
            ->with('success', 'تم إضافة الحساب البنكي بنجاح');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreClientBankAccountRequest $request, Client $client, ClientBankAccount $bankAccount)
    {
        $data = $request->validated();
        $data['is_primary'] = $request->has('is_primary') ? 1 : 0;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        
        $bankAccount->update($data);

        return redirect()->route('clients.show', $client)
            ->with('success', 'تم تحديث الحساب البنكي بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client, ClientBankAccount $bankAccount)
    {
        $bankAccount->delete();

        return redirect()->route('clients.show', $client)
            ->with('success', 'تم حذف الحساب البنكي بنجاح');
    }

    /**
     * Set bank account as primary
     */
    public function setPrimary(Client $client, ClientBankAccount $bankAccount)
    {
        $bankAccount->update(['is_primary' => true]);

        return redirect()->route('clients.show', $client)
            ->with('success', 'تم تعيين الحساب البنكي كأساسي');
    }
}
