<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DbBackup extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:backup';
    protected $description = 'Melakukan backup terjadwal database perpustakaan ke format .sql dengan rotasi berkas.';
    protected $usage       = 'db:backup [options]';
    protected $options     = [
        '--rotate' => 'Jumlah hari retensi berkas backup (default: 14 hari).',
        '--dest'   => 'Folder tujuan penyimpanan backup (default: ROOTPATH/backups).',
    ];

    public function run(array $params)
    {
        CLI::write('=== Memulai Proses Backup Database SIMPUS ===', 'yellow');

        $dbConfig = config('Database')->default;
        $host     = $dbConfig['hostname'] ?? '127.0.0.1';
        $port     = $dbConfig['port'] ?? 3307;
        $dbname   = $dbConfig['database'] ?? 'perpustakaan';
        $user     = $dbConfig['username'] ?? 'root';
        $password = $dbConfig['password'] ?? '';

        // Tentukan path tujuan
        $destDir = $params['dest'] ?? CLI::getOption('dest') ?? (ROOTPATH . 'backups');
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $timestamp = date('Y-m-d_His');
        $fileName  = "backup_{$dbname}_{$timestamp}.sql";
        $filePath  = $destDir . DIRECTORY_SEPARATOR . $fileName;

        // Cari executable mysqldump
        $mysqldumpPath = $this->findMysqldump();
        if (!$mysqldumpPath) {
            CLI::error('Executable mysqldump tidak ditemukan di sistem maupun instalasi XAMPP.');
            return;
        }

        CLI::write("Menggunakan mysqldump: {$mysqldumpPath}", 'light_gray');
        CLI::write("Target Database: {$dbname} ({$host}:{$port})", 'light_gray');
        CLI::write("File Output: {$filePath}", 'cyan');

        // Susun argumen mysqldump
        $pwdArg = !empty($password) ? "--password=" . escapeshellarg($password) : "";
        $cmd = sprintf(
            '%s --host=%s --port=%d --user=%s %s --single-transaction --quick --routines --triggers --default-character-set=utf8mb4 %s > %s',
            escapeshellarg($mysqldumpPath),
            escapeshellarg($host),
            (int)$port,
            escapeshellarg($user),
            $pwdArg,
            escapeshellarg($dbname),
            escapeshellarg($filePath)
        );

        exec($cmd, $output, $returnVar);

        if ($returnVar !== 0 || !file_exists($filePath) || filesize($filePath) === 0) {
            CLI::error("Gagal melakukan dump database. Return code: {$returnVar}");
            return;
        }

        $fileSizeKb = round(filesize($filePath) / 1024, 2);
        CLI::write("Backup berhasil dibuat! Ukuran berkas: {$fileSizeKb} KB", 'green');

        // Catat ke Audit Log jika tabel tersedia
        try {
            \App\Models\AuditLogModel::record(
                'BACKUP_DATABASE',
                "Pencadangan terjadwal database perpustakaan berhasil disimpan ke {$fileName} ({$fileSizeKb} KB).",
                null
            );
        } catch (\Throwable $e) {
            // Abaikan jika audit_log belum siap
        }

        // Rotasi Berkas Lama
        $rotateDays = (int)($params['rotate'] ?? CLI::getOption('rotate') ?? 14);
        $this->rotateBackups($destDir, $rotateDays);

        CLI::write('=== Selesai ===', 'green');
    }

    protected function findMysqldump(): ?string
    {
        $candidates = [
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\Program Files\\MariaDB\\bin\\mysqldump.exe',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
        ];

        foreach ($candidates as $c) {
            if (file_exists($c)) {
                return $c;
            }
        }

        // Cek PATH sistem
        $which = DIRECTORY_SEPARATOR === '\\' ? 'where mysqldump' : 'which mysqldump';
        $path = trim((string)shell_exec($which));
        if (!empty($path) && file_exists(strtok($path, "\r\n"))) {
            return strtok($path, "\r\n");
        }

        return null;
    }

    protected function rotateBackups(string $dir, int $days): void
    {
        if ($days <= 0) return;

        $cutoff = time() - ($days * 86400);
        $files = glob($dir . DIRECTORY_SEPARATOR . '*.sql');
        $deleted = 0;

        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $cutoff) {
                unlink($file);
                $deleted++;
            }
        }

        if ($deleted > 0) {
            CLI::write("Rotasi backup: {$deleted} berkas lama (> {$days} hari) berhasil dihapus.", 'yellow');
        }
    }
}
