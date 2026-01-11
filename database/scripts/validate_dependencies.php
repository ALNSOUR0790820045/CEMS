<?php

/**
 * Migration Dependency Validation Script
 * 
 * Validates that all foreign keys reference tables that are created
 * before the tables that reference them.
 * 
 * Checks for:
 * - Missing parent tables
 * - Circular dependencies
 * - Timestamp conflicts (child created before parent)
 */

echo "🔍 Starting dependency validation...\n\n";

$analysisFile = __DIR__ . '/migration_analysis.json';

if (!file_exists($analysisFile)) {
    die("❌ Analysis file not found. Please run analyze_migrations.php first.\n");
}

$data = json_decode(file_get_contents($analysisFile), true);
$migrations = $data['migrations'];

echo "📊 Analyzing {$data['statistics']['total_files']} migrations...\n\n";

// Build tables map: table_name => [timestamp, file]
$tablesCreated = [];
$tableAlterations = [];

foreach ($migrations as $migration) {
    if ($migration['type'] === 'create' && $migration['table']) {
        // Keep only the first creation (in case of duplicates)
        if (!isset($tablesCreated[$migration['table']])) {
            $tablesCreated[$migration['table']] = [
                'timestamp' => $migration['timestamp'],
                'file' => $migration['file'],
            ];
        }
    } elseif ($migration['type'] === 'alter' && $migration['table']) {
        if (!isset($tableAlterations[$migration['table']])) {
            $tableAlterations[$migration['table']] = [];
        }
        $tableAlterations[$migration['table']][] = [
            'timestamp' => $migration['timestamp'],
            'file' => $migration['file'],
            'foreign_keys' => $migration['foreign_keys'],
        ];
    }
}

echo "✅ Found " . count($tablesCreated) . " unique tables created\n";
echo "✅ Found " . count($tableAlterations) . " tables with alterations\n\n";

// Validate dependencies
$errors = [];
$warnings = [];
$circularDeps = [];

foreach ($migrations as $migration) {
    $table = $migration['table'];
    $foreignKeys = $migration['foreign_keys'];
    $timestamp = $migration['timestamp'];
    $file = $migration['file'];
    
    if (empty($table) || empty($foreignKeys)) {
        continue;
    }
    
    // Check each foreign key
    foreach ($foreignKeys as $column => $referencedTable) {
        // Skip self-references and empty references
        if ($referencedTable === $table || empty($referencedTable)) {
            continue;
        }
        
        // Check if referenced table exists
        if (!isset($tablesCreated[$referencedTable])) {
            $errors[] = [
                'severity' => 'critical',
                'type' => 'missing_parent_table',
                'file' => $file,
                'timestamp' => $timestamp,
                'table' => $table,
                'column' => $column,
                'references' => $referencedTable,
                'message' => "Table '{$table}' references '{$referencedTable}' which is never created",
            ];
            continue;
        }
        
        // Check timestamp ordering
        $parentTimestamp = $tablesCreated[$referencedTable]['timestamp'];
        $parentFile = $tablesCreated[$referencedTable]['file'];
        
        if ($migration['type'] === 'create' && $timestamp < $parentTimestamp) {
            $errors[] = [
                'severity' => 'critical',
                'type' => 'timestamp_conflict',
                'file' => $file,
                'timestamp' => $timestamp,
                'table' => $table,
                'column' => $column,
                'references' => $referencedTable,
                'parent_timestamp' => $parentTimestamp,
                'parent_file' => $parentFile,
                'message' => "Child table '{$table}' (timestamp: {$timestamp}) is created BEFORE parent table '{$referencedTable}' (timestamp: {$parentTimestamp})",
            ];
        } elseif ($timestamp === $parentTimestamp) {
            $warnings[] = [
                'severity' => 'warning',
                'type' => 'same_timestamp',
                'file' => $file,
                'timestamp' => $timestamp,
                'table' => $table,
                'column' => $column,
                'references' => $referencedTable,
                'parent_file' => $parentFile,
                'message' => "Table '{$table}' and its parent '{$referencedTable}' have the same timestamp. Execution order is not guaranteed.",
            ];
        }
    }
}

