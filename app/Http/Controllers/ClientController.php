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
        $query = Client::query()->with(['contacts', 'bankAccounts', 'city', 'country']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('client_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('tax_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('client_type')) {
            $query->where('client_type', $request->client_type);
        }

        // Filter by category
        if ($request->filled('client_category')) {
            $query->where('client_category', $request->client_category);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by country
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        // Filter by city
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            if ($request->is_active === '1' || $request->is_active === 'true') {
                $query->where('is_active', true);
            } else {
                $query->where('is_active', false);
            }
        }

        $clients = $query->latest()->paginate(20);
        
        $countries = Country::active()->get();
        $cities = City::active()->get();

        return view('clients.index', compact('clients', 'countries', 'cities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientCode = Client::generateClientCode();
        $countries = Country::active()->get();
        $cities = City::active()->get();

        return view('clients.create', compact('clientCode', 'countries', 'cities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        $data = $request->validated();
        
        // Generate client code if not provided
        if (empty($data['client_code'])) {
            $data['client_code'] = Client::generateClientCode();
        }
        
        // Add company_id from authenticated user
        if (! auth()->user()->company_id) {
            return redirect()->back()
                ->withErrors(['error' => 'لم يتم تعيين شركة للمستخدم. يرجى الاتصال بالمسؤول.'])
                ->withInput();
        }
        $data['company_id'] = auth()->user()->company_id;
        
        // Handle checkbox
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        
        $client = Client::create($data);

        return redirect()->route('clients.show', $client)
            ->with('success', 'تم إضافة العميل بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $client->load([
            'city',
            'country',
            'projects',
            'contacts' => function ($query) {
                $query->orderBy('is_primary', 'desc')->orderBy('created_at', 'desc');
            },
            'bankAccounts' => function ($query) {
                $query->orderBy('is_primary', 'desc')->orderBy('created_at', 'desc');
            },
            'documents' => function ($query) {
                $query->latest();
            }
        ]);

        return view('clients. show', compact('client'));
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
        $data = $request->validated();
        
        // Handle checkbox
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        
        $client->update($data);

        return redirect()->route('clients.show', $client)
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
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        $client = Client::withTrashed()->findOrFail($id);
        $client->restore();

        return redirect()->route('clients.show', $client)
            ->with('success', 'تم استعادة العميل بنجاح');
    }

    /**
     * Generate next client code (API endpoint)
     */
    public function generateCode()
    {
        return response()->json([
            'code' => Client::generateClientCode()
        ]);
    }
}