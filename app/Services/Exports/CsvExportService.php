<?php

namespace App\Services\Exports;

use Illuminate\Support\Facades\Storage;

class CsvExportService
{
    public function export(array $data, string $reportType, string $filename = null): string
    {
        $filename = $filename ?? $this->generateFilename($reportType);
        
        $csv = $this->generateCsv($data);
        
        $path = 'reports/' . $filename;
        Storage::put($path, $csv);

        return $path;
    }

    public function download(array $data, string $reportType, string $filename = null)
    {
        $filename = $filename ?? $this->generateFilename($reportType);
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $output = fopen('php://output', 'w');
        
        if (isset($data['headers'])) {
            fputcsv($output, $data['headers']);
        }

        if (isset($data['rows'])) {
            foreach ($data['rows'] as $row) {
                fputcsv($output, $row);
            }
        }

        fclose($output);
        exit;
    }

    private function generateCsv(array $data): string
    {
        $csv = '';
        
        if (isset($data['title'])) {
            $csv .= $data['title'] . "\n\n";
        }

        if (isset($data['headers'])) {
            $csv .= implode(',', array_map(function($header) {
                return '"' . str_replace('"', '""', $header) . '"';
            }, $data['headers'])) . "\n";
        }

        if (isset($data['rows'])) {
            foreach ($data['rows'] as $row) {
                $csv .= implode(',', array_map(function($cell) {
                    return '"' . str_replace('"', '""', $cell) . '"';
                }, $row)) . "\n";
            }
        }

        return $csv;
    }

    private function generateFilename(string $reportType): string
    {
        return sprintf('%s_%s.csv', $reportType, now()->format('Y-m-d_His'));
    }
}
