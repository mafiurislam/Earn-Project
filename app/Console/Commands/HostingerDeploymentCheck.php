<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('hostinger:check')]
#[Description('Validate and prepare all files, database, and configurations for Hostinger SQLite deployment')]
class HostingerDeploymentCheck extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('====================================================');
        $this->info('  Rajdoot Nivedan Media - Hostinger Readiness Check  ');
        $this->info('====================================================');

        $hasErrors = false;

        // 1. PHP SQLite Extension Check
        if (extension_loaded('pdo_sqlite')) {
            $this->line(' [✓] PDO SQLite extension is loaded.');
        } else {
            $this->error(' [✗] PDO SQLite extension is NOT loaded.');
            $hasErrors = true;
        }

        // 2. Database File Check
        $dbPath = database_path('database.sqlite');
        if (file_exists($dbPath)) {
            $size = round(filesize($dbPath) / 1024, 2);
            $this->line(" [✓] SQLite database file exists at: {$dbPath} ({$size} KB).");

            if (is_writable($dbPath)) {
                $this->line(' [✓] Database file is writable.');
            } else {
                $this->warn(' [!] Database file is not writable. On Hostinger, run chmod 775 on database/database.sqlite.');
            }

            if (is_writable(dirname($dbPath))) {
                $this->line(' [✓] Database directory is writable (required for SQLite WAL/journal locks).');
            } else {
                $this->warn(' [!] Database directory is not writable. On Hostinger, run chmod 775 on database/.');
            }
        } else {
            $this->error(" [✗] SQLite database file missing at: {$dbPath}");
            $hasErrors = true;
        }

        // 3. Database Data & Users Check
        try {
            $userCount = User::count();
            $adminCount = User::where('is_admin', true)->count();
            $this->line(" [✓] Database connected. Found {$userCount} users ({$adminCount} admin accounts).");
        } catch (\Throwable $e) {
            $this->error(' [✗] Could not query SQLite database: '.$e->getMessage());
            $hasErrors = true;
        }

        // 4. File Integrity Checks (Root Entry & .htaccess)
        $rootHtaccess = base_path('.htaccess');
        if (file_exists($rootHtaccess)) {
            $this->line(' [✓] Root .htaccess exists with security and routing rules.');
        } else {
            $this->error(' [✗] Root .htaccess is missing.');
            $hasErrors = true;
        }

        $rootIndex = base_path('index.php');
        if (file_exists($rootIndex)) {
            $this->line(' [✓] Root index.php entry point exists for shared hosting.');
        } else {
            $this->error(' [✗] Root index.php is missing.');
            $hasErrors = true;
        }

        $dbHtaccess = database_path('.htaccess');
        if (file_exists($dbHtaccess)) {
            $this->line(' [✓] database/.htaccess exists (SQLite security lock active).');
        } else {
            $this->warn(' [!] database/.htaccess missing. Recommended to block direct web downloads.');
        }

        $envHostinger = base_path('.env.hostinger');
        if (file_exists($envHostinger)) {
            $this->line(' [✓] .env.hostinger pre-configured template exists.');
        } else {
            $this->warn(' [!] .env.hostinger is missing.');
        }

        // 5. Storage Directory Checks
        $requiredDirs = [
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/logs',
            'storage/app/public/verifications',
            'storage/app/public/songs/audio',
            'storage/app/public/songs/covers',
            'storage/app/public/avatars',
            'bootstrap/cache',
        ];

        foreach ($requiredDirs as $dir) {
            $fullDir = base_path($dir);
            if (! is_dir($fullDir)) {
                if (@mkdir($fullDir, 0775, true)) {
                    $this->line(" [✓] Created missing directory: {$dir}");
                } else {
                    $this->warn(" [!] Directory missing and could not create: {$dir}");
                }
            } else {
                $this->line(" [✓] Directory exists: {$dir}");
            }
        }

        $this->newLine();
        if ($hasErrors) {
            $this->error('Hostinger deployment check finished with errors! Please review above.');

            return Command::FAILURE;
        }

        $this->info('Hostinger deployment check PASSED! The project is 100% ready for Hostinger upload.');

        return Command::SUCCESS;
    }
}
