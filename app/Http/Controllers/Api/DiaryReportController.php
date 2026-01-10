<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteDiary;
use App\Models\DiaryManpower;
use App\Models\DiaryEquipment;
use App\Models\DiaryIncident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiaryReportController extends Controller
{
    public function dailySummary(Request $request, $projectId)
    {
        $date = $request->input('date', now()->format('Y-m-d'));

        $diary = SiteDiary::with([
            'manpower.subcontractor',
            'equipment',
            'activities.unit',
            'materials.material',
            'visitors',
            'incidents',
            'instructions'
        ])
        ->where('project_id', $projectId)
        ->whereDate('diary_date', $date)
        ->first();

        if (!$diary) {
            return response()->json([
                'message' => 'No diary found for this date'
            ], 404);
        }

        $summary = [
            'diary' => $diary,
            'totals' => [
                'manpower' => [
                    'own' => $diary->manpower->sum('own_count'),
                    'subcontractor' => $diary->manpower->sum('subcontractor_count'),
                    'total' => $diary->manpower->sum(function ($m) {
                        return $m->own_count + $m->subcontractor_count;
                    }),
                    'total_hours' => $diary->manpower->sum('hours_worked'),
                    'overtime_hours' => $diary->manpower->sum('overtime_hours'),
                ],
                'equipment' => [
                    'count' => $diary->equipment->count(),
                    'total_hours_worked' => $diary->equipment->sum('hours_worked'),
                    'total_hours_idle' => $diary->equipment->sum('hours_idle'),
                    'total_fuel' => $diary->equipment->sum('fuel_consumed'),
                ],
                'activities' => [
                    'count' => $diary->activities->count(),
                    'completed' => $diary->activities->where('status', 'completed')->count(),
                    'in_progress' => $diary->activities->where('status', 'in_progress')->count(),
                    'delayed' => $diary->activities->where('status', 'delayed')->count(),
                ],
                'materials' => [
                    'items_received' => $diary->materials->where('quantity_received', '>', 0)->count(),
                    'items_used' => $diary->materials->where('quantity_used', '>', 0)->count(),
                ],
                'visitors' => $diary->visitors->count(),
                'incidents' => [
                    'total' => $diary->incidents->count(),
                    'by_type' => $diary->incidents->groupBy('incident_type')->map->count(),
                    'by_severity' => $diary->incidents->groupBy('severity')->map->count(),
                ],
                'instructions' => [
                    'total' => $diary->instructions->count(),
                    'pending' => $diary->instructions->where('status', 'pending')->count(),
                    'completed' => $diary->instructions->where('status', 'completed')->count(),
                ],
            ]
        ];

        return response()->json($summary);
    }

    public function weeklySummary(Request $request, $projectId)
    {
        $startDate = $request->input('start_date', now()->startOfWeek()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfWeek()->format('Y-m-d'));

        $diaries = SiteDiary::with(['manpower', 'equipment', 'activities', 'incidents'])
            ->where('project_id', $projectId)
            ->whereBetween('diary_date', [$startDate, $endDate])
            ->orderBy('diary_date')
            ->get();

        $summary = [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_days' => $diaries->count(),
            ],
            'manpower' => [
                'daily_average_own' => round($diaries->avg(function ($d) {
                    return $d->manpower->sum('own_count');
                }), 2),
                'daily_average_subcontractor' => round($diaries->avg(function ($d) {
                    return $d->manpower->sum('subcontractor_count');
                }), 2),
                'total_man_hours' => $diaries->sum(function ($d) {
                    return $d->manpower->sum('hours_worked');
                }),
                'total_overtime_hours' => $diaries->sum(function ($d) {
                    return $d->manpower->sum('overtime_hours');
                }),
                'by_trade' => $this->getManpowerByTrade($projectId, $startDate, $endDate),
            ],
            'equipment' => [
                'total_hours_worked' => $diaries->sum(function ($d) {
                    return $d->equipment->sum('hours_worked');
                }),
                'total_hours_idle' => $diaries->sum(function ($d) {
                    return $d->equipment->sum('hours_idle');
                }),
                'average_utilization' => $this->calculateEquipmentUtilization($diaries),
            ],
            'activities' => [
                'total_activities' => $diaries->sum(function ($d) {
                    return $d->activities->count();
                }),
                'completed' => $diaries->sum(function ($d) {
                    return $d->activities->where('status', 'completed')->count();
                }),
                'delayed' => $diaries->sum(function ($d) {
                    return $d->activities->where('status', 'delayed')->count();
                }),
            ],
            'weather' => [
                'by_morning' => $diaries->groupBy('weather_morning')->map->count(),
                'by_afternoon' => $diaries->groupBy('weather_afternoon')->map->count(),
                'avg_temperature_min' => round($diaries->avg('temperature_min'), 2),
                'avg_temperature_max' => round($diaries->avg('temperature_max'), 2),
            ],
            'work_status' => $diaries->groupBy('work_status')->map->count(),
            'incidents' => [
                'total' => $diaries->sum(function ($d) {
                    return $d->incidents->count();
                }),
                'by_type' => $this->getIncidentsByType($projectId, $startDate, $endDate),
            ],
        ];

        return response()->json($summary);
    }

    public function monthlySummary(Request $request, $projectId)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        
        $startDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $diaries = SiteDiary::with(['manpower', 'equipment', 'activities', 'incidents'])
            ->where('project_id', $projectId)
            ->whereBetween('diary_date', [$startDate, $endDate])
            ->orderBy('diary_date')
            ->get();

        $summary = [
            'period' => [
                'year' => $year,
                'month' => $month,
                'month_name' => $startDate->format('F'),
                'total_days_recorded' => $diaries->count(),
                'working_days' => $diaries->where('work_status', 'normal')->count(),
                'delayed_days' => $diaries->where('work_status', 'delayed')->count(),
                'suspended_days' => $diaries->where('work_status', 'suspended')->count(),
                'holidays' => $diaries->where('work_status', 'holiday')->count(),
            ],
            'manpower' => [
                'total_man_days_own' => $diaries->sum(function ($d) {
                    return $d->manpower->sum('own_count');
                }),
                'total_man_days_subcontractor' => $diaries->sum(function ($d) {
                    return $d->manpower->sum('subcontractor_count');
                }),
                'daily_average' => round($diaries->avg(function ($d) {
                    return $d->manpower->sum(fn($m) => $m->own_count + $m->subcontractor_count);
                }), 2),
                'by_trade' => $this->getManpowerByTrade($projectId, $startDate, $endDate),
            ],
            'equipment' => [
                'total_hours_worked' => $diaries->sum(function ($d) {
                    return $d->equipment->sum('hours_worked');
                }),
                'total_fuel_consumed' => $diaries->sum(function ($d) {
                    return $d->equipment->sum('fuel_consumed');
                }),
            ],
            'incidents' => [
                'total' => $diaries->sum(function ($d) {
                    return $d->incidents->count();
                }),
                'accidents' => $this->countIncidentsByType($diaries, 'accident'),
                'near_misses' => $this->countIncidentsByType($diaries, 'near_miss'),
                'by_severity' => $this->getIncidentsBySeverity($projectId, $startDate, $endDate),
            ],
            'weather_summary' => [
                'sunny_days' => $diaries->where('weather_morning', 'sunny')->count(),
                'rainy_days' => $diaries->whereIn('weather_morning', ['rainy', 'stormy'])->count(),
                'avg_temperature' => round(($diaries->avg('temperature_min') + $diaries->avg('temperature_max')) / 2, 2),
            ],
        ];

        return response()->json($summary);
    }

    public function manpowerHistogram(Request $request, $projectId)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $data = SiteDiary::select('diary_date')
            ->with('manpower')
            ->where('project_id', $projectId)
            ->whereBetween('diary_date', [$startDate, $endDate])
            ->orderBy('diary_date')
            ->get()
            ->map(function ($diary) {
                return [
                    'date' => $diary->diary_date->format('Y-m-d'),
                    'own' => $diary->manpower->sum('own_count'),
                    'subcontractor' => $diary->manpower->sum('subcontractor_count'),
                    'total' => $diary->manpower->sum(fn($m) => $m->own_count + $m->subcontractor_count),
                ];
            });

        return response()->json($data);
    }

    public function equipmentUtilization(Request $request, $projectId)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $data = DB::table('diary_equipment')
            ->join('site_diaries', 'diary_equipment.site_diary_id', '=', 'site_diaries.id')
            ->select(
                'diary_equipment.equipment_type',
                DB::raw('SUM(diary_equipment.hours_worked) as total_hours_worked'),
                DB::raw('SUM(diary_equipment.hours_idle) as total_hours_idle'),
                DB::raw('SUM(diary_equipment.hours_worked + diary_equipment.hours_idle) as total_hours'),
                DB::raw('ROUND((SUM(diary_equipment.hours_worked) / NULLIF(SUM(diary_equipment.hours_worked + diary_equipment.hours_idle), 0)) * 100, 2) as utilization_rate')
            )
            ->where('site_diaries.project_id', $projectId)
            ->whereBetween('site_diaries.diary_date', [$startDate, $endDate])
            ->groupBy('diary_equipment.equipment_type')
            ->get();

        return response()->json($data);
    }

    public function weatherAnalysis(Request $request, $projectId)
    {
        $startDate = $request->input('start_date', now()->subDays(90)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $diaries = SiteDiary::where('project_id', $projectId)
            ->whereBetween('diary_date', [$startDate, $endDate])
            ->get();

        $analysis = [
            'total_days' => $diaries->count(),
            'weather_distribution' => [
                'morning' => $diaries->groupBy('weather_morning')->map->count(),
                'afternoon' => $diaries->groupBy('weather_afternoon')->map->count(),
            ],
            'temperature' => [
                'min' => $diaries->min('temperature_min'),
                'max' => $diaries->max('temperature_max'),
                'avg_min' => round($diaries->avg('temperature_min'), 2),
                'avg_max' => round($diaries->avg('temperature_max'), 2),
            ],
            'site_conditions' => $diaries->groupBy('site_condition')->map->count(),
            'impact_on_work' => [
                'delayed_due_to_weather' => $diaries->where('work_status', 'delayed')
                    ->filter(function ($d) {
                        return str_contains(strtolower($d->delay_reason ?? ''), 'weather') ||
                               str_contains(strtolower($d->delay_reason ?? ''), 'rain') ||
                               str_contains(strtolower($d->delay_reason ?? ''), 'storm');
                    })->count(),
            ],
        ];

        return response()->json($analysis);
    }

    public function incidentLog(Request $request, $projectId)
    {
        $startDate = $request->input('start_date', now()->subDays(90)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $incidents = DB::table('diary_incidents')
            ->join('site_diaries', 'diary_incidents.site_diary_id', '=', 'site_diaries.id')
            ->select('diary_incidents.*', 'site_diaries.diary_date', 'site_diaries.diary_number')
            ->where('site_diaries.project_id', $projectId)
            ->whereBetween('site_diaries.diary_date', [$startDate, $endDate])
            ->orderBy('site_diaries.diary_date', 'desc')
            ->get();

        return response()->json($incidents);
    }

    public function progressPhotos(Request $request, $projectId)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $category = $request->input('category', null);

        $query = DB::table('diary_photos')
            ->join('site_diaries', 'diary_photos.site_diary_id', '=', 'site_diaries.id')
            ->join('users', 'diary_photos.taken_by_id', '=', 'users.id')
            ->select(
                'diary_photos.*',
                'site_diaries.diary_date',
                'site_diaries.diary_number',
                'users.name as taken_by_name'
            )
            ->where('site_diaries.project_id', $projectId)
            ->whereBetween('site_diaries.diary_date', [$startDate, $endDate]);

        if ($category) {
            $query->where('diary_photos.category', $category);
        }

        $photos = $query->orderBy('site_diaries.diary_date', 'desc')->get();

        return response()->json($photos);
    }

    // Helper methods
    private function getManpowerByTrade($projectId, $startDate, $endDate)
    {
        return DB::table('diary_manpower')
            ->join('site_diaries', 'diary_manpower.site_diary_id', '=', 'site_diaries.id')
            ->select(
                'diary_manpower.trade',
                DB::raw('SUM(diary_manpower.own_count) as total_own'),
                DB::raw('SUM(diary_manpower.subcontractor_count) as total_subcontractor'),
                DB::raw('SUM(diary_manpower.own_count + diary_manpower.subcontractor_count) as total')
            )
            ->where('site_diaries.project_id', $projectId)
            ->whereBetween('site_diaries.diary_date', [$startDate, $endDate])
            ->groupBy('diary_manpower.trade')
            ->get();
    }

    private function getIncidentsByType($projectId, $startDate, $endDate)
    {
        return DB::table('diary_incidents')
            ->join('site_diaries', 'diary_incidents.site_diary_id', '=', 'site_diaries.id')
            ->select('diary_incidents.incident_type', DB::raw('COUNT(*) as count'))
            ->where('site_diaries.project_id', $projectId)
            ->whereBetween('site_diaries.diary_date', [$startDate, $endDate])
            ->groupBy('diary_incidents.incident_type')
            ->pluck('count', 'incident_type');
    }

    private function getIncidentsBySeverity($projectId, $startDate, $endDate)
    {
        return DB::table('diary_incidents')
            ->join('site_diaries', 'diary_incidents.site_diary_id', '=', 'site_diaries.id')
            ->select('diary_incidents.severity', DB::raw('COUNT(*) as count'))
            ->where('site_diaries.project_id', $projectId)
            ->whereBetween('site_diaries.diary_date', [$startDate, $endDate])
            ->groupBy('diary_incidents.severity')
            ->pluck('count', 'severity');
    }

    private function calculateEquipmentUtilization($diaries)
    {
        $totalWorked = $diaries->sum(function ($d) {
            return $d->equipment->sum('hours_worked');
        });
        
        $totalHours = $diaries->sum(function ($d) {
            return $d->equipment->sum(fn($e) => $e->hours_worked + $e->hours_idle);
        });

        return $totalHours > 0 ? round(($totalWorked / $totalHours) * 100, 2) : 0;
    }

    private function countIncidentsByType($diaries, $type)
    {
        return $diaries->sum(function ($d) use ($type) {
            return $d->incidents->where('incident_type', $type)->count();
        });
    }
}
