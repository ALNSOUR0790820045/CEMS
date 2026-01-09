<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PunchReport;
use App\Models\PunchList;
use App\Models\PunchItem;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PunchReportController extends Controller
{
    public function summary($projectId)
    {
        $project = Project::findOrFail($projectId);

        $lists = PunchList::where('project_id', $projectId)->get();
        $items = PunchItem::whereHas('punchList', function ($query) use ($projectId) {
            $query->where('project_id', $projectId);
        })->get();

        $summary = [
            'project' => $project,
            'total_lists' => $lists->count(),
            'total_items' => $items->count(),
            'open_items' => $items->where('status', 'open')->count(),
            'in_progress_items' => $items->where('status', 'in_progress')->count(),
            'completed_items' => $items->where('status', 'completed')->count(),
            'verified_items' => $items->where('status', 'verified')->count(),
            'overdue_items' => $items->filter(function ($item) {
                return $item->isOverdue();
            })->count(),
            'by_severity' => [
                'minor' => $items->where('severity', 'minor')->count(),
                'major' => $items->where('severity', 'major')->count(),
                'critical' => $items->where('severity', 'critical')->count(),
            ],
            'by_discipline' => $items->groupBy('discipline')->map->count(),
            'completion_percentage' => $items->count() > 0 
                ? ($items->whereIn('status', ['completed', 'verified'])->count() / $items->count()) * 100 
                : 0,
        ];

        return response()->json($summary);
    }

    public function detailed($projectId)
    {
        $project = Project::findOrFail($projectId);

        $lists = PunchList::with([
            'items.assignedTo',
            'items.verifiedBy',
            'contractor',
            'inspector'
        ])->where('project_id', $projectId)->get();

        return response()->json([
            'project' => $project,
            'lists' => $lists,
        ]);
    }

    public function byContractor($projectId)
    {
        $project = Project::findOrFail($projectId);

        $lists = PunchList::with(['contractor', 'items'])
            ->where('project_id', $projectId)
            ->get()
            ->groupBy('contractor_id');

        $summary = [];
        foreach ($lists as $contractorId => $contractorLists) {
            $contractor = $contractorLists->first()->contractor;
            $allItems = $contractorLists->flatMap->items;

            $summary[] = [
                'contractor' => $contractor,
                'total_lists' => $contractorLists->count(),
                'total_items' => $allItems->count(),
                'open_items' => $allItems->where('status', 'open')->count(),
                'completed_items' => $allItems->whereIn('status', ['completed', 'verified'])->count(),
            ];
        }

        return response()->json([
            'project' => $project,
            'contractors' => $summary,
        ]);
    }

    public function overdue($projectId)
    {
        $project = Project::findOrFail($projectId);

        $items = PunchItem::with(['punchList', 'assignedTo'])
            ->whereHas('punchList', function ($query) use ($projectId) {
                $query->where('project_id', $projectId);
            })
            ->where('due_date', '<', now())
            ->whereNotIn('status', ['completed', 'verified'])
            ->orderBy('due_date')
            ->get();

        return response()->json([
            'project' => $project,
            'overdue_items' => $items,
            'total_overdue' => $items->count(),
        ]);
    }

    public function statistics($projectId)
    {
        $project = Project::findOrFail($projectId);

        $items = PunchItem::whereHas('punchList', function ($query) use ($projectId) {
            $query->where('project_id', $projectId);
        })->get();

        $stats = [
            'total_items' => $items->count(),
            'status_breakdown' => [
                'open' => $items->where('status', 'open')->count(),
                'in_progress' => $items->where('status', 'in_progress')->count(),
                'completed' => $items->where('status', 'completed')->count(),
                'verified' => $items->where('status', 'verified')->count(),
                'rejected' => $items->where('status', 'rejected')->count(),
            ],
            'severity_breakdown' => [
                'minor' => $items->where('severity', 'minor')->count(),
                'major' => $items->where('severity', 'major')->count(),
                'critical' => $items->where('severity', 'critical')->count(),
            ],
            'priority_breakdown' => [
                'low' => $items->where('priority', 'low')->count(),
                'medium' => $items->where('priority', 'medium')->count(),
                'high' => $items->where('priority', 'high')->count(),
                'urgent' => $items->where('priority', 'urgent')->count(),
            ],
            'category_breakdown' => [
                'defect' => $items->where('category', 'defect')->count(),
                'incomplete' => $items->where('category', 'incomplete')->count(),
                'damage' => $items->where('category', 'damage')->count(),
                'missing' => $items->where('category', 'missing')->count(),
                'wrong' => $items->where('category', 'wrong')->count(),
            ],
            'discipline_breakdown' => $items->groupBy('discipline')->map->count(),
        ];

        return response()->json([
            'project' => $project,
            'statistics' => $stats,
        ]);
    }

    public function export(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);

        $validated = $request->validate([
            'report_type' => 'required|in:summary,detailed,location,discipline,contractor',
            'period_from' => 'nullable|date',
            'period_to' => 'nullable|date',
            'filters' => 'nullable|array',
        ]);

        // Generate report number
        $year = date('Y');
        $sequence = PunchReport::whereYear('created_at', $year)->count() + 1;
        $reportNumber = 'PLR-'.$year.'-'.str_pad($sequence, 4, '0', STR_PAD_LEFT);

        $report = PunchReport::create([
            'report_number' => $reportNumber,
            'project_id' => $projectId,
            'report_type' => $validated['report_type'],
            'report_date' => now(),
            'period_from' => $validated['period_from'] ?? null,
            'period_to' => $validated['period_to'] ?? null,
            'filters' => $validated['filters'] ?? null,
            'generated_by_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Report generated successfully',
            'data' => $report
        ], 201);
    }
}
