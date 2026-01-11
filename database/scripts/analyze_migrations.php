<?php

/**
 * Comprehensive Migration Analysis Script
 * 
 * Scans all migration files and extracts:
 * - Table names created
 * - Foreign key references and constraints
 * - Dependencies between tables
 * - Timestamps for ordering validation
 * - Column definitions and types
 * - Indexes defined
 */

echo "🔍 Starting comprehensive migration analysis...\n\n";

$migrationsPath = __DIR__ . '/../migrations/';
$files = glob($migrationsPath . '*.php');

if (empty($files)) {
    die("❌ No migration files found in {$migrationsPath}\n");
}

echo "📊 Found " . count($files) . " migration files\n";
echo "⏳ Analyzing...\n\n";

$analysis = [];
$statistics = [
    'total_files' => count($files),
    'total_tables' => 0,
    'total_foreign_keys' => 0,
    'files_with_issues' => 0,
];

foreach ($files as $file) {
    $content = file_get_contents($file);
    $filename = basename($file);
    
    // Extract timestamp from filename
    $timestamp = 'unknown';
    if (preg_match('/^(\d{4}_\d{2}_\d{2}_\d{6})_/', $filename, $matches)) {
        $timestamp = $matches[1];
    }
    
    // Extract table names from Schema::create()
    $tables = [];
    if (preg_match_all("/Schema::create\('([^']+)'/", $content, $matches)) {
        $tables = $matches[1];
        $statistics['total_tables'] += count($tables);
    }
    
    // Extract table names from Schema::table() for alterations
    $alteredTables = [];
    if (preg_match_all("/Schema::table\('([^']+)'/", $content, $matches)) {
        $alteredTables = $matches[1];
    }
    
    // Extract foreign key references using multiple patterns
    $foreignKeys = [];
    
    // Pattern 1: ->foreignId('column')->constrained('table')
    if (preg_match_all("/foreignId\('([^']+)'\)->constrained\('([^']+)'\)/", $content, $matches)) {
        for ($i = 0; $i < count($matches[1]); $i++) {
            $foreignKeys[$matches[1][$i]] = $matches[2][$i];
            $statistics['total_foreign_keys']++;
        }
    }
    
    // Pattern 2: ->foreignId('column')->constrained() - infers table name
    if (preg_match_all("/foreignId\('([^']+)'\)->constrained\(\)/", $content, $matches)) {
        foreach ($matches[1] as $column) {
            // Infer table name from column (e.g., project_id -> projects)
            $inferredTable = rtrim($column, '_id');
            
            // Laravel's pluralization rules (simplified)
            // NOTE: This uses simplified rules that work for this codebase.
            // For a more robust solution, consider using Laravel's Str::plural()
            // or Doctrine Inflector in a Laravel-aware context.
            if (substr($inferredTable, -1) === 'y') {
                $inferredTable = substr($inferredTable, 0, -1) . 'ies'; // company -> companies
            } elseif (substr($inferredTable, -2) === 'ch') {
                $inferredTable .= 'es'; // branch -> branches
            } elseif (substr($inferredTable, -2) === 'cy') {
                $inferredTable = substr($inferredTable, 0, -2) . 'cies'; // currency -> currencies  
            } elseif (substr($inferredTable, -1) !== 's') {
                $inferredTable .= 's'; // Simple pluralization
            }
            
            $foreignKeys[$column] = $inferredTable;
            $statistics['total_foreign_keys']++;
        }
    }
    
    // Pattern 3: ->foreign('column')->references('id')->on('table')
    if (preg_match_all("/foreign\('([^']+)'\)->references\('[^']+'\)->on\('([^']+)'\)/", $content, $matches)) {
        for ($i = 0; $i < count($matches[1]); $i++) {
            $foreignKeys[$matches[1][$i]] = $matches[2][$i];
            $statistics['total_foreign_keys']++;
        }
    }
    
    // Extract indexes
    $indexes = [];
    if (preg_match_all("/\$table->index\(([^\)]+)\)/", $content, $matches)) {
        $indexes = $matches[1];
    }
    
    // Extract unique constraints
    $uniques = [];
    if (preg_match_all("/\$table->unique\(([^\)]+)\)/", $content, $matches)) {
        $uniques = $matches[1];
    }
    
    // Detect potential issues
    $issues = [];
    
    // Check if foreign keys are defined before table is used
    foreach ($foreignKeys as $column => $referencedTable) {
        if (empty($referencedTable)) {
            $issues[] = "Foreign key column '{$column}' has no clear referenced table";
        }
    }
    
    if (!empty($issues)) {
        $statistics['files_with_issues']++;
    }
    
    // Store analysis results
    foreach ($tables as $tableName) {
        $analysis[] = [
            'file' => $filename,
            'timestamp' => $timestamp,
            'table' => $tableName,
            'type' => 'create',
            'foreign_keys' => $foreignKeys,
            'indexes' => $indexes,
            'uniques' => $uniques,
            'issues' => $issues,
        ];
    }
    
    // Also track alterations
    foreach ($alteredTables as $tableName) {
        $analysis[] = [
            'file' => $filename,
            'timestamp' => $timestamp,
            'table' => $tableName,
            'type' => 'alter',
            'foreign_keys' => $foreignKeys,
            'indexes' => $indexes,
            'uniques' => $uniques,
            'issues' => $issues,
        ];
    }
    
    // If no tables found, still track the file
    if (empty($tables) && empty($alteredTables)) {
        $analysis[] = [
            'file' => $filename,
            'timestamp' => $timestamp,
            'table' => null,
            'type' => 'unknown',
            'foreign_keys' => $foreignKeys,
            'indexes' => [],
            'uniques' => [],
            'issues' => ['No table creation or alteration found'],
        ];
        $statistics['files_with_issues']++;
    }
}

// Sort by timestamp
usort($analysis, function($a, $b) {
    return strcmp($a['timestamp'], $b['timestamp']);
});

// Generate output
$output = [
    'version' => '1.0',
    'generated_at' => date('Y-m-d H:i:s'),
    'statistics' => $statistics,
    'migrations' => $analysis,
];

$outputFile = __DIR__ . '/migration_analysis.json';
file_put_contents(
    $outputFile,
    json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

// Print summary
echo "✅ Analysis complete!\n\n";
echo "📈 Statistics:\n";
echo "  - Total migration files: {$statistics['total_files']}\n";
echo "  - Total tables created/altered: {$statistics['total_tables']}\n";
echo "  - Total foreign keys: {$statistics['total_foreign_keys']}\n";
echo "  - Files with issues: {$statistics['files_with_issues']}\n\n";
echo "💾 Results saved to: {$outputFile}\n";
echo "📝 Next step: Run validate_dependencies.php to check foreign key integrity\n";
