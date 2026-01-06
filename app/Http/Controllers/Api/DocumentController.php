<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\DocumentAccess;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Document::with(['uploadedBy', 'approvedBy', 'company'])
            ->where('company_id', auth()->user()->company_id);

        // Apply filters
        if ($request->has('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('is_confidential')) {
            $query->where('is_confidential', $request->boolean('is_confidential'));
        }

        $documents = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($documents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $file = $request->file('file');
            $path = $file->store('documents', 'public');

            $document = Document::create([
                'document_name' => $request->document_name,
                'document_type' => $request->document_type,
                'category' => $request->category,
                'related_entity_type' => $request->related_entity_type,
                'related_entity_id' => $request->related_entity_id,
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientOriginalExtension(),
                'description' => $request->description,
                'tags' => $request->tags,
                'is_confidential' => $request->boolean('is_confidential', false),
                'status' => $request->status ?? 'draft',
                'expiry_date' => $request->expiry_date,
                'uploaded_by_id' => auth()->id(),
                'company_id' => auth()->user()->company_id,
            ]);

            // Create initial version
            DocumentVersion::create([
                'document_id' => $document->id,
                'version' => '1.0',
                'file_path' => $path,
                'change_description' => 'Initial upload',
                'uploaded_by_id' => auth()->id(),
                'uploaded_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Document created successfully',
                'document' => $document->load(['uploadedBy', 'company']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create document',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document): JsonResponse
    {
        $this->authorize('view', $document);

        $document->load(['uploadedBy', 'approvedBy', 'company', 'versions', 'accessRights']);

        return response()->json($document);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentRequest $request, Document $document): JsonResponse
    {
        $this->authorize('update', $document);

        $document->update($request->validated());

        return response()->json([
            'message' => 'Document updated successfully',
            'document' => $document->load(['uploadedBy', 'approvedBy', 'company']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document): JsonResponse
    {
        $this->authorize('delete', $document);

        $document->delete();

        return response()->json([
            'message' => 'Document deleted successfully',
        ]);
    }

    /**
     * Upload a new version of the document.
     */
    public function uploadVersion(Request $request, Document $document): JsonResponse
    {
        $this->authorize('update', $document);

        $request->validate([
            'file' => 'required|file|max:51200',
            'version' => 'required|string|max:50',
            'change_description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('file');
            $path = $file->store('documents', 'public');

            // Update document
            $document->update([
                'version' => $request->version,
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientOriginalExtension(),
            ]);

            // Create version record
            DocumentVersion::create([
                'document_id' => $document->id,
                'version' => $request->version,
                'file_path' => $path,
                'change_description' => $request->change_description,
                'uploaded_by_id' => auth()->id(),
                'uploaded_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'New version uploaded successfully',
                'document' => $document->load(['versions']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to upload new version',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all versions of the document.
     */
    public function versions(Document $document): JsonResponse
    {
        $this->authorize('view', $document);

        $versions = $document->versions()
            ->with('uploadedBy')
            ->orderBy('uploaded_at', 'desc')
            ->get();

        return response()->json($versions);
    }

    /**
     * Grant access to a user or role.
     */
    public function grantAccess(Request $request, Document $document): JsonResponse
    {
        $this->authorize('update', $document);

        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'role_id' => 'nullable|exists:roles,id',
            'access_level' => 'required|in:view,download,edit,delete',
        ]);

        if (!$request->user_id && !$request->role_id) {
            return response()->json([
                'message' => 'Either user_id or role_id must be provided',
            ], 422);
        }

        $access = DocumentAccess::create([
            'document_id' => $document->id,
            'user_id' => $request->user_id,
            'role_id' => $request->role_id,
            'access_level' => $request->access_level,
            'granted_by_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Access granted successfully',
            'access' => $access->load(['user', 'role', 'grantedBy']),
        ], 201);
    }

    /**
     * Search documents.
     */
    public function search(Request $request): JsonResponse
    {
        $query = Document::with(['uploadedBy', 'approvedBy', 'company'])
            ->where('company_id', auth()->user()->company_id);

        // Text search
        if ($request->has('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('document_name', 'like', "%{$searchTerm}%")
                    ->orWhere('document_number', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhere('category', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by document type
        if ($request->has('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by tags
        if ($request->has('tags')) {
            $tags = is_array($request->tags) ? $request->tags : [$request->tags];
            $query->where(function ($q) use ($tags) {
                foreach ($tags as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            });
        }

        // Filter by related entity
        if ($request->has('related_entity_type')) {
            $query->where('related_entity_type', $request->related_entity_type);
            if ($request->has('related_entity_id')) {
                $query->where('related_entity_id', $request->related_entity_id);
            }
        }

        $documents = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($documents);
    }
}
