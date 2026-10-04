<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * LogRotation — Spark Command for SIMPUS Log Management
 * =========================================================================
 * Manages log rotation, compression, and retention for the SIMPUS library
 * management system. Prevents the writable/logs/ directory from growing
 * without bound.
 * 
 * Usage:
 *   php spark logs:rotate              # Run with default 30-day retention
 *   php spark logs:rotate --days=14    # Custom retention period
 *   php spark logs:rotate --dry-run    # Preview actions without executing
 *   php spark logs:rotate --verbose    # Show detailed file information
 * 
 * Policy:
 *   - Log files older than 7 days: compressed (gzip)
 *   - Log files older than 30 days (configurable): deleted
 *   - Compressed archives (.gz) older than retention days: deleted
 * =========================================================================
 */
class LogRotation extends BaseCommand
{
    /**
     * The group the command is lumped under when listing commands.
     */
    protected $group = 'Maintenance';

    /**
     * The command's name.
     */
    protected $name = 'logs:rotate';

    /**
     * The command's short description.
     */
    protected $description = 'Rotasi dan pembersihan log aplikasi SIMPUS (kompres log lama, hapus log expired).';

    /**
     * The command's usage string.
     */
    protected $usage = 'logs:rotate [--days=30] [--compress-after=7] [--dry-run] [--verbose]';

    /**
     * The command's options.
     */
    protected $options = [
        '--days'           => 'Jumlah hari retensi log sebelum dihapus (default: 30)',
        '--compress-after' => 'Jumlah hari sebelum log dikompres/gzip (default: 7)',
        '--dry-run'        => 'Preview tindakan tanpa mengeksekusi',
        '--verbose'        => 'Tampilkan informasi detail setiap file',
    ];

