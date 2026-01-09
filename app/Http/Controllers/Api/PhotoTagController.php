<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhotoTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhotoTagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = PhotoTag::where('company_id', Auth::user()->company_id)
            ->orderBy('usage_count', 'desc')
            ->get();

        return response()->json($tags);
    }

    /**
     * Get popular tags
     */
    public function popular()
    {
        $tags = PhotoTag::where('company_id', Auth::user()->company_id)
            ->orderBy('usage_count', 'desc')
            ->limit(20)
            ->get();

        return response()->json($tags);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'color' => 'nullable|string',
        ]);

        $validated['company_id'] = Auth::user()->company_id;
        $validated['color'] = $validated['color'] ?? '#000000';

        $tag = PhotoTag::create($validated);

        return response()->json($tag, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tag = PhotoTag::findOrFail($id);

        return response()->json($tag);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tag = PhotoTag::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'color' => 'nullable|string',
        ]);

        $tag->update($validated);

        return response()->json($tag);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tag = PhotoTag::findOrFail($id);
        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully'], 200);
    }
}
