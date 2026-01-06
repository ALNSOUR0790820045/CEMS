<?php

namespace App\Console\Commands;

use App\Models\Backup;
use Illuminate\Console\Command;

class DatabaseBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--name= : Optional backup name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a database backup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup...');

        $name = $this->option('name') ?: 'database_' . now()->format('Y-m-d_H-i-s');
        
        // Sanitize name to prevent path traversal
        $name = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
        
        $filename = $name . '.sql';
        $backupPath = 'backups/database/' . $filename;
        $fullPath = storage_path('app/' . $backupPath);

        // Create backup record
        $backup = Backup::create([
            'name' => $name,
            'type' => 'database',
            'filename' => $filename,
            'path' => $backupPath,
            'status' => 'processing',
            'created_by' => null, // System-generated backup
        ]);

        try {
            // Get database configuration
            $dbConnection = config('database.default');
            $dbConfig = config("database.connections.{$dbConnection}");

            // Determine database type
            if ($dbConnection === 'pgsql') {
                // PostgreSQL backup
                $command = sprintf(
                    'PGPASSWORD=%s pg_dump -h %s -p %s -U %s %s > %s',
                    escapeshellarg($dbConfig['password']),
                    escapeshellarg($dbConfig['host']),
                    escapeshellarg($dbConfig['port']),
                    escapeshellarg($dbConfig['username']),
                    escapeshellarg($dbConfig['database']),
                    escapeshellarg($fullPath)
                );
            } elseif ($dbConnection === 'mysql') {
                // MySQL backup
                $command = sprintf(
                    'mysqldump -h %s -P %s -u %s -p%s %s > %s',
                    escapeshellarg($dbConfig['host']),
                    escapeshellarg($dbConfig['port'] ?? 3306),
                    escapeshellarg($dbConfig['username']),
                    escapeshellarg($dbConfig['password']),
                    escapeshellarg($dbConfig['database']),
                    escapeshellarg($fullPath)
                );
            } else {
                throw new \Exception("Unsupported database type: {$dbConnection}");
            }

            // Execute backup command
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                throw new \Exception('Database backup failed with exit code: ' . $returnVar);
            }

            // Get file size
            $size = file_exists($fullPath) ? filesize($fullPath) : 0;

            // Update backup record
            $backup->update([
                'status' => 'completed',
                'size' => $size,
                'completed_at' => now(),
            ]);

            $this->info("Database backup completed successfully!");
            $this->info("File: {$filename}");
            $this->info("Size: " . $backup->formatted_size);

            // Clean old backups (keep last 30)
            $this->cleanOldBackups();

        } catch (\Exception $e) {
            $backup->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            $this->error('Database backup failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Clean old backups, keeping only the last 30
     */
    protected function cleanOldBackups()
    {
        $oldBackups = Backup::where('type', 'database')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->skip(30)
            ->take(PHP_INT_MAX)
            ->get();

        foreach ($oldBackups as $backup) {
            $this->info("Removing old backup: {$backup->filename}");
            $backup->delete();
        }

        if ($oldBackups->count() > 0) {
            $this->info("Removed {$oldBackups->count()} old backup(s)");
        }
    }
}
