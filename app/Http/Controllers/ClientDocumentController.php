<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientDocument;
use App\Http\Requests\StoreClientDocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ClientDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Client $client)
    {
        $documents = $client->documents()->with('uploadedBy')->latest()->get();
        return response()->json($documents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientDocumentRequest $request, Client $client)
    {
        $data = $request->validated();
        
        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('client_documents/' . $client->id, 'public');
            
            $data['file_path'] = $path;
            $data['file_size'] = $file->getSize();
            $data['mime_type'] = $file->getMimeType();
        }
        
        $data['client_id'] = $client->id;
        $data['uploaded_by_id'] = Auth::id();
        
        unset($data['file']);
        
        $document = ClientDocument::create($data);

        return redirect()->route('clients.show', $client)
            ->with('success', 'تم رفع المستند بنجاح');
    }

    /**
     * Download the specified resource.
     */
    public function download(Client $client, ClientDocument $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->document_name
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client, ClientDocument $document)
    {
        $document->delete();

        return redirect()->route('clients.show', $client)
            ->with('success', 'تم حذف المستند بنجاح');
    }
}
