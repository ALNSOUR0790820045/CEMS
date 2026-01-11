<?php

/**
 * Generate Comprehensive Migration Dependency Map
 * 
 * Creates a complete dependency graph showing all tables,
 * their dependencies, and what depends on them.
 */

echo "🔍 Generating comprehensive dependency map...\n\n";

$analysisFile = __DIR__ . '/migration_analysis.json';
$errorsFile = __DIR__ . '/dependency_errors.json';

if (!file_exists($analysisFile)) {
    die("❌ Analysis file not found. Please run analyze_migrations.php first.\n");
}

if (!file_exists($errorsFile)) {
    die("❌ Errors file not found. Please run validate_dependencies.php first.\n");
}

$analysisData = json_decode(file_get_contents($analysisFile), true);
$errorsData = json_decode(file_get_contents($errorsFile), true);

$migrations = $analysisData['migrations'];
$dependencyGraph = $errorsData['dependency_graph'];
$tablesCreated = $errorsData['tables_created'];

echo "📊 Processing " . count($migrations) . " migrations...\n";

// Build comprehensive dependency map
$dependencyMap = [];

foreach ($migrations as $migration) {
    $table = $migration['table'];
    if (!$table || $migration['type'] !== 'create') {
        continue;
    }
    
    $foreignKeys = $migration['foreign_keys'];
    $dependsOn = [];
    
    // Get tables this table depends on
    foreach ($foreignKeys as $column => $referencedTable) {
        if ($referencedTable && $referencedTable !== $table && isset($tablesCreated[$referencedTable])) {
            $dependsOn[] = $referencedTable;
        }
    }
    
    $dependsOn = array_unique($dependsOn);
    
    $dependencyMap[$table] = [
        'file' => $migration['file'],
        'timestamp' => $migration['timestamp'],
        'depends_on' => $dependsOn,
        'depended_by' => [],
        'foreign_keys' => count($foreignKeys),
        'indexes' => count($migration['indexes'] ?? []),
        'has_issues' => !empty($migration['issues']),
    ];
}

// Build reverse dependencies (what depends on this table)
foreach ($dependencyMap as $table => $info) {
    foreach ($info['depends_on'] as $parentTable) {
        if (isset($dependencyMap[$parentTable])) {
            $dependencyMap[$parentTable]['depended_by'][] = $table;
        }
    }
}

// Sort dependencies alphabetically
foreach ($dependencyMap as $table => $info) {
    sort($dependencyMap[$table]['depended_by']);
}

// Find core tables (no dependencies)
$coreTables = [];
$leafTables = [];
foreach ($dependencyMap as $table => $info) {
    if (empty($info['depends_on'])) {
        $coreTables[] = $table;
    }
    if (empty($info['depended_by'])) {
        $leafTables[] = $table;
    }
}

// Extract timestamp conflicts and missing tables from errors
$timestampConflicts = [];
foreach ($errorsData['warnings'] as $warning) {
    if ($warning['type'] === 'same_timestamp') {
        $timestampConflicts[] = [
            'child' => $warning['table'],
            'child_file' => $warning['file'],
            'parent' => $warning['references'],
            'parent_file' => $warning['parent_file'],
            'timestamp' => $warning['timestamp'],
        ];
    }
}

$missingParentTables = [];
foreach ($errorsData['critical_errors'] as $error) {
    if ($error['type'] === 'missing_parent_table') {
        $missingParentTables[] = [
            'table' => $error['table'],
            'file' => $error['file'],
            'missing_parent' => $error['references'],
            'column' => $error['column'],
        ];
    }
}

// Generate output
$output = [
    'version' => '1.0',
    'generated_at' => date('Y-m-d H:i:s'),
    'total_migrations' => count($migrations),
    'total_tables' => count($dependencyMap),
    'summary' => [
        'core_tables' => count($coreTables),
        'leaf_tables' => count($leafTables),
        'total_foreign_keys' => $analysisData['statistics']['total_foreign_keys'],
        'missing_parent_tables' => count($missingParentTables),
        'timestamp_conflicts' => count($timestampConflicts),
        'circular_dependencies' => count($errorsData['circular_dependencies'] ?? []),
    ],
    'core_tables' => $coreTables,
    'leaf_tables' => $leafTables,
    'dependency_graph' => $dependencyMap,
    'circular_dependencies' => $errorsData['circular_dependencies'] ?? [],
    'missing_parent_tables' => $missingParentTables,
    'timestamp_conflicts' => array_slice($timestampConflicts, 0, 20), // First 20 for brevity
];

$outputFile = __DIR__ . '/../migrations/migration_dependencies.json';
file_put_contents(
    $outputFile,
    json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

echo "✅ Dependency map generated!\n\n";
echo "📈 Summary:\n";
echo "  - Total tables: " . count($dependencyMap) . "\n";
echo "  - Core tables (no dependencies): " . count($coreTables) . "\n";
echo "  - Leaf tables (nothing depends on): " . count($leafTables) . "\n";
echo "  - Total foreign keys: " . $analysisData['statistics']['total_foreign_keys'] . "\n";
echo "  - Missing parent tables: " . count($missingParentTables) . "\n";
echo "  - Timestamp conflicts: " . count($timestampConflicts) . "\n\n";

echo "💾 Saved to: {$outputFile}\n";

echo "\n📋 Core Tables (Independent, created first):\n";
foreach (array_slice($coreTables, 0, 15) as $table) {
    echo "  - {$table}\n";
}
if (count($coreTables) > 15) {
    echo "  ... and " . (count($coreTables) - 15) . " more\n";
}
