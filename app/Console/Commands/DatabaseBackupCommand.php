<?php

namespace App\Console\Commands;

use App\Models\Backup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        ]);

        try {
            // Get database configuration
            $dbConnection = config('database.default');
            $dbConfig = config("database.connections.{$dbConnection}");

            // Determine database type
            if ($dbConnection === 'pgsql') {
                // PostgreSQL backup
                $command = sprintf(
                    'PGPASSWORD="%s" pg_dump -h %s -p %s -U %s %s > %s',
                    $dbConfig['password'],
                    $dbConfig['host'],
                    $dbConfig['port'],
                    $dbConfig['username'],
                    $dbConfig['database'],
                    $fullPath
                );
            } elseif ($dbConnection === 'mysql') {
                // MySQL backup
                $command = sprintf(
                    'mysqldump -h %s -P %s -u %s -p"%s" %s > %s',
                    $dbConfig['host'],
                    $dbConfig['port'] ?? 3306,
                    $dbConfig['username'],
                    $dbConfig['password'],
                    $dbConfig['database'],
                    $fullPath
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
