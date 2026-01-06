<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use Illuminate\Http\Request;

class TenderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenders = Tender::with('company')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('tenders.index', compact('tenders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tenders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tender_code' => 'required|unique:tenders,tender_code',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'client_name' => 'nullable|string|max:255',
            'estimated_value' => 'nullable|numeric|min:0',
            'submission_date' => 'nullable|date',
            'opening_date' => 'nullable|date',
            'project_start_date' => 'nullable|date',
            'project_duration_days' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,submitted,won,lost,cancelled',
        ]);

        $tender = Tender::create($validated);

        return redirect()->route('tenders.show', $tender)
            ->with('success', 'Tender created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tender = Tender::with(['wbsItems', 'activities', 'milestones'])
            ->findOrFail($id);

        return view('tenders.show', compact('tender'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tender = Tender::findOrFail($id);

        return view('tenders.edit', compact('tender'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tender = Tender::findOrFail($id);

        $validated = $request->validate([
            'tender_code' => 'required|unique:tenders,tender_code,' . $id,
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'client_name' => 'nullable|string|max:255',
            'estimated_value' => 'nullable|numeric|min:0',
            'submission_date' => 'nullable|date',
            'opening_date' => 'nullable|date',
            'project_start_date' => 'nullable|date',
            'project_duration_days' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,submitted,won,lost,cancelled',
        ]);

        $tender->update($validated);

        return redirect()->route('tenders.show', $tender)
            ->with('success', 'Tender updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $tender = Tender::findOrFail($id);
            $tender->delete();

            return redirect()->route('tenders.index')
                ->with('success', 'Tender deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete tender: ' . $e->getMessage());
        }
    }
}
