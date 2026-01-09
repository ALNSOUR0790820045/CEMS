<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContractTemplate;
use App\Models\ContractGenerated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractTemplateApiController extends Controller
{
    public function index()
    {
        $templates = ContractTemplate::active()
            ->with(['clauses' => function($query) {
                $query->mainClauses();
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $templates
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:contract_templates,code',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'type' => 'required|in:jea_01,jea_02,fidic_red,fidic_yellow,fidic_silver,ministry,custom',
            'version' => 'nullable|string',
            'year' => 'nullable|integer',
            'description' => 'nullable|string',
            'file_path' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $template = ContractTemplate::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء قالب العقد بنجاح',
            'data' => $template
        ], 201);
    }

    public function show($id)
    {
        $template = ContractTemplate::with([
            'clauses',
            'specialConditions',
            'variables'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $template
        ]);
    }

    public function update(Request $request, $id)
    {
        $template = ContractTemplate::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|unique:contract_templates,code,' . $id,
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'type' => 'required|in:jea_01,jea_02,fidic_red,fidic_yellow,fidic_silver,ministry,custom',
            'version' => 'nullable|string',
            'year' => 'nullable|integer',
            'description' => 'nullable|string',
            'file_path' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $template->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث قالب العقد بنجاح',
            'data' => $template
        ]);
    }

    public function clauses($id)
    {
        $template = ContractTemplate::findOrFail($id);
        $clauses = $template->clauses()->with(['children'])->mainClauses()->get();

        return response()->json([
            'success' => true,
            'data' => $clauses
        ]);
    }

    public function variables($id)
    {
        $template = ContractTemplate::findOrFail($id);
        $variables = $template->variables()->get();

        return response()->json([
            'success' => true,
            'data' => $variables
        ]);
    }

    public function generate(Request $request, $id)
    {
        $template = ContractTemplate::findOrFail($id);

        $validated = $request->validate([
            'contract_title' => 'required|string|max:255',
            'parties' => 'required|array',
            'filled_data' => 'required|array',
            'modified_clauses' => 'nullable|array',
            'added_special_conditions' => 'nullable|array',
        ]);

        $validated['template_id'] = $id;
        $validated['generated_by'] = Auth::id();
        $validated['status'] = 'draft';

        $contract = ContractGenerated::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء العقد بنجاح',
            'data' => $contract
        ], 201);
    }

    public function jea01()
    {
        $template = ContractTemplate::where('code', 'JEA-01')
            ->with(['clauses', 'specialConditions', 'variables'])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $template
        ]);
    }

    public function jea02()
    {
        $template = ContractTemplate::where('code', 'JEA-02')
            ->with(['clauses', 'specialConditions', 'variables'])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $template
        ]);
    }

    public function generateFromTemplate(Request $request)
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

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء العقد من القالب بنجاح',
            'data' => $contract
        ], 201);
    }

    public function exportWord($id)
    {
        $contract = ContractGenerated::with(['template'])->findOrFail($id);
        
        // TODO: Implement Word export
        return response()->json([
            'success' => true,
            'message' => 'Word export functionality to be implemented',
            'contract_id' => $id
        ]);
    }

    public function exportPdf($id)
    {
        $contract = ContractGenerated::with(['template'])->findOrFail($id);
        
        // TODO: Implement PDF export
        return response()->json([
            'success' => true,
            'message' => 'PDF export functionality to be implemented',
            'contract_id' => $id
        ]);
    }
}
