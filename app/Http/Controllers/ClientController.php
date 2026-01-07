<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\City;
use App\Models\Country;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Client::with(['city', 'country']);

        // Apply filters
        if ($request->filled('client_type')) {
            $query->where('client_type', $request->client_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('client_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $clients = $query->latest()->paginate(15);

        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = Country::active()->get();
        $cities = City::active()->get();

        return view('clients.create', compact('countries', 'cities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        $validated = $request->validated();
        
        // Generate client code
        $validated['client_code'] = $this->generateClientCode();
        
        // Add company_id from authenticated user
        if (!auth()->user()->company_id) {
            return redirect()->back()
                ->withErrors(['error' => 'لم يتم تعيين شركة للمستخدم. يرجى الاتصال بالمسؤول.'])
                ->withInput();
        }
        $validated['company_id'] = auth()->user()->company_id;
        
        // Handle checkbox
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'تم إضافة العميل بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $client->load(['city', 'country', 'projects']);

        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        $countries = Country::active()->get();
        $cities = City::active()->get();

        return view('clients.edit', compact('client', 'countries', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        $validated = $request->validated();
        
        // Handle checkbox
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'تم تحديث العميل بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();
        
        return redirect()->route('clients.index')
            ->with('success', 'تم حذف العميل بنجاح');
    }

    /**
     * Generate client code.
     */
    private function generateClientCode()
    {
        $year = date('Y');
        
        // Get the last client code for this year
        $lastClient = Client::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastClient && preg_match('/CLT-(\d{4})-(\d{4})/', $lastClient->client_code, $matches)) {
            $sequence = intval($matches[2]) + 1;
        } else {
            $sequence = 1;
        }
        
        return sprintf('CLT-%s-%04d', $year, $sequence);
    }
}