    /**
     * Execute the log rotation command.
     */
    public function run(array $params)
    {
        // Support both --key=val and --key val formats
        $parsed = [];
        foreach ($params as $k => $v) {
            if (is_string($k) && str_contains($k, '=')) {
                [$key, $val] = explode('=', $k, 2);
                $parsed[$key] = $val;
            } else {
                $parsed[$k] = $v;
            }
        }
        foreach (CLI::getOptions() as $k => $v) {
            if (str_contains($k, '=')) {
                [$key, $val] = explode('=', $k, 2);
                $parsed[$key] = $val;
            } elseif (!isset($parsed[$k])) {
                $parsed[$k] = $v;
            }
        }

        $retentionDays  = (int)($parsed['days'] ?? 30);
        $compressAfter  = (int)($parsed['compress-after'] ?? $parsed['compress_after'] ?? 7);
        $dryRun         = array_key_exists('dry-run', $parsed) || array_key_exists('dry_run', $parsed);
        $verbose        = array_key_exists('verbose', $parsed) || array_key_exists('v', $parsed);

        $logPath = WRITEPATH . 'logs/';

        CLI::write('');
        CLI::write('╔══════════════════════════════════════════════════════════╗', 'cyan');
        CLI::write('║    🗂️  SIMPUS — Rotasi & Pembersihan Log Aplikasi       ║', 'cyan');
        CLI::write('╚══════════════════════════════════════════════════════════╝', 'cyan');
        CLI::write('');

        if ($dryRun) {
            CLI::write('  ⚠️  MODE DRY-RUN — Tidak ada perubahan yang akan dilakukan.', 'yellow');
            CLI::write('');
        }

        CLI::write("  📁 Direktori Log  : {$logPath}", 'white');
        CLI::write("  📅 Retensi        : {$retentionDays} hari", 'white');
        CLI::write("  🗜️  Kompres Setelah: {$compressAfter} hari", 'white');
        CLI::write('');

        if (!is_dir($logPath)) {
            CLI::write('  ❌ Direktori log tidak ditemukan!', 'red');
            return;
        }

        $now = time();
        $compressThreshold = $now - ($compressAfter * 86400);
        $deleteThreshold   = $now - ($retentionDays * 86400);

        $files = glob($logPath . '*');
        if (empty($files)) {
            CLI::write('  ℹ️  Tidak ada file log ditemukan.', 'light_blue');
            return;
        }

        $stats = [
            'total_files'      => 0,
            'compressed'       => 0,
            'deleted'          => 0,
            'skipped'          => 0,
            'bytes_freed'      => 0,
            'bytes_compressed' => 0,
            'errors'           => 0,
        ];

        // Pre-scan: Calculate total size
        $totalSizeBefore = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                $totalSizeBefore += filesize($file);
            }
        }

        CLI::write("  📊 Total file     : " . count($files), 'white');
        CLI::write("  💾 Total ukuran   : " . $this->formatBytes($totalSizeBefore), 'white');
        CLI::write('');
        CLI::write('  ── Proses Rotasi ──────────────────────────────────────', 'dark_gray');
        CLI::write('');

        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            $basename = basename($file);
            $stats['total_files']++;

            // Skip non-log files (index.html, .gitkeep, etc.)
            if (!preg_match('/^log-\d{4}-\d{2}-\d{2}\.log(\.gz)?$/', $basename) && 
                !preg_match('/\.log(\.gz)?$/', $basename)) {
                $stats['skipped']++;
                if ($verbose) {
                    CLI::write("  ⏭️  Skip: {$basename} (bukan file log)", 'dark_gray');
                }
                continue;
            }

            if (preg_match('/^log-(\d{4}-\d{2}-\d{2})\.log/', $basename, $matches)) {
                $fileTime = strtotime($matches[1] . ' 23:59:59');
            } else {
                $fileTime = filemtime($file);
            }
            $fileSize = filesize($file);
            $fileAge  = (int)(($now - $fileTime) / 86400);

            // === PHASE 1: DELETE old files (log and gz) ===
            if ($fileTime < $deleteThreshold) {
                if ($verbose || $dryRun) {
                    CLI::write("  🗑️  Hapus: {$basename} ({$this->formatBytes($fileSize)}, umur {$fileAge} hari)", 'red');
                }
                if (!$dryRun) {
                    if (@unlink($file)) {
                        $stats['deleted']++;
                        $stats['bytes_freed'] += $fileSize;
                    } else {
                        $stats['errors']++;
                        CLI::write("  ❌ Gagal hapus: {$basename}", 'red');
                    }
                } else {
                    $stats['deleted']++;
                    $stats['bytes_freed'] += $fileSize;
                }
                continue;
            }

            // === PHASE 2: COMPRESS files older than compress threshold ===
            if ($fileTime < $compressThreshold && !str_ends_with($basename, '.gz')) {
                $gzPath = $file . '.gz';
                if ($verbose || $dryRun) {
                    CLI::write("  🗜️  Kompres: {$basename} ({$this->formatBytes($fileSize)}, umur {$fileAge} hari)", 'yellow');
                }
                if (!$dryRun) {
                    if ($this->compressFile($file, $gzPath)) {
                        $gzSize = filesize($gzPath);
                        $savings = $fileSize - $gzSize;
                        $stats['compressed']++;
                        $stats['bytes_compressed'] += $savings;
                        if ($verbose) {
                            $ratio = $fileSize > 0 ? round(($savings / $fileSize) * 100) : 0;
                            CLI::write("     → {$this->formatBytes($fileSize)} → {$this->formatBytes($gzSize)} (hemat {$ratio}%)", 'dark_gray');
                        }
                    } else {
                        $stats['errors']++;
                        CLI::write("  ❌ Gagal kompres: {$basename}", 'red');
                    }
                } else {
                    $stats['compressed']++;
                }
                continue;
            }

            // File masih baru — skip
            $stats['skipped']++;
            if ($verbose) {
                CLI::write("  ✅ Aktif: {$basename} ({$this->formatBytes($fileSize)}, umur {$fileAge} hari)", 'green');
            }
        }

        // Post-scan: Calculate total size after
        $totalSizeAfter = 0;
        $remainingFiles = glob($logPath . '*');
        if ($remainingFiles) {
            foreach ($remainingFiles as $file) {
                if (is_file($file)) {
                    $totalSizeAfter += filesize($file);
                }
            }
        }

        // === SUMMARY ===
        CLI::write('');
        CLI::write('  ── Ringkasan ──────────────────────────────────────────', 'dark_gray');
        CLI::write('');
        CLI::write("  📁 Total file diproses : {$stats['total_files']}", 'white');
        CLI::write("  🗜️  File dikompres      : {$stats['compressed']}", $stats['compressed'] > 0 ? 'yellow' : 'white');
        CLI::write("  🗑️  File dihapus        : {$stats['deleted']}", $stats['deleted'] > 0 ? 'red' : 'white');
        CLI::write("  ⏭️  File dilewati       : {$stats['skipped']}", 'dark_gray');
        
        if ($stats['errors'] > 0) {
            CLI::write("  ❌ Error               : {$stats['errors']}", 'red');
        }

        if (!$dryRun) {
            $freed = $stats['bytes_freed'] + $stats['bytes_compressed'];
            CLI::write('');
            CLI::write("  💾 Ukuran sebelum : {$this->formatBytes($totalSizeBefore)}", 'white');
            CLI::write("  💾 Ukuran setelah : {$this->formatBytes($totalSizeAfter)}", 'white');
            CLI::write("  🎉 Space hemat   : {$this->formatBytes($freed)}", 'green');
        }

        CLI::write('');

        if ($dryRun) {
            CLI::write('  ℹ️  Jalankan tanpa --dry-run untuk mengeksekusi.', 'light_blue');
            CLI::write('');
        }

        // Log the rotation action
        if (!$dryRun && ($stats['compressed'] > 0 || $stats['deleted'] > 0)) {
            log_message('info', "[LogRotation] Rotasi selesai: {$stats['compressed']} dikompres, {$stats['deleted']} dihapus, {$this->formatBytes($stats['bytes_freed'] + $stats['bytes_compressed'])} dihemat.");
        }
    }

    /**
     * Compress a file using gzip and remove the original.
     */
    private function compressFile(string $source, string $destination): bool
    {
        try {
            $data = file_get_contents($source);
            if ($data === false) {
                return false;
            }

            $compressed = gzencode($data, 9);
            if ($compressed === false) {
                return false;
            }

            if (file_put_contents($destination, $compressed) === false) {
                return false;
            }

            // Remove original only after successful compression
            @unlink($source);
            return true;
        } catch (\Throwable $e) {
            // If compression fails, clean up partial gz file
            if (file_exists($destination)) {
                @unlink($destination);
            }
            return false;
        }
    }

    /**
     * Format bytes into human-readable string.
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        $value = (float)$bytes;
        while ($value >= 1024 && $i < count($units) - 1) {
            $value /= 1024;
            $i++;
        }
        return round($value, 2) . ' ' . $units[$i];
    }
}
