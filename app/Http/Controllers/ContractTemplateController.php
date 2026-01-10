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
        $contract = ContractGenerated::with(['template.clauses', 'template.specialConditions'])->findOrFail($id);
        
        // Check if PHPWord is available
        if (!class_exists('\PhpOffice\PhpWord\PhpWord')) {
            return response()->json([
                'success' => false,
                'message' => 'PHPWord package is not installed. Please run: composer require phpoffice/phpword'
            ], 500);
        }
        
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        
        // Add title
        $section->addTitle($contract->contract_title, 1);
        
        // Add parties
        if ($contract->parties && is_array($contract->parties)) {
            $section->addTitle('Parties', 2);
            foreach ($contract->parties as $party) {
                $partyName = $party['name'] ?? '';
                $partyDetails = $party['details'] ?? '';
                $section->addText($partyName . ': ' . $partyDetails);
            }
        }
        
        // Add clauses from template
        if ($contract->template && $contract->template->clauses) {
            $section->addTitle('Contract Clauses', 2);
            foreach ($contract->template->clauses as $clause) {
                $section->addTitle($clause->title, 3);
                $section->addText(strip_tags($clause->content));
            }
        }
        
        // Save to temporary location
        $filename = 'contract_' . $contract->id . '_' . time() . '.docx';
        $path = storage_path('app/temp/' . $filename);
        
        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($path);
        
        return response()->download($path)->deleteFileAfterSend();
    }

    public function exportPdf($id)
    {
        $contract = ContractGenerated::with(['template.clauses', 'template.specialConditions'])->findOrFail($id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('contract-templates.pdf', [
            'contract' => $contract,
            'template' => $contract->template,
        ]);
        
        $filename = 'contract_' . $contract->id . '_' . time() . '.pdf';
        
        return $pdf->download($filename);
    }
}
