<?php

namespace App\Services\Reports;

use App\Models\Company;
use App\Models\ReportHistory;
use Illuminate\Support\Facades\Cache;

abstract class BaseReportService
{
    protected Company $company;
    protected array $parameters;

    public function __construct(Company $company, array $parameters = [])
    {
        $this->company = $company;
        $this->parameters = $parameters;
    }

    abstract public function generate(): array;

    abstract public function getReportType(): string;

    protected function validateParameters(): void
    {
        // Override in child classes to validate specific parameters
    }

    protected function getCacheKey(): string
    {
        return sprintf(
            'report:%s:%s:%s',
            $this->getReportType(),
            $this->company->id,
            md5(json_encode($this->parameters))
        );
    }

    public function getReport(): array
    {
        $this->validateParameters();

        $cacheKey = $this->getCacheKey();
        
        return Cache::remember($cacheKey, now()->addMinutes(30), function () {
            return $this->generate();
        });
    }

    public function saveToHistory(string $filePath, string $fileFormat, int $userId): ReportHistory
    {
        return ReportHistory::create([
            'report_type' => $this->getReportType(),
            'report_parameters' => $this->parameters,
            'file_path' => $filePath,
            'file_format' => $fileFormat,
            'generated_by_id' => $userId,
            'generated_at' => now(),
            'company_id' => $this->company->id,
        ]);
    }

    protected function formatDate($date): string
    {
        return $date ? \Carbon\Carbon::parse($date)->format('Y-m-d') : '';
    }

    protected function formatCurrency($amount): string
    {
        return number_format($amount, 2);
    }
}
