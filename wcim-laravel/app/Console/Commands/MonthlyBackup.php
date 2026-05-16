<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('backup:monthly')]
#[Description('Create a monthly backup of the SQLite database')]
class MonthlyBackup extends Command
{
    public function handle()
    {
        $backupDir = storage_path('backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filename = 'scheduled-backup-' . now()->format('Y-m-d') . '.sqlite';
        File::copy(database_path('database.sqlite'), $backupDir . '/' . $filename);

        $this->info('Monthly backup created: ' . $filename);
    }
}
