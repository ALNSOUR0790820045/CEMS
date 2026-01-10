<?php

namespace Tests\Unit\Migrations;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MigrationOrderTest extends TestCase
{
    /**
     * Test that risk_registers table is created before risks table
     */
    public function test_risk_registers_created_before_risks(): void
    {
        $migrations = $this->getMigrationFiles();
        
        $riskRegistersIndex = null;
        $risksIndex = null;
        
        foreach ($migrations as $index => $migration) {
            if (str_contains($migration, 'create_risk_registers_table')) {
                $riskRegistersIndex = $index;
            }
            if (str_contains($migration, 'create_risks_table')) {
                $risksIndex = $index;
            }
        }
        
        $this->assertNotNull($riskRegistersIndex, 'risk_registers migration not found');
        $this->assertNotNull($risksIndex, 'risks migration not found');
        $this->assertLessThan(
            $risksIndex,
            $riskRegistersIndex,
            'risk_registers table must be created before risks table'
        );
    }

    /**
     * Test that risks table is created before risk_assessments
     */
    public function test_risks_created_before_risk_assessments(): void
    {
        $migrations = $this->getMigrationFiles();
        
        $risksIndex = null;
        $assessmentsIndex = null;
        
        foreach ($migrations as $index => $migration) {
            if (str_contains($migration, 'create_risks_table')) {
                $risksIndex = $index;
            }
            if (str_contains($migration, 'create_risk_assessments_table')) {
                $assessmentsIndex = $index;
            }
        }
        
        $this->assertNotNull($risksIndex, 'risks migration not found');
        $this->assertNotNull($assessmentsIndex, 'risk_assessments migration not found');
        $this->assertLessThan(
            $assessmentsIndex,
            $risksIndex,
            'risks table must be created before risk_assessments table'
        );
    }

    /**
     * Test that tenders table is created before tender_related_tables
     */
    public function test_tenders_created_before_tender_related_tables(): void
    {
        $migrations = $this->getMigrationFiles();
        
        $tendersIndex = null;
        $tenderRelatedIndex = null;
        
        foreach ($migrations as $index => $migration) {
            if (str_contains($migration, 'create_tenders_table')) {
                $tendersIndex = $index;
            }
            if (str_contains($migration, 'create_tender_related_tables')) {
                $tenderRelatedIndex = $index;
            }
        }
        
        $this->assertNotNull($tendersIndex, 'tenders migration not found');
        $this->assertNotNull($tenderRelatedIndex, 'tender_related_tables migration not found');
        $this->assertLessThan(
            $tenderRelatedIndex,
            $tendersIndex,
            'tenders table must be created before tender_related_tables'
        );
    }

    /**
     * Test that payroll_periods is created before payroll_entries
     */
    public function test_payroll_periods_created_before_payroll_entries(): void
    {
        $migrations = $this->getMigrationFiles();
        
        $periodsIndex = null;
        $entriesIndex = null;
        
        foreach ($migrations as $index => $migration) {
            if (str_contains($migration, 'create_payroll_periods_table')) {
                $periodsIndex = $index;
            }
            if (str_contains($migration, 'create_payroll_entries_table')) {
                $entriesIndex = $index;
            }
        }
        
        $this->assertNotNull($periodsIndex, 'payroll_periods migration not found');
        $this->assertNotNull($entriesIndex, 'payroll_entries migration not found');
        $this->assertLessThan(
            $entriesIndex,
            $periodsIndex,
            'payroll_periods table must be created before payroll_entries table'
        );
    }

    /**
     * Test that payroll_entries is created before payroll_allowances
     */
    public function test_payroll_entries_created_before_payroll_allowances(): void
    {
        $migrations = $this->getMigrationFiles();
        
        $entriesIndex = null;
        $allowancesIndex = null;
        
        foreach ($migrations as $index => $migration) {
            if (str_contains($migration, 'create_payroll_entries_table')) {
                $entriesIndex = $index;
            }
            if (str_contains($migration, 'create_payroll_allowances_table')) {
                $allowancesIndex = $index;
            }
        }
        
        $this->assertNotNull($entriesIndex, 'payroll_entries migration not found');
        $this->assertNotNull($allowancesIndex, 'payroll_allowances migration not found');
        $this->assertLessThan(
            $allowancesIndex,
            $entriesIndex,
            'payroll_entries table must be created before payroll_allowances table'
        );
    }

    /**
     * Test that projects table is created before project_wbs table
     */
    public function test_projects_created_before_project_wbs(): void
    {
        $migrations = $this->getMigrationFiles();
        
        $projectsIndex = null;
        $wbsIndex = null;
        
        foreach ($migrations as $index => $migration) {
            if (str_contains($migration, 'create_projects_table')) {
                $projectsIndex = $index;
            }
            if (str_contains($migration, 'create_project_wbs_table')) {
                $wbsIndex = $index;
            }
        }
        
        $this->assertNotNull($projectsIndex, 'projects migration not found');
        $this->assertNotNull($wbsIndex, 'project_wbs migration not found');
        $this->assertLessThan(
            $wbsIndex,
            $projectsIndex,
            'projects table must be created before project_wbs table'
        );
    }

    /**
     * Test that companies table is created before projects table
     */
    public function test_companies_created_before_projects(): void
    {
        $migrations = $this->getMigrationFiles();
        
        $companiesIndex = null;
        $projectsIndex = null;
        
        foreach ($migrations as $index => $migration) {
            if (str_contains($migration, 'create_companies_table')) {
                $companiesIndex = $index;
            }
            if (str_contains($migration, 'create_projects_table')) {
                $projectsIndex = $index;
            }
        }
        
        $this->assertNotNull($companiesIndex, 'companies migration not found');
        $this->assertNotNull($projectsIndex, 'projects migration not found');
        $this->assertLessThan(
            $projectsIndex,
            $companiesIndex,
            'companies table must be created before projects table'
        );
    }

    /**
     * Test that currencies table is created before branches enhancement
     */
    public function test_currencies_created_before_branches_enhancement(): void
    {
        $migrations = $this->getMigrationFiles();
        
        $currenciesIndex = null;
        $branchesEnhanceIndex = null;
        
        foreach ($migrations as $index => $migration) {
            if (str_contains($migration, 'create_currencies_table')) {
                $currenciesIndex = $index;
            }
            if (str_contains($migration, 'add_currency_to_branches')) {
                $branchesEnhanceIndex = $index;
            }
        }
        
        $this->assertNotNull($currenciesIndex, 'currencies migration not found');
        $this->assertNotNull($branchesEnhanceIndex, 'branches enhancement migration not found');
        $this->assertLessThan(
            $branchesEnhanceIndex,
            $currenciesIndex,
            'currencies table must be created before branches enhancement'
        );
    }

    /**
     * Get all migration files in chronological order
     */
    private function getMigrationFiles(): array
    {
        $path = database_path('migrations');
        $files = File::files($path);
        
        $migrations = array_map(function ($file) {
            return $file->getFilename();
        }, $files);
        
        sort($migrations);
        
        return $migrations;
    }
}
