<?php

namespace App\Http\Controllers;

use App\Models\PriceRequest;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PriceRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $requests = PriceRequest::with(['project', 'requester'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('prices.requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::where('is_active', true)->get();
        return view('prices.requests.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_number' => 'required|string|unique:price_requests,request_number',
            'project_id' => 'nullable|exists:projects,id',
            'request_date' => 'required|date',
            'required_by' => 'required|date|after:request_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_description' => 'required|string',
            'items.*.specifications' => 'nullable|string',
            'items.*.unit' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $priceRequest = PriceRequest::create([
                'request_number' => $validated['request_number'],
                'project_id' => $validated['project_id'] ?? null,
                'request_date' => $validated['request_date'],
                'required_by' => $validated['required_by'],
                'notes' => $validated['notes'] ?? null,
                'requested_by' => Auth::id(),
            ]);

            foreach ($validated['items'] as $item) {
                $priceRequest->items()->create($item);
            }

            DB::commit();
            
            return redirect()->route('price-requests.show', $priceRequest)
                ->with('success', 'تم إنشاء طلب عرض الأسعار بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء الطلب');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PriceRequest $priceRequest)
    {
        $priceRequest->load(['project', 'requester', 'items', 'quotations.vendor']);
        return view('prices.requests.show', compact('priceRequest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PriceRequest $priceRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,received,analyzed,closed',
            'notes' => 'nullable|string',
        ]);

        $priceRequest->update($validated);

        return redirect()->route('price-requests.show', $priceRequest)
            ->with('success', 'تم تحديث الطلب بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PriceRequest $priceRequest)
    {
        if ($priceRequest->status !== 'draft') {
            return back()->with('error', 'لا يمكن حذف طلب تم إرساله');
        }

        $priceRequest->delete();
        
        return redirect()->route('price-requests.index')
            ->with('success', 'تم حذف الطلب بنجاح');
    }

    /**
     * Mark request as sent
     */
    public function send(PriceRequest $priceRequest)
    {
        $priceRequest->update(['status' => 'sent']);
        
        return redirect()->route('price-requests.show', $priceRequest)
            ->with('success', 'تم إرسال الطلب بنجاح');
    }
}
