<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeDocumentController extends BaseApiController
{
    public function index(Employee $employee)
    {
        $documents = $employee->documents;
        return response()->json($documents);
    }

    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'document_type' => 'required|in:national_id,passport,contract,certificate,resume,visa,work_permit,insurance,other',
            'document_name' => 'required|string|max:255',
            'file' => 'required|file|max:10240',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $filePath = $file->store('employees/documents', 'public');

        $document = $employee->documents()->create([
            'document_type' => $validated['document_type'],
            'document_name' => $validated['document_name'],
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'issue_date' => $validated['issue_date'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'uploaded_by_id' => Auth::id(),
            'company_id' => $this->getCompanyId(),
        ]);

        return response()->json($document, 201);
    }

    public function show(Employee $employee, EmployeeDocument $document)
    {
        return response()->json($document);
    }

    public function update(Request $request, Employee $employee, EmployeeDocument $document)
    {
        $validated = $request->validate([
            'document_type' => 'required|in:national_id,passport,contract,certificate,resume,visa,work_permit,insurance,other',
            'document_name' => 'required|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $document->update($validated);

        return response()->json($document);
    }

    public function destroy(Employee $employee, EmployeeDocument $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return response()->json(null, 204);
    }

    public function download(Employee $employee, EmployeeDocument $document)
    {
        return Storage::disk('public')->download($document->file_path, $document->document_name);
    }
}
