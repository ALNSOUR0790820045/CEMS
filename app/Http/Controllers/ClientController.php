<?php

namespace App\Http\Controllers;

use App\Models\Client;
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
        $query = Client::query()->with(['contacts', 'bankAccounts']);

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by type
        if ($request->filled('client_type')) {
            $query->byType($request->client_type);
        }

        // Filter by category
        if ($request->filled('client_category')) {
            $query->byCategory($request->client_category);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->byRating($request->rating);
        }

        // Filter by country
        if ($request->filled('country')) {
            $query->byCountry($request->country);
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->byCity($request->city);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            if ($request->is_active === '1' || $request->is_active === 'true') {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        $clients = $query->latest()->paginate(20);

        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientCode = Client::generateClientCode();
        return view('clients.create', compact('clientCode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        $data = $request->validated();
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

        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        $data = $request->validated();
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
