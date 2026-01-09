<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhotoAlbum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhotoAlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PhotoAlbum::with(['project', 'coverPhoto', 'createdBy']);

        // Filter by project
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by album type
        if ($request->has('album_type')) {
            $query->where('album_type', $request->album_type);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('album_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        $albums = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($albums);
    }

    /**
     * Get albums by project
     */
    public function byProject($projectId)
    {
        $albums = PhotoAlbum::with(['coverPhoto'])
            ->where('project_id', $projectId)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($albums);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'album_type' => 'required|in:progress,quality,safety,milestone,handover,general',
            'status' => 'nullable|in:active,archived',
        ]);

        $validated['created_by_id'] = Auth::id();
        $validated['company_id'] = Auth::user()->company_id;

        $album = PhotoAlbum::create($validated);

        return response()->json($album->load(['project', 'createdBy']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $album = PhotoAlbum::with(['project', 'coverPhoto', 'photos', 'createdBy'])
            ->findOrFail($id);

        return response()->json($album);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $album = PhotoAlbum::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'album_type' => 'sometimes|in:progress,quality,safety,milestone,handover,general',
            'status' => 'sometimes|in:active,archived',
        ]);

        $album->update($validated);

        return response()->json($album->load(['project', 'coverPhoto']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $album = PhotoAlbum::findOrFail($id);
        $album->delete();

        return response()->json(['message' => 'Album deleted successfully'], 200);
    }

    /**
     * Set cover photo for album
     */
    public function setCover(Request $request, $id)
    {
        $album = PhotoAlbum::findOrFail($id);

        $validated = $request->validate([
            'photo_id' => 'required|exists:photos,id',
        ]);

        $album->update(['cover_photo_id' => $validated['photo_id']]);

        return response()->json($album->load(['coverPhoto']));
    }
}
