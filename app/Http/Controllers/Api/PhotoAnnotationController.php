<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhotoAnnotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhotoAnnotationController extends Controller
{
    /**
     * Get annotations by photo
     */
    public function byPhoto($photoId)
    {
        $annotations = PhotoAnnotation::with(['createdBy'])
            ->where('photo_id', $photoId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($annotations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $photoId)
    {
        $validated = $request->validate([
            'annotation_type' => 'required|in:arrow,circle,rectangle,text,marker',
            'coordinates' => 'required|array',
            'color' => 'nullable|string',
            'text' => 'nullable|string',
        ]);

        $validated['photo_id'] = $photoId;
        $validated['created_by_id'] = Auth::id();
        $validated['color'] = $validated['color'] ?? '#FF0000';

        $annotation = PhotoAnnotation::create($validated);

        return response()->json($annotation->load('createdBy'), 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $annotation = PhotoAnnotation::findOrFail($id);

        $validated = $request->validate([
            'annotation_type' => 'sometimes|in:arrow,circle,rectangle,text,marker',
            'coordinates' => 'sometimes|array',
            'color' => 'nullable|string',
            'text' => 'nullable|string',
        ]);

        $annotation->update($validated);

        return response()->json($annotation);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $annotation = PhotoAnnotation::findOrFail($id);
        $annotation->delete();

        return response()->json(['message' => 'Annotation deleted successfully'], 200);
    }
}
