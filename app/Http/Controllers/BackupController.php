<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    /**
     * Display a listing of backups with statistics
     */
    public function index()
    {
        $backups = Backup::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'database_count' => Backup::database()->completed()->count(),
            'files_count' => Backup::files()->completed()->count(),
            'total_count' => Backup::completed()->count(),
            'total_size' => Backup::completed()->sum('size'),
        ];

        return view('backups.index', compact('backups', 'stats'));
    }

    /**
     * Show the form for creating a new backup
     */
    public function create()
    {
        return view('backups.create');
    }

    /**
     * Store a newly created backup
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:database,files,full',
            'name' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9_\-]+$/',
        ]);

        $type = $request->input('type');
        $name = $request->input('name') ?: $type . '_' . now()->format('Y-m-d_H-i-s');

        try {
            // Run backup command in background
            switch ($type) {
                case 'database':
                    Artisan::call('backup:database', ['--name' => $name]);
                    break;
                case 'files':
                    Artisan::call('backup:files', ['--name' => $name]);
                    break;
                case 'full':
                    Artisan::call('backup:full', ['--name' => $name]);
                    break;
            }

            $message = 'تم بدء عملية النسخ الاحتياطي بنجاح';
            
            return redirect()->route('backups.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('backups.create')
                ->with('error', 'فشلت عملية النسخ الاحتياطي: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file
     */
    public function download(Backup $backup)
    {
        try {
            return $backup->download();
        } catch (\Exception $e) {
            return redirect()->route('backups.index')
                ->with('error', 'فشل تحميل الملف: ' . $e->getMessage());
        }
    }

    /**
     * Restore a database backup
     */
    public function restore(Request $request, Backup $backup)
    {
        if ($backup->type !== 'database') {
            return redirect()->route('backups.index')
                ->with('error', 'لا يمكن استرجاع إلا النسخ الاحتياطية لقاعدة البيانات');
        }

        if ($backup->status !== 'completed') {
            return redirect()->route('backups.index')
                ->with('error', 'لا يمكن استرجاع نسخة احتياطية غير مكتملة');
        }

        try {
            $fullPath = storage_path('app/' . $backup->path);

            if (!file_exists($fullPath)) {
                throw new \Exception('الملف غير موجود');
            }

            // Get database configuration
            $dbConnection = config('database.default');
            $dbConfig = config("database.connections.{$dbConnection}");

            // Determine database type and restore command
            if ($dbConnection === 'pgsql') {
                // PostgreSQL restore
                $command = sprintf(
                    'PGPASSWORD=%s psql -h %s -p %s -U %s %s < %s',
                    escapeshellarg($dbConfig['password']),
                    escapeshellarg($dbConfig['host']),
                    escapeshellarg($dbConfig['port']),
                    escapeshellarg($dbConfig['username']),
                    escapeshellarg($dbConfig['database']),
                    escapeshellarg($fullPath)
                );
            } elseif ($dbConnection === 'mysql') {
                // MySQL restore
                $command = sprintf(
                    'mysql -h %s -P %s -u %s -p%s %s < %s',
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

            // Execute restore command
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                throw new \Exception('فشلت عملية الاسترجاع');
            }

            return redirect()->route('backups.index')
                ->with('success', 'تم استرجاع النسخة الاحتياطية بنجاح');

        } catch (\Exception $e) {
            return redirect()->route('backups.index')
                ->with('error', 'فشلت عملية الاسترجاع: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified backup
     */
    public function destroy(Backup $backup)
    {
        try {
            $backup->delete();

            return redirect()->route('backups.index')
                ->with('success', 'تم حذف النسخة الاحتياطية بنجاح');

        } catch (\Exception $e) {
            return redirect()->route('backups.index')
                ->with('error', 'فشل حذف النسخة الاحتياطية: ' . $e->getMessage());
        }
    }
}
