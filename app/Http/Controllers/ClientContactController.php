<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientContact;
use App\Http\Requests\StoreClientContactRequest;
use Illuminate\Http\Request;

class ClientContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Client $client)
    {
        $contacts = $client->contacts()->latest()->get();
        return response()->json($contacts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientContactRequest $request, Client $client)
    {
        $data = $request->validated();
        $data['client_id'] = $client->id;
        $data['is_primary'] = $request->has('is_primary') ? 1 : 0;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        
        $contact = ClientContact::create($data);

        return redirect()->route('clients.show', $client)
            ->with('success', 'تم إضافة جهة الاتصال بنجاح');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreClientContactRequest $request, Client $client, ClientContact $contact)
    {
        $data = $request->validated();
        $data['is_primary'] = $request->has('is_primary') ? 1 : 0;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        
        $contact->update($data);

        return redirect()->route('clients.show', $client)
            ->with('success', 'تم تحديث جهة الاتصال بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client, ClientContact $contact)
    {
        $contact->delete();

        return redirect()->route('clients.show', $client)
            ->with('success', 'تم حذف جهة الاتصال بنجاح');
    }

    /**
     * Set contact as primary
     */
    public function setPrimary(Client $client, ClientContact $contact)
    {
        $contact->update(['is_primary' => true]);

        return redirect()->route('clients.show', $client)
            ->with('success', 'تم تعيين جهة الاتصال كأساسية');
    }
}
