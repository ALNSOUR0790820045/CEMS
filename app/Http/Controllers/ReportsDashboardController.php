<?php

namespace App\Http\Controllers;

use App\Models\ReportHistory;
use Illuminate\Http\Request;

class ReportsDashboardController extends Controller
{
    public function index()
    {
        $recentReports = ReportHistory::with(['generatedBy', 'company'])
            ->orderBy('generated_at', 'desc')
            ->limit(10)
            ->get();

        return view('reports.dashboard', [
            'recentReports' => $recentReports,
        ]);
    }

    public function history()
    {
        $reports = ReportHistory::with(['generatedBy', 'company'])
            ->orderBy('generated_at', 'desc')
            ->paginate(20);

        return view('reports.history', [
            'reports' => $reports,
        ]);
    }
}
