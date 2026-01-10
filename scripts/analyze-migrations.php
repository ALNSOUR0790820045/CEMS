#!/usr/bin/env php
<?php
/**
 * Comprehensive Migration Analyzer
 * Analyzes all migrations and generates detailed report
 * 
 * Usage: php scripts/analyze-migrations.php
 */

class MigrationAnalyzer
{
    private string $migrationsPath;
    private array $migrations = [];
    private array $tables = [];
    private array $foreignKeys = [];
    private array $issues = [];
    private array $statistics = [];

    public function __construct(string $migrationsPath)
    {
        $this->migrationsPath = $migrationsPath;
    }

    /**
     * Main analysis entry point
     */
    public function analyze(): array
    {
        echo "🔍 Starting comprehensive migration analysis...\n\n";

        $this->loadMigrations();
        $this->parseMigrations();
        $this->analyzeForeignKeys();
        $this->analyzeTableStructure();
        $this->analyzeDataTypes();
        $this->analyzeNamingConventions();
        $this->calculateStatistics();

        echo "✅ Analysis complete!\n\n";

        return $this->generateReport();
    }

    /**
     * Load all migration files
     */
    private function loadMigrations(): void
    {
        echo "📂 Loading migrations from {$this->migrationsPath}...\n";

        $files = glob($this->migrationsPath . '/*.php');
        sort($files);

        foreach ($files as $file) {
            $filename = basename($file);
            $this->migrations[$filename] = [
                'path' => $file,
                'filename' => $filename,
                'content' => file_get_contents($file),
                'timestamp' => $this->extractTimestamp($filename),
                'name' => $this->extractName($filename),
            ];
        }

        echo "   Found " . count($this->migrations) . " migration files\n\n";
    }

    /**
     * Parse migrations to extract table and foreign key information
     */
    private function parseMigrations(): void
    {
        echo "🔬 Parsing migrations for structure analysis...\n";

        foreach ($this->migrations as $filename => $migration) {
            $content = $migration['content'];

            // Extract table creation
            if (preg_match("/Schema::create\('([^']+)'/", $content, $matches)) {
                $tableName = $matches[1];
                $this->tables[$tableName] = [
                    'migration' => $filename,
                    'timestamp' => $migration['timestamp'],
                    'foreign_keys' => [],
                    'columns' => [],
                ];

                // Extract foreign keys
                $this->extractForeignKeys($tableName, $content, $filename);

                // Extract columns with data types
                $this->extractColumns($tableName, $content);
            }

            // Check for table modifications (Schema::table)
            if (preg_match_all("/Schema::table\('([^']+)'/", $content, $matches)) {
                foreach ($matches[1] as $tableName) {
                    $this->extractForeignKeys($tableName, $content, $filename, true);
                }
            }
        }

        echo "   Found " . count($this->tables) . " tables\n\n";
    }

    /**
     * Extract foreign keys from migration content
     */
    private function extractForeignKeys(string $tableName, string $content, string $filename, bool $isModification = false): void
    {
        // Pattern for foreignId()->constrained()
        preg_match_all("/\\\$table->foreignId\('([^']+)'\)->(?:nullable\(\)->)?constrained\((?:'([^']+)')?\)/", $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $columnName = $match[1];
            $referencedTable = $match[2] ?? $this->guessTableFromColumn($columnName);

            $fkInfo = [
                'table' => $tableName,
                'column' => $columnName,
                'references' => $referencedTable,
                'migration' => $filename,
                'timestamp' => $this->extractTimestamp($filename),
                'is_modification' => $isModification,
            ];

            $this->foreignKeys[] = $fkInfo;

            if (isset($this->tables[$tableName])) {
                $this->tables[$tableName]['foreign_keys'][] = $fkInfo;
            }
        }
    }