// Check for potential circular dependencies
echo "🔄 Checking for circular dependencies...\n";
$dependencyGraph = [];
foreach ($migrations as $migration) {
    if (empty($migration['table']) || empty($migration['foreign_keys'])) {
        continue;
    }
    
    $table = $migration['table'];
    if (!isset($dependencyGraph[$table])) {
        $dependencyGraph[$table] = [];
    }
    
    foreach ($migration['foreign_keys'] as $column => $referencedTable) {
        if ($referencedTable !== $table && !empty($referencedTable)) {
            $dependencyGraph[$table][] = $referencedTable;
        }
    }
}

// Simple circular dependency detection (A->B, B->A)
foreach ($dependencyGraph as $table => $dependencies) {
    foreach ($dependencies as $dependency) {
        if (isset($dependencyGraph[$dependency]) && in_array($table, $dependencyGraph[$dependency])) {
            $circularDeps[] = [
                'table1' => $table,
                'table2' => $dependency,
                'message' => "Circular dependency detected between '{$table}' and '{$dependency}'",
            ];
        }
    }
}

// Remove duplicates from circular deps
$circularDeps = array_unique($circularDeps, SORT_REGULAR);

// Output results
echo "\n" . str_repeat("=", 70) . "\n";
echo "📋 VALIDATION RESULTS\n";
echo str_repeat("=", 70) . "\n\n";

if (empty($errors) && empty($warnings) && empty($circularDeps)) {
    echo "✅ No dependency issues found!\n";
    echo "✅ All foreign keys reference existing tables\n";
    echo "✅ All timestamps are properly ordered\n";
} else {
    if (!empty($errors)) {
        echo "❌ CRITICAL ERRORS: " . count($errors) . "\n\n";
        foreach ($errors as $i => $error) {
            echo ($i + 1) . ". {$error['type']}: {$error['message']}\n";
            echo "   File: {$error['file']}\n";
            echo "   Table: {$error['table']}\n";
            echo "   Column: {$error['column']}\n";
            if (isset($error['parent_file'])) {
                echo "   Parent file: {$error['parent_file']}\n";
            }
            echo "\n";
        }
    }
    
    if (!empty($warnings)) {
        echo "⚠️  WARNINGS: " . count($warnings) . "\n\n";
        foreach ($warnings as $i => $warning) {
            echo ($i + 1) . ". {$warning['message']}\n";
            echo "   File: {$warning['file']}\n";
            echo "   Parent file: {$warning['parent_file']}\n";
            echo "\n";
        }
    }
    
    if (!empty($circularDeps)) {
        echo "🔄 CIRCULAR DEPENDENCIES: " . count($circularDeps) . "\n\n";
        foreach ($circularDeps as $i => $dep) {
            echo ($i + 1) . ". {$dep['message']}\n\n";
        }
    }
}

// Save detailed report
$report = [
    'version' => '1.0',
    'generated_at' => date('Y-m-d H:i:s'),
    'summary' => [
        'total_errors' => count($errors),
        'total_warnings' => count($warnings),
        'circular_dependencies' => count($circularDeps),
        'tables_analyzed' => count($tablesCreated),
    ],
    'critical_errors' => $errors,
    'warnings' => $warnings,
    'circular_dependencies' => $circularDeps,
    'tables_created' => $tablesCreated,
    'dependency_graph' => $dependencyGraph,
];

$reportFile = __DIR__ . '/dependency_errors.json';
file_put_contents(
    $reportFile,
    json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

echo "\n💾 Detailed report saved to: {$reportFile}\n";
echo "📝 Review the JSON file for complete details\n";

// Exit with error code if critical issues found
if (!empty($errors)) {
    exit(1);
}
