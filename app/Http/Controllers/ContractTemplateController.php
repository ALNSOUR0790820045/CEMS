<?php

namespace App\Http\Controllers;

use App\Models\ContractTemplate;
use App\Models\ContractGenerated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractTemplateController extends Controller
{
    public function index()
    {
        $templates = ContractTemplate::active()
            ->with(['clauses' => function($query) {
                $query->mainClauses();
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('contract-templates.index', compact('templates'));
    }

    public function show(ContractTemplate $contractTemplate)
    {
        $contractTemplate->load([
            'clauses' => function($query) {
                $query->mainClauses();
            },
            'specialConditions',
            'variables'
        ]);

        return view('contract-templates.show', compact('contractTemplate'));
    }

    public function clauses(ContractTemplate $contractTemplate)
    {
        $contractTemplate->load(['clauses' => function($query) {
            $query->orderBy('sort_order');
        }]);

        return view('contract-templates.clauses', compact('contractTemplate'));
    }

    public function generate(ContractTemplate $contractTemplate)
    {
        $contractTemplate->load(['variables', 'clauses', 'specialConditions']);

        return view('contract-templates.generate', compact('contractTemplate'));
    }

    public function storeGenerated(Request $request)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:contract_templates,id',
            'contract_title' => 'required|string|max:255',
            'parties' => 'required|array',
            'filled_data' => 'required|array',
            'modified_clauses' => 'nullable|array',
            'added_special_conditions' => 'nullable|array',
        ]);

        $validated['generated_by'] = Auth::id();
        $validated['status'] = 'draft';

        $contract = ContractGenerated::create($validated);

        return redirect()
            ->route('contract-templates.preview', $contract->id)
            ->with('success', 'تم إنشاء العقد بنجاح');
    }

    public function preview($id)
    {
        $contract = ContractGenerated::with(['template.clauses', 'template.specialConditions'])
            ->findOrFail($id);

        return view('contract-templates.preview', compact('contract'));
    }

    public function jea01()
    {
        $template = ContractTemplate::where('code', 'JEA-01')->firstOrFail();
        $template->load(['clauses', 'specialConditions', 'variables']);

        return view('contract-templates.jea-01', compact('template'));
    }

    public function jea02()
    {
        $template = ContractTemplate::where('code', 'JEA-02')->firstOrFail();
        $template->load(['clauses', 'specialConditions', 'variables']);

        return view('contract-templates.jea-02', compact('template'));
    }

    public function exportWord($id)
    {
        $contract = ContractGenerated::with(['template'])->findOrFail($id);
        
        // TODO: Implement Word export using PHPWord
        return response()->json([
            'message' => 'Word export functionality to be implemented',
            'contract_id' => $id
        ]);
    }

    public function exportPdf($id)
    {
        $contract = ContractGenerated::with(['template'])->findOrFail($id);
        
        // TODO: Implement PDF export using DomPDF
        return response()->json([
            'message' => 'PDF export functionality to be implemented',
            'contract_id' => $id
        ]);
    }
}
