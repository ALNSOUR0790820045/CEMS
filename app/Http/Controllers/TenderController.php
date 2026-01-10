<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use App\Models\Company;
use Illuminate\Http\Request;

class TenderController extends Controller
{
    public function index()
    {
        $tenders = Tender::with('company')->orderBy('created_at', 'desc')->paginate(20);
        return view('tenders.index', compact('tenders'));
    }

    public function create()
    {
        $companies = Company::where('is_active', true)->get();
        return view('tenders.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'code' => 'required|unique:tenders,code',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'submission_date' => 'nullable|date',
            'estimated_value' => 'nullable|numeric|min:0',
        ]);

        $tender = Tender::create($validated);

        return redirect()->route('tender-risks.dashboard', $tender->id)
            ->with('success', 'تم إنشاء العطاء بنجاح');
    }

    public function show($id)
    {
        $tender = Tender::with('company', 'risks')->findOrFail($id);
        return view('tenders.show', compact('tender'));
    }

    public function edit($id)
    {
        $tender = Tender::findOrFail($id);
        $companies = Company::where('is_active', true)->get();
        return view('tenders.edit', compact('tender', 'companies'));
    }

    public function update(Request $request, $id)
    {
        $tender = Tender::findOrFail($id);

        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'code' => 'required|unique:tenders,code,' . $id,
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'submission_date' => 'nullable|date',
            'estimated_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,submitted,won,lost',
        ]);

        $tender->update($validated);

        return redirect()->route('tenders.show', $tender->id)
            ->with('success', 'تم تحديث العطاء بنجاح');
    }

    public function destroy($id)
    {
        $tender = Tender::findOrFail($id);
        $tender->delete();

        return redirect()->route('tenders.index')
            ->with('success', 'تم حذف العطاء بنجاح');
    }
}
