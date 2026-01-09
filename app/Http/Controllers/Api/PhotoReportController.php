<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhotoReport;
use App\Models\PhotoReportItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhotoReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PhotoReport::with(['project', 'createdBy']);

        // Filter by project
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by report type
        if ($request->has('report_type')) {
            $query->where('report_type', $request->report_type);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($reports);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'report_type' => 'required|in:weekly,monthly,milestone,handover',
            'period_from' => 'nullable|date',
            'period_to' => 'nullable|date|after_or_equal:period_from',
            'cover_page_text' => 'nullable|string',
        ]);

        $validated['created_by_id'] = Auth::id();
        $validated['company_id'] = Auth::user()->company_id;
        $validated['status'] = 'draft';

        $report = PhotoReport::create($validated);

        return response()->json($report->load(['project', 'createdBy']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $report = PhotoReport::with(['project', 'items.photo', 'createdBy'])
            ->findOrFail($id);

        return response()->json($report);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $report = PhotoReport::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'report_type' => 'sometimes|in:weekly,monthly,milestone,handover',
            'period_from' => 'nullable|date',
            'period_to' => 'nullable|date|after_or_equal:period_from',
            'cover_page_text' => 'nullable|string',
            'status' => 'sometimes|in:draft,published',
        ]);

        $report->update($validated);

        return response()->json($report);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $report = PhotoReport::findOrFail($id);
        $report->delete();

        return response()->json(['message' => 'Report deleted successfully'], 200);
    }

    /**
     * Add photos to report
     */
    public function addPhotos(Request $request, $id)
    {
        $report = PhotoReport::findOrFail($id);

        $validated = $request->validate([
            'photos' => 'required|array',
            'photos.*.photo_id' => 'required|exists:photos,id',
            'photos.*.caption' => 'nullable|string',
            'photos.*.description' => 'nullable|string',
            'photos.*.sort_order' => 'nullable|integer',
        ]);

        foreach ($validated['photos'] as $photoData) {
            PhotoReportItem::create([
                'photo_report_id' => $id,
                'photo_id' => $photoData['photo_id'],
                'caption' => $photoData['caption'] ?? null,
                'description' => $photoData['description'] ?? null,
                'sort_order' => $photoData['sort_order'] ?? 0,
            ]);
        }

        return response()->json($report->load('items.photo'));
    }

    /**
     * Generate PDF report
     */
    public function generatePdf($id)
    {
        $report = PhotoReport::with(['project', 'items.photo'])->findOrFail($id);

        // This would typically use a PDF library like DomPDF
        // For now, returning a simple response
        return response()->json([
            'message' => 'PDF generation would be implemented here',
            'report' => $report,
        ]);
    }

    /**
     * Publish report
     */
    public function publish($id)
    {
        $report = PhotoReport::findOrFail($id);
        
        $report->update([
            'status' => 'published',
        ]);

        return response()->json($report);
    }
}
