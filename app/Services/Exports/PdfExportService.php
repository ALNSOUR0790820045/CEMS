<?php

namespace App\Services\Exports;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfExportService
{
    public function export(array $data, string $reportType, string $filename = null): string
    {
        $filename = $filename ?? $this->generateFilename($reportType);
        
        $pdf = Pdf::loadView('reports.pdf.' . $reportType, $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        $path = 'reports/' . $filename;
        Storage::put($path, $pdf->output());

        return $path;
    }

    public function download(array $data, string $reportType, string $filename = null)
    {
        $filename = $filename ?? $this->generateFilename($reportType);
        
        $pdf = Pdf::loadView('reports.pdf.' . $reportType, $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->download($filename);
    }

    private function generateFilename(string $reportType): string
    {
        return sprintf('%s_%s.pdf', $reportType, now()->format('Y-m-d_His'));
    }
}
