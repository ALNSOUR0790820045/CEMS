<?php

namespace App\Http\Controllers\Api;

use App\Models\PunchList;
use App\Models\PunchItem;
use App\Models\Project;
use Illuminate\Http\Request;

class PunchDashboardController extends Controller
{
    public function projectDashboard($projectId)
    {
        $project = Project::findOrFail($projectId);

        $lists = PunchList::where('project_id', $projectId)->get();
        $items = PunchItem::whereHas('punchList', function ($query) use ($projectId) {
            $query->where('project_id', $projectId);
        })->get();

        $dashboard = [
            'project' => $project,
            'overview' => [
                'total_lists' => $lists->count(),
                'active_lists' => $lists->whereIn('status', ['issued', 'in_progress'])->count(),
                'completed_lists' => $lists->where('status', 'completed')->count(),
                'total_items' => $items->count(),
                'open_items' => $items->where('status', 'open')->count(),
                'in_progress_items' => $items->where('status', 'in_progress')->count(),
                'completed_items' => $items->where('status', 'completed')->count(),
                'verified_items' => $items->where('status', 'verified')->count(),
                'overdue_items' => $items->filter(fn($item) => $item->isOverdue())->count(),
            ],
            'completion_rate' => $items->count() > 0 
                ? round(($items->whereIn('status', ['completed', 'verified'])->count() / $items->count()) * 100, 2)
                : 0,
            'recent_items' => $items->sortByDesc('created_at')->take(10)->values(),
        ];

        return response()->json($dashboard);
    }

    public function summary($projectId)
    {
        $project = Project::findOrFail($projectId);

        $items = PunchItem::whereHas('punchList', function ($query) use ($projectId) {
            $query->where('project_id', $projectId);
        })->get();

        return response()->json([
            'total_items' => $items->count(),
            'by_status' => [
                'open' => $items->where('status', 'open')->count(),
                'in_progress' => $items->where('status', 'in_progress')->count(),
                'completed' => $items->where('status', 'completed')->count(),
                'verified' => $items->where('status', 'verified')->count(),
            ],
            'by_severity' => [
                'minor' => $items->where('severity', 'minor')->count(),
                'major' => $items->where('severity', 'major')->count(),
                'critical' => $items->where('severity', 'critical')->count(),
            ],
        ]);
    }

    public function byDiscipline($projectId)
    {
        $items = PunchItem::whereHas('punchList', function ($query) use ($projectId) {
            $query->where('project_id', $projectId);
        })->get();

        $byDiscipline = $items->groupBy('discipline')->map(function ($disciplineItems) {
            return [
                'total' => $disciplineItems->count(),
                'open' => $disciplineItems->where('status', 'open')->count(),
                'completed' => $disciplineItems->whereIn('status', ['completed', 'verified'])->count(),
            ];
        });

        return response()->json($byDiscipline);
    }

    public function byContractor($projectId)
    {
        $lists = PunchList::with(['items', 'contractor'])
            ->where('project_id', $projectId)
            ->get()
            ->groupBy('contractor_id');

        $byContractor = [];
        foreach ($lists as $contractorId => $contractorLists) {
            if (!$contractorId) continue;

            $contractor = $contractorLists->first()->contractor;
            $allItems = $contractorLists->flatMap->items;

            $byContractor[$contractor->name ?? 'Unknown'] = [
                'total_items' => $allItems->count(),
                'open' => $allItems->where('status', 'open')->count(),
                'completed' => $allItems->whereIn('status', ['completed', 'verified'])->count(),
            ];
        }

        return response()->json($byContractor);
    }

    public function byLocation($projectId)
    {
        $items = PunchItem::whereHas('punchList', function ($query) use ($projectId) {
            $query->where('project_id', $projectId);
        })->get();

        $byLocation = $items->groupBy('location')->map(function ($locationItems) {
            return [
                'total' => $locationItems->count(),
                'open' => $locationItems->where('status', 'open')->count(),
                'completed' => $locationItems->whereIn('status', ['completed', 'verified'])->count(),
            ];
        });

        return response()->json($byLocation);
    }

    public function aging($projectId)
    {
        $items = PunchItem::whereHas('punchList', function ($query) use ($projectId) {
            $query->where('project_id', $projectId);
        })->whereNotIn('status', ['completed', 'verified'])->get();

        $aging = [
            '0-7_days' => 0,
            '8-14_days' => 0,
            '15-30_days' => 0,
            'over_30_days' => 0,
        ];

        foreach ($items as $item) {
            $age = now()->diffInDays($item->created_at);
            
            if ($age <= 7) {
                $aging['0-7_days']++;
            } elseif ($age <= 14) {
                $aging['8-14_days']++;
            } elseif ($age <= 30) {
                $aging['15-30_days']++;
            } else {
                $aging['over_30_days']++;
            }
        }

        return response()->json($aging);
    }

    public function trend(Request $request, $projectId)
    {
        $days = $request->input('days', 30);

        $items = PunchItem::whereHas('punchList', function ($query) use ($projectId) {
            $query->where('project_id', $projectId);
        })->where('created_at', '>=', now()->subDays($days))->get();

        $trend = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayItems = $items->filter(fn($item) => $item->created_at->format('Y-m-d') === $date);

            $trend[] = [
                'date' => $date,
                'created' => $dayItems->count(),
                'completed' => $dayItems->whereIn('status', ['completed', 'verified'])->count(),
            ];
        }

        return response()->json($trend);
    }
}
