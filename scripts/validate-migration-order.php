<?php
/**
 * Migration Order Validation Script
 * 
 * This script validates that all database migrations are ordered correctly
 * ensuring that parent tables are created before child tables that reference them.
 * 
 * Usage: php scripts/validate-migration-order.php
 */

$migrationsDir = __DIR__ . '/../database/migrations';
$files = glob($migrationsDir . '/*.php');

if (empty($files)) {
    echo "❌ ERROR: No migration files found in {$migrationsDir}\n";
    exit(1);
}

// Parse all migrations
$migrations = [];
$tableToMigration = [];

foreach ($files as $file) {
    $basename = basename($file);
    
    // Extract timestamp and name
    if (!preg_match('/^(\d{4}_\d{2}_\d{2}_\d{6})_(.+)\.php$/', $basename, $matches)) {
        continue;
    }
    
    $content = file_get_contents($file);
    
    // Extract table name from Schema::create
    preg_match('/Schema::create\([\'"]([a-z_]+)[\'"]/i', $content, $tableMatch);
    $tableName = $tableMatch[1] ?? null;
    
    // Extract foreign key dependencies
    // Pattern 1: ->constrained('table_name')
    preg_match_all('/->constrained\([\'"]([a-z_]+)[\'"]\)/i', $content, $fkMatches1);
    
    // Pattern 2: ->constrained() without parameter (uses convention)
    // Extract the field name from foreignId('field_name') when followed by constrained()
    preg_match_all('/foreignId\([\'"]([a-z_]+)[\'"]\)\s*->\s*constrained\(\)/i', $content, $fkMatches2);
    
    $singularDeps = [];
    foreach ($fkMatches2[1] ?? [] as $field) {
        // Remove _id suffix to get the singular table name
        $singular = preg_replace('/_id$/', '', $field);
        $singularDeps[] = $singular;
    }
    
    // Irregular plurals mapping
    $irregularPlurals = [
        'person' => 'people',
        'child' => 'children',
        'tooth' => 'teeth',
        'foot' => 'feet',
        'mouse' => 'mice',
        'goose' => 'geese',
        'man' => 'men',
        'woman' => 'women',
        'ox' => 'oxen',
        'staff' => 'staff',
        'deer' => 'deer',
        'sheep' => 'sheep',
        'fish' => 'fish',
        'series' => 'series',
        'species' => 'species',
        'photo' => 'photos',  // photo -> photos (not photoes)
    ];
    
    // Pluralize singular dependencies
    $pluralDeps = array_map(function($singular) use ($irregularPlurals) {
        // Check irregular plurals first
        if (isset($irregularPlurals[$singular])) {
            return $irregularPlurals[$singular];
        }
        
        // Standard English pluralization rules
        if (substr($singular, -1) === 'y' && !in_array(substr($singular, -2, 1), ['a', 'e', 'i', 'o', 'u'])) {
            // company -> companies, category -> categories
            return substr($singular, 0, -1) . 'ies';
        } elseif (preg_match('/(ch|sh|ss|x|z|o)$/', $singular)) {
            // address -> addresses, class -> classes, box -> boxes
            return $singular . 'es';
        } elseif (preg_match('/(f|fe)$/', $singular)) {
            // leaf -> leaves, knife -> knives
            return preg_replace('/(f|fe)$/', 'ves', $singular);
        } else {
            // Most cases: project -> projects
            return $singular . 's';
        }
    }, $singularDeps);
    
    $dependencies = array_merge(
        $fkMatches1[1] ?? [],
        $pluralDeps
    );
    $dependencies = array_unique($dependencies);
    
    // Filter out self-references
    if ($tableName) {
        $dependencies = array_filter($dependencies, fn($dep) => $dep !== $tableName);
    }
    
    $migration = [
        'timestamp' => $matches[1],
        'name' => $matches[2],
        'basename' => $basename,
        'file' => $file,
        'table' => $tableName,
        'dependencies' => array_values($dependencies)
    ];
    
    $migrations[] = $migration;
    
    if ($tableName) {
        if (isset($tableToMigration[$tableName])) {
            // Multiple migrations creating the same table
            echo "⚠️  WARNING: Multiple migrations create table '{$tableName}':\n";
            echo "   - {$tableToMigration[$tableName]['basename']}\n";
            echo "   - {$basename}\n\n";
        }
        $tableToMigration[$tableName] = $migration;
    }
}

// Sort migrations by timestamp (execution order)
usort($migrations, function($a, $b) {
    return strcmp($a['timestamp'], $b['timestamp']);
});

