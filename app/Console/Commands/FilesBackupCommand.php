<?php

namespace App\Console\Commands;

use App\Models\Backup;
use Illuminate\Console\Command;
use ZipArchive;

class FilesBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:files {--name= : Optional backup name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of files (storage/app/public and public/uploads)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting files backup...');

        $name = $this->option('name') ?: 'files_' . now()->format('Y-m-d_H-i-s');
        
        // Sanitize name to prevent path traversal
        $name = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
        
        $filename = $name . '.zip';
        $backupPath = 'backups/files/' . $filename;
        $fullPath = storage_path('app/' . $backupPath);

        // Create backup record
        $backup = Backup::create([
            'name' => $name,
            'type' => 'files',
            'filename' => $filename,
            'path' => $backupPath,
            'status' => 'processing',
            'created_by' => null, // System-generated backup
        ]);

        try {
            // Create ZIP archive
            $zip = new ZipArchive();

            if ($zip->open($fullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('Failed to create ZIP archive');
            }

            // Add storage/app/public
            $storagePath = storage_path('app/public');
            if (is_dir($storagePath)) {
                $this->info('Adding storage/app/public...');
                $this->addDirectoryToZip($zip, $storagePath, 'storage/');
            }

            // Add public/uploads if exists
            $uploadsPath = public_path('uploads');
            if (is_dir($uploadsPath)) {
                $this->info('Adding public/uploads...');
                $this->addDirectoryToZip($zip, $uploadsPath, 'uploads/');
            }

            $zip->close();

            // Get file size
            $size = file_exists($fullPath) ? filesize($fullPath) : 0;

            // Update backup record
            $backup->update([
                'status' => 'completed',
                'size' => $size,
                'completed_at' => now(),
            ]);

            $this->info("Files backup completed successfully!");
            $this->info("File: {$filename}");
            $this->info("Size: " . $backup->formatted_size);

            // Clean old backups (keep last 10)
            $this->cleanOldBackups();

        } catch (\Exception $e) {
            $backup->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            $this->error('Files backup failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Add directory to ZIP archive recursively
     */
    protected function addDirectoryToZip(ZipArchive $zip, string $directory, string $localPrefix = '')
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $filePath = $file->getRealPath();
            
            // Safely calculate relative path
            if (strlen($filePath) > strlen($directory)) {
                $relativePath = $localPrefix . substr($filePath, strlen($directory) + 1);
            } else {
                $relativePath = $localPrefix . basename($filePath);
            }

            if ($file->isDir()) {
                $zip->addEmptyDir($relativePath);
            } else {
                $zip->addFile($filePath, $relativePath);
            }
        }
    }

    /**
     * Clean old backups, keeping only the last 10
     */
    protected function cleanOldBackups()
    {
        $oldBackups = Backup::where('type', 'files')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->skip(10)
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
