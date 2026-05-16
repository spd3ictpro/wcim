<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    private function getBackupDir()
    {
        $dir = storage_path('backups');
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        return $dir;
    }

    private function getPassword()
    {
        return env('BACKUP_PASSWORD', 'admin');
    }

    public function index(Request $request)
    {
        if (!session('backup_authenticated')) {
            return view('backup.login');
        }

        $dbPath = database_path('database.sqlite');
        $dbSize = File::exists($dbPath) ? File::size($dbPath) : 0;
        $dbSizeFormatted = $this->formatBytes($dbSize);

        $backupDir = $this->getBackupDir();
        $backups = collect(File::files($backupDir))
            ->sortByDesc(fn($f) => $f->getMTime())
            ->map(fn($f) => (object) [
                'name' => $f->getFilename(),
                'size' => $this->formatBytes($f->getSize()),
                'date' => date('d F Y, h:i A', $f->getMTime()),
            ]);

        $latestBackup = $backups->first()?->date ?? 'Never';

        return view('backup.index', compact('dbSizeFormatted', 'latestBackup', 'backups'));
    }

    public function authenticate(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required|string',
        ]);

        if ($validated['password'] === $this->getPassword()) {
            session(['backup_authenticated' => true]);
            return redirect()->route('backup.index');
        }

        return back()->with('error', 'Incorrect password.');
    }

    public function logout()
    {
        session()->forget('backup_authenticated');
        return redirect()->route('backup.index');
    }

    public function export()
    {
        $dbPath = database_path('database.sqlite');
        $backupDir = $this->getBackupDir();
        $filename = 'wcim-backup-' . now()->format('Y-m-d_His') . '.sqlite';
        $destPath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        if (File::exists($dbPath)) {
            File::copy($dbPath, $destPath);
        }

        return response()->download($destPath)->deleteFileAfterSend(false);
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'backup_file' => 'required|file|mimes:sqlite,db,application/octet-stream|max:10240',
        ]);

        $dbPath = database_path('database.sqlite');

        $file = $request->file('backup_file');

        DB::disconnect();

        $backupDir = $this->getBackupDir();
        $tempBackup = $backupDir . DIRECTORY_SEPARATOR . 'pre-import-backup-' . now()->format('Y-m-d_His') . '.sqlite';
        if (File::exists($dbPath)) {
            File::copy($dbPath, $tempBackup);
        }

        File::copy($file->getRealPath(), $dbPath);

        DB::purge('sqlite');

        return redirect()->route('backup.index')->with('success', 'Database restored successfully. A backup of your previous data was saved.');
    }

    public function download($filename)
    {
        $backupDir = $this->getBackupDir();
        $filePath = $backupDir . DIRECTORY_SEPARATOR . basename($filename);

        if (!File::exists($filePath)) {
            return redirect()->route('backup.index')->with('error', 'Backup file not found.');
        }

        return response()->download($filePath);
    }

    public function delete($filename)
    {
        $backupDir = $this->getBackupDir();
        $filePath = $backupDir . DIRECTORY_SEPARATOR . basename($filename);

        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        return redirect()->route('backup.index')->with('success', 'Backup deleted.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|different:current_password',
            'confirm_password' => 'required|string|same:new_password',
        ]);

        if ($validated['current_password'] !== $this->getPassword()) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $envPath = base_path('.env');
        if (File::exists($envPath)) {
            $content = File::get($envPath);
            if (str_contains($content, 'BACKUP_PASSWORD=')) {
                $content = preg_replace(
                    '/BACKUP_PASSWORD=.*/',
                    'BACKUP_PASSWORD=' . $validated['new_password'],
                    $content
                );
            } else {
                $content .= PHP_EOL . 'BACKUP_PASSWORD=' . $validated['new_password'] . PHP_EOL;
            }
            File::put($envPath, $content);
        }

        return redirect()->route('backup.index')->with('success', 'Password updated successfully.');
    }

    private function formatBytes($bytes)
    {
        if ($bytes < 1024) return round($bytes) . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}