// Build execution order map
$executionOrder = [];
foreach ($migrations as $index => $migration) {
    if ($migration['table']) {
        $executionOrder[$migration['table']] = $index;
    }
}

// Validation
$errors = [];
$warnings = [];
$duplicateTimestamps = [];

echo "=== MIGRATION ORDER VALIDATION ===\n\n";
echo "Total migrations: " . count($migrations) . "\n";
echo "Tables to create: " . count($tableToMigration) . "\n\n";

// Check 1: Duplicate timestamps
echo "▶ Checking for duplicate timestamps...\n";
$timestampGroups = [];
foreach ($migrations as $migration) {
    $timestampGroups[$migration['timestamp']][] = $migration;
}

$hasDuplicates = false;
foreach ($timestampGroups as $timestamp => $group) {
    if (count($group) > 1) {
        $hasDuplicates = true;
        $duplicateTimestamps[$timestamp] = $group;
        $warnings[] = "Duplicate timestamp {$timestamp} used by " . count($group) . " migrations";
    }
}

if ($hasDuplicates) {
    echo "  ⚠️  Found " . count($duplicateTimestamps) . " duplicate timestamps\n";
} else {
    echo "  ✅ All timestamps are unique\n";
}
echo "\n";

// Check 2: Foreign key ordering
echo "▶ Checking foreign key dependencies...\n";
$orderingErrors = [];

foreach ($migrations as $index => $migration) {
    if (empty($migration['dependencies'])) {
        continue;
    }
    
    foreach ($migration['dependencies'] as $dependency) {
        // Check if dependency table exists
        if (!isset($executionOrder[$dependency])) {
            $orderingErrors[] = [
                'migration' => $migration,
                'dependency' => $dependency,
                'issue' => 'missing',
                'index' => $index
            ];
            continue;
        }
        
        // Check if dependency is created before this migration
        $depIndex = $executionOrder[$dependency];
        if ($depIndex >= $index) {
            $orderingErrors[] = [
                'migration' => $migration,
                'dependency' => $dependency,
                'issue' => 'order',
                'index' => $index,
                'depIndex' => $depIndex,
                'depMigration' => $migrations[$depIndex]
            ];
        }
    }
}

if (empty($orderingErrors)) {
    echo "  ✅ All foreign key dependencies are properly ordered\n";
} else {
    echo "  ❌ Found " . count($orderingErrors) . " ordering issues\n";
}
echo "\n";

// Display errors
if (!empty($orderingErrors)) {
    echo "=== ORDERING ERRORS ===\n\n";
    
    foreach ($orderingErrors as $error) {
        if ($error['issue'] === 'missing') {
            echo "❌ MISSING TABLE: {$error['migration']['basename']}\n";
            echo "   References table '{$error['dependency']}' which is never created\n";
            echo "   Table to create: {$error['migration']['table']}\n";
        } else {
            echo "❌ WRONG ORDER: {$error['migration']['basename']}\n";
            echo "   Creates: {$error['migration']['table']}\n";
            echo "   Depends on: {$error['dependency']}\n";
            echo "   But '{$error['dependency']}' is created AFTER in: {$error['depMigration']['basename']}\n";
            echo "   Current position: #{$error['index']} ({$error['migration']['timestamp']})\n";
            echo "   Dependency position: #{$error['depIndex']} ({$error['depMigration']['timestamp']})\n";
        }
        echo "\n";
    }
}

// Display duplicate timestamp details
if (!empty($duplicateTimestamps) && count($duplicateTimestamps) <= 10) {
    echo "=== DUPLICATE TIMESTAMP DETAILS ===\n\n";
    
    foreach ($duplicateTimestamps as $timestamp => $group) {
        echo "Timestamp: {$timestamp} (" . count($group) . " files)\n";
        foreach ($group as $migration) {
            echo "  - {$migration['name']}";
            if (!empty($migration['dependencies'])) {
                echo " (depends on: " . implode(', ', $migration['dependencies']) . ")";
            }
            echo "\n";
        }
        echo "\n";
    }
}

// Summary
echo "=== SUMMARY ===\n\n";
echo "Warnings: " . count($warnings) . "\n";
echo "Errors: " . count($orderingErrors) . "\n";

if (empty($orderingErrors)) {
    echo "\n✅ VALIDATION PASSED: All migrations are properly ordered!\n";
    echo "   Safe to run: php artisan migrate:fresh\n";
    exit(0);
} else {
    echo "\n❌ VALIDATION FAILED: Migration ordering issues detected!\n";
    echo "   Fix the errors above before running migrations.\n";
    exit(1);
}
