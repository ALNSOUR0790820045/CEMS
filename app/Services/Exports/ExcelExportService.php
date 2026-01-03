<?php

namespace App\Services\Exports;

use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExcelExportService
{
    public function export(array $data, string $reportType, string $filename = null): string
    {
        $filename = $filename ?? $this->generateFilename($reportType);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $this->populateSheet($sheet, $data);
        $this->applyFormatting($sheet);

        $writer = new Xlsx($spreadsheet);
        $tempPath = storage_path('app/temp/' . $filename);
        
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }
        
        $writer->save($tempPath);

        $path = 'reports/' . $filename;
        Storage::put($path, file_get_contents($tempPath));
        unlink($tempPath);

        return $path;
    }

    public function download(array $data, string $reportType, string $filename = null)
    {
        $filename = $filename ?? $this->generateFilename($reportType);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $this->populateSheet($sheet, $data);
        $this->applyFormatting($sheet);

        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    private function populateSheet($sheet, array $data): void
    {
        if (isset($data['title'])) {
            $sheet->setCellValue('A1', $data['title']);
            $sheet->mergeCells('A1:F1');
        }

        if (isset($data['headers']) && isset($data['rows'])) {
            $headerRow = isset($data['title']) ? 3 : 1;
            $col = 'A';
            foreach ($data['headers'] as $header) {
                $sheet->setCellValue($col . $headerRow, $header);
                $col++;
            }

            $row = $headerRow + 1;
            foreach ($data['rows'] as $rowData) {
                $col = 'A';
                foreach ($rowData as $cellData) {
                    $sheet->setCellValue($col . $row, $cellData);
                    $col++;
                }
                $row++;
            }
        }
    }

    private function applyFormatting($sheet): void
    {
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8E8E8']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ];

        $sheet->getStyle('A3:Z3')->applyFromArray($headerStyle);
        $sheet->getColumnDimension('A')->setAutoSize(true);
    }

    private function generateFilename(string $reportType): string
    {
        return sprintf('%s_%s.xlsx', $reportType, now()->format('Y-m-d_His'));
    }
}
