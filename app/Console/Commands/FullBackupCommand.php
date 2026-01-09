<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FullBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:full {--name= : Optional backup name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a full backup (database + files)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting full backup...');
        $this->newLine();

        $baseName = $this->option('name') ?: 'full_' . now()->format('Y-m-d_H-i-s');

        // Run database backup
        $this->info('=== Database Backup ===');
        $databaseResult = $this->call('backup:database', [
            '--name' => $baseName . '_database',
        ]);

        $this->newLine();

        // Run files backup
        $this->info('=== Files Backup ===');
        $filesResult = $this->call('backup:files', [
            '--name' => $baseName . '_files',
        ]);

        $this->newLine();

        if ($databaseResult === 0 && $filesResult === 0) {
            $this->info('✓ Full backup completed successfully!');
            return 0;
        } else {
            $this->error('✗ Full backup completed with errors');
            return 1;
        }
    }
}
