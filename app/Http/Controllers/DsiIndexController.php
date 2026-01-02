<?php

namespace App\Http\Controllers;

use App\Models\DsiIndex;
use App\Models\DsiImportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DsiIndexController extends Controller
{
    public function index()
    {
        $indices = DsiIndex::orderByDesc('year')->orderByDesc('month')->paginate(12);
        $importLogs = DsiImportLog::with('importedBy')->latest()->limit(5)->get();
        
        return view('price-escalation.dsi-indices', compact('indices', 'importLogs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'materials_index' => 'required|numeric|min:0',
            'labor_index' => 'required|numeric|min:0',
            'general_index' => 'nullable|numeric|min:0',
            'source' => 'nullable|string|max:255',
            'reference_url' => 'nullable|url',
        ]);
        
        // Create index_date from year and month
        $validated['index_date'] = \Carbon\Carbon::createFromDate($validated['year'], $validated['month'], 1);
        
        $index = DsiIndex::create($validated);
        
        // Calculate change percent
        $index->calculateChangePercent();
        
        return redirect()->route('price-escalation.dsi-indices')
            ->with('success', 'تم إضافة المؤشر بنجاح');
    }

    public function update(Request $request, DsiIndex $index)
    {
        $validated = $request->validate([
            'materials_index' => 'required|numeric|min:0',
            'labor_index' => 'required|numeric|min:0',
            'general_index' => 'nullable|numeric|min:0',
            'source' => 'nullable|string|max:255',
            'reference_url' => 'nullable|url',
        ]);
        
        $index->update($validated);
        
        // Recalculate change percent
        $index->calculateChangePercent();
        
        return redirect()->route('price-escalation.dsi-indices')
            ->with('success', 'تم تحديث المؤشر بنجاح');
    }

    public function destroy(DsiIndex $index)
    {
        $index->delete();
        return redirect()->route('price-escalation.dsi-indices')
            ->with('success', 'تم حذف المؤشر بنجاح');
    }

    public function importForm()
    {
        return view('price-escalation.import-dsi');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
        ]);
        
        try {
            $file = $request->file('file');
            $path = $file->store('dsi-imports');
            
            // Parse file
            $data = $this->parseImportFile($file);
            
            $imported = 0;
            foreach ($data as $row) {
                $indexDate = \Carbon\Carbon::createFromDate($row['year'], $row['month'], 1);
                
                DsiIndex::updateOrCreate(
                    ['year' => $row['year'], 'month' => $row['month']],
                    [
                        'index_date' => $indexDate,
                        'materials_index' => $row['materials'],
                        'labor_index' => $row['labor'],
                        'general_index' => $row['general'] ?? null,
                        'source' => $row['source'] ?? 'DOS Jordan',
                    ]
                );
                
                $imported++;
            }
            
            // Calculate change percentages for all imported
            foreach ($data as $row) {
                $index = DsiIndex::where('year', $row['year'])
                    ->where('month', $row['month'])
                    ->first();
                if ($index) {
                    $index->calculateChangePercent();
                }
            }
            
            // Log import
            DsiImportLog::create([
                'import_date' => now(),
                'records_imported' => $imported,
                'file_path' => $path,
                'imported_by' => Auth::id(),
                'notes' => 'Imported ' . $imported . ' records',
            ]);
            
            return redirect()->route('price-escalation.dsi-indices')
                ->with('success', "تم استيراد {$imported} مؤشر بنجاح");
                
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'خطأ في معالجة الملف: ' . $e->getMessage()])->withInput();
        }
    }

    private function parseImportFile($file): array
    {
        $extension = $file->getClientOriginalExtension();
        $data = [];
        
        if ($extension === 'csv') {
            $handle = fopen($file->getRealPath(), 'r');
            $header = fgetcsv($handle); // Skip header
            
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 4) {
                    $data[] = [
                        'year' => (int) $row[0],
                        'month' => (int) $row[1],
                        'materials' => (float) $row[2],
                        'labor' => (float) $row[3],
                        'general' => isset($row[4]) ? (float) $row[4] : null,
                        'source' => isset($row[5]) ? $row[5] : 'DOS Jordan',
                    ];
                }
            }
            
            fclose($handle);
        } else {
            // For Excel files, you would use a library like PhpSpreadsheet
            // For simplicity, throwing an exception here
            throw new \Exception('Excel import not yet implemented. Please use CSV format.');
        }
        
        return $data;
    }

    public function getTrend(Request $request)
    {
        $months = $request->input('months', 12);
        
        $trend = DsiIndex::orderByDesc('year')
            ->orderByDesc('month')
            ->limit($months)
            ->get()
            ->reverse()
            ->values();
        
        return response()->json($trend);
    }
}