    /**
     * Extract column definitions
     */
    private function extractColumns(string $tableName, string $content): void
    {
        // Extract various column types
        $patterns = [
            'decimal' => "/\\\$table->decimal\('([^']+)',\s*(\d+),\s*(\d+)\)/",
            'string' => "/\\\$table->string\('([^']+)'(?:,\s*(\d+))?\)/",
            'enum' => "/\\\$table->enum\('([^']+)'/",
            'text' => "/\\\$table->text\('([^']+)'/",
            'integer' => "/\\\$table->(?:integer|bigInteger|unsignedBigInteger)\('([^']+)'/",
        ];

        foreach ($patterns as $type => $pattern) {
            if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $column = [
                        'name' => $match[1],
                        'type' => $type,
                    ];

                    if ($type === 'decimal' && isset($match[2], $match[3])) {
                        $column['precision'] = $match[2];
                        $column['scale'] = $match[3];
                    } elseif ($type === 'string' && isset($match[2])) {
                        $column['length'] = $match[2];
                    }

                    $this->tables[$tableName]['columns'][] = $column;
                }
            }
        }
    }

    /**
     * Analyze foreign key dependencies
     */
    private function analyzeForeignKeys(): void
    {
        echo "🔗 Analyzing foreign key dependencies...\n";

        foreach ($this->foreignKeys as $fk) {
            $referencedTable = $fk['references'];

            // Check if referenced table exists
            if (!isset($this->tables[$referencedTable])) {
                $this->addIssue('critical', [
                    'type' => 'missing_referenced_table',
                    'file' => $fk['migration'],
                    'table' => $fk['table'],
                    'column' => $fk['column'],
                    'references' => $referencedTable,
                    'issue' => "References table '{$referencedTable}' which doesn't exist in migrations",
                    'fix' => "Create migration for '{$referencedTable}' table or fix the reference",
                ]);
                continue;
            }

            // Skip self-referencing tables (parent_id, etc.) - these are valid
            if ($fk['table'] === $referencedTable) {
                continue;
            }

            // Check creation order (only for different tables)
            if (!$fk['is_modification']) {
                $tableTimestamp = $this->tables[$fk['table']]['timestamp'];
                $referencedTimestamp = $this->tables[$referencedTable]['timestamp'];

                // Only flag if created at exactly the same time (same migration file)
                // This is an issue when tables are in the same file and reference each other
                if ($tableTimestamp === $referencedTimestamp) {
                    $this->addIssue('critical', [
                        'type' => 'foreign_key_order',
                        'file' => $fk['migration'],
                        'table' => $fk['table'],
                        'column' => $fk['column'],
                        'references' => $referencedTable,
                        'issue' => "Table '{$fk['table']}' references '{$referencedTable}' but they are created in the same migration file",
                        'fix' => "Split into separate migration files or ensure '{$referencedTable}' is created first in the file",
                        'table_timestamp' => $tableTimestamp,
                        'referenced_timestamp' => $referencedTimestamp,
                    ]);
                } elseif ($tableTimestamp < $referencedTimestamp) {
                    $this->addIssue('critical', [
                        'type' => 'foreign_key_order',
                        'file' => $fk['migration'],
                        'table' => $fk['table'],
                        'column' => $fk['column'],
                        'references' => $referencedTable,
                        'issue' => "Table '{$fk['table']}' is created before '{$referencedTable}' but references it",
                        'fix' => "Rename migration file to run after '{$this->tables[$referencedTable]['migration']}'",
                        'table_timestamp' => $tableTimestamp,
                        'referenced_timestamp' => $referencedTimestamp,
                    ]);
                }
            }
        }

        echo "   Analyzed " . count($this->foreignKeys) . " foreign key relationships\n\n";
    }

    /**
     * Analyze table structure
     */
    private function analyzeTableStructure(): void
    {
        echo "📊 Analyzing table structure...\n";

        foreach ($this->tables as $tableName => $tableInfo) {
            // Check for common issues
            if (empty($tableInfo['columns'])) {
                $this->addIssue('medium', [
                    'type' => 'no_columns_extracted',
                    'file' => $tableInfo['migration'],
                    'table' => $tableName,
                    'issue' => "No columns could be extracted (may be a parsing limitation)",
                ]);
            }
        }

        echo "   Analyzed " . count($this->tables) . " table structures\n\n";
    }

    /**
     * Analyze data type consistency
     */
    private function analyzeDataTypes(): void
    {
        echo "🔢 Analyzing data type consistency...\n";

        $decimalColumns = [];
        $stringColumns = [];

        foreach ($this->tables as $tableName => $tableInfo) {
            foreach ($tableInfo['columns'] as $column) {
                if ($column['type'] === 'decimal') {
                    $key = $column['name'];
                    if (!isset($decimalColumns[$key])) {
                        $decimalColumns[$key] = [];
                    }
                    $decimalColumns[$key][] = [
                        'table' => $tableName,
                        'precision' => $column['precision'],
                        'scale' => $column['scale'],
                        'migration' => $tableInfo['migration'],
                    ];
                } elseif ($column['type'] === 'string') {
                    $key = $column['name'];
                    if (!isset($stringColumns[$key])) {
                        $stringColumns[$key] = [];
                    }
                    $stringColumns[$key][] = [
                        'table' => $tableName,
                        'length' => $column['length'] ?? 255,
                        'migration' => $tableInfo['migration'],
                    ];
                }
            }
        }

        // Check for inconsistencies
        foreach ($decimalColumns as $columnName => $occurrences) {
            if (count($occurrences) > 1) {
                $precisions = array_unique(array_column($occurrences, 'precision'));
                if (count($precisions) > 1) {
                    $this->addIssue('medium', [
                        'type' => 'inconsistent_decimal_precision',
                        'column' => $columnName,
                        'issue' => "Column '{$columnName}' has inconsistent decimal precision",
                        'occurrences' => $occurrences,
                        'fix' => 'Standardize decimal precision across all tables',
                    ]);
                }
            }
        }

        foreach ($stringColumns as $columnName => $occurrences) {
            if (count($occurrences) > 1) {
                $lengths = array_unique(array_column($occurrences, 'length'));
                if (count($lengths) > 1) {
                    $this->addIssue('low', [
                        'type' => 'inconsistent_string_length',
                        'column' => $columnName,
                        'issue' => "Column '{$columnName}' has inconsistent string length",
                        'occurrences' => $occurrences,
                        'fix' => 'Standardize string length across all tables',
                    ]);
                }
            }
        }

        echo "   Analyzed data type consistency\n\n";
    }

    /**
     * Analyze naming conventions
     */
    private function analyzeNamingConventions(): void
    {
        echo "📝 Analyzing naming conventions...\n";

        $problematicNames = [
            'a_r_receipts' => 'ar_receipts or accounts_receivable_receipts',
            'a_r_invoices' => 'ar_invoices or accounts_receivable_invoices',
            'a_r_receipt_allocations' => 'ar_receipt_allocations',
            'g_l_accounts' => 'gl_accounts or general_ledger_accounts',
            'g_r_n_items' => 'grn_items or goods_receipt_note_items',
            'i_p_c_s' => 'ipcs or interim_payment_certificates',
        ];

        foreach ($this->tables as $tableName => $tableInfo) {
            if (isset($problematicNames[$tableName])) {
                $this->addIssue('low', [
                    'type' => 'naming_convention',
                    'file' => $tableInfo['migration'],
                    'table' => $tableName,
                    'issue' => "Table name uses inconsistent underscore pattern",
                    'suggestion' => $problematicNames[$tableName],
                    'fix' => 'Rename table to follow consistent naming convention',
                ]);
            }
        }

        echo "   Analyzed naming conventions\n\n";
    }

    /**
     * Calculate statistics
     */
    private function calculateStatistics(): void
    {
        $this->statistics = [
            'total_migrations' => count($this->migrations),
            'total_tables' => count($this->tables),
            'total_foreign_keys' => count($this->foreignKeys),
            'critical_issues' => count($this->issues['critical'] ?? []),
            'high_issues' => count($this->issues['high'] ?? []),
            'medium_issues' => count($this->issues['medium'] ?? []),
            'low_issues' => count($this->issues['low'] ?? []),
        ];
    }

    /**
     * Generate comprehensive report
     */
    public function generateReport(): array
    {
        return [
            'generated_at' => date('Y-m-d H:i:s'),
            'total_migrations' => $this->statistics['total_migrations'],
            'statistics' => $this->statistics,
            'issues' => $this->issues,
            'tables' => $this->tables,
            'foreign_keys' => $this->foreignKeys,
        ];
    }

    /**
     * Add an issue to the report
     */
    private function addIssue(string $priority, array $issue): void
    {
        if (!isset($this->issues[$priority])) {
            $this->issues[$priority] = [];
        }
        $this->issues[$priority][] = $issue;
    }

    /**
     * Extract timestamp from filename
     */
    private function extractTimestamp(string $filename): string
    {
        if (preg_match('/^(\d{4}_\d{2}_\d{2}_\d{6})/', $filename, $matches)) {
            return $matches[1];
        }
        return '0000_00_00_000000';
    }

    /**
     * Extract migration name from filename
     */
    private function extractName(string $filename): string
    {
        return preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', str_replace('.php', '', $filename));
    }

    /**
     * Guess table name from foreign key column name
     */
    private function guessTableFromColumn(string $columnName): string
    {
        // Remove _id suffix
        $tableName = preg_replace('/_id$/', '', $columnName);

        // Handle irregular plurals
        $irregularPlurals = [
            'company' => 'companies',
            'country' => 'countries',
            'city' => 'cities',
            'category' => 'categories',
            'currency' => 'currencies',
            'activity' => 'activities',
            'entry' => 'entries',
            'diary' => 'diaries',
            'liability' => 'liabilities',
        ];

        if (isset($irregularPlurals[$tableName])) {
            return $irregularPlurals[$tableName];
        }

        // Handle words ending in 'ch', 'sh', 'ss', 'x', 'z' -> add 'es'
        if (preg_match('/(ch|sh|ss|x|z)$/', $tableName)) {
            return $tableName . 'es';
        }

        // Handle words ending in 'y' preceded by consonant -> 'ies'
        if (preg_match('/[^aeiou]y$/', $tableName)) {
            return preg_replace('/y$/', 'ies', $tableName);
        }

        // Pluralize (simple approach)
        if (!str_ends_with($tableName, 's')) {
            $tableName .= 's';
        }

        return $tableName;
    }

    /**
     * Print summary to console
     */
    public function printSummary(array $report): void
    {
        echo "\n";
        echo "═══════════════════════════════════════════════════════════\n";
        echo "                  MIGRATION ANALYSIS REPORT                 \n";
        echo "═══════════════════════════════════════════════════════════\n\n";

        echo "📊 STATISTICS\n";
        echo "─────────────────────────────────────────────────────────\n";
        echo "  Total Migrations:   {$report['statistics']['total_migrations']}\n";
        echo "  Total Tables:       {$report['statistics']['total_tables']}\n";
        echo "  Total Foreign Keys: {$report['statistics']['total_foreign_keys']}\n\n";

        echo "⚠️  ISSUES BY PRIORITY\n";
        echo "─────────────────────────────────────────────────────────\n";
        echo "  🔴 Critical: {$report['statistics']['critical_issues']}\n";
        echo "  🟠 High:     {$report['statistics']['high_issues']}\n";
        echo "  🟡 Medium:   {$report['statistics']['medium_issues']}\n";
        echo "  🟢 Low:      {$report['statistics']['low_issues']}\n\n";

        if (!empty($report['issues']['critical'])) {
            echo "🔴 CRITICAL ISSUES\n";
            echo "─────────────────────────────────────────────────────────\n";
            foreach ($report['issues']['critical'] as $index => $issue) {
                echo "  " . ($index + 1) . ". {$issue['issue']}\n";
                echo "     File: {$issue['file']}\n";
                if (isset($issue['table'])) {
                    echo "     Table: {$issue['table']}\n";
                }
                if (isset($issue['fix'])) {
                    echo "     Fix: {$issue['fix']}\n";
                }
                echo "\n";
            }
        }

        echo "═══════════════════════════════════════════════════════════\n\n";
    }

    /**
     * Save report to file
     */
    public function saveReport(array $report, string $outputPath): void
    {
        $json = json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents($outputPath, $json);
        echo "💾 Report saved to: {$outputPath}\n";
    }
}

// Main execution
if (php_sapi_name() === 'cli') {
    $migrationsPath = __DIR__ . '/../database/migrations';
    $outputPath = __DIR__ . '/../reports/migration-analysis-report.json';

    $analyzer = new MigrationAnalyzer($migrationsPath);
    $report = $analyzer->analyze();
    $analyzer->printSummary($report);
    $analyzer->saveReport($report, $outputPath);

    echo "\n✅ Analysis complete! Check the detailed report at:\n";
    echo "   {$outputPath}\n\n";

    // Exit with error code if there are critical issues
    $criticalCount = $report['statistics']['critical_issues'];
    if ($criticalCount > 0) {
        echo "⚠️  Found {$criticalCount} critical issues that need attention!\n";
        exit(1);
    }

    exit(0);
}
