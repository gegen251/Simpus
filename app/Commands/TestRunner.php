<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestRunner extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Testing';

    /**
     * The Command's name
     *
     * @var string
     */
    protected $name = 'test';

    /**
     * the Command's short description
     *
     * @var string
     */
    protected $description = 'Menjalankan rangkaian unit & feature test PHPUnit untuk SIMPUS.';

    /**
     * the Command's usage
     *
     * @var string
     */
    protected $usage = 'test [options] [filter]';

    /**
     * the Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'filter' => 'Filter nama test yang ingin dijalankan (opsional).',
    ];

    /**
     * the Command's Options
     *
     * @var array
     */
    protected $options = [
        '--filter'   => 'Filter spesifik method/class test',
        '--testdox'  => 'Tampilkan laporan test dalam format TestDox',
        '--coverage' => 'Aktifkan laporan code coverage (membutuhkan Xdebug/PCOV)',
    ];

    /**
     * Actually run the command.
     */
    public function run(array $params)
    {
        CLI::write('');
        CLI::write('╔══════════════════════════════════════════════════════════╗', 'cyan');
        CLI::write('║       🧪 SIMPUS — Automated Test Suite Runner            ║', 'cyan');
        CLI::write('╚══════════════════════════════════════════════════════════╝', 'cyan');
        CLI::write('');

        // Pastikan working directory kembali ke ROOTPATH (karena spark chdir ke FCPATH/public)
        chdir(ROOTPATH);

        // Tentukan PHP executable
        $phpBinary = 'C:\\xampp\\php\\php.exe';
        if (!is_file($phpBinary)) {
            $phpBinary = 'php';
        }

        // Tentukan path PHPUnit
        $phpunitBin = ROOTPATH . 'vendor/phpunit/phpunit/phpunit';
        if (!is_file($phpunitBin)) {
            CLI::error('PHPUnit executable tidak ditemukan di vendor/phpunit/phpunit/phpunit.');
            return EXIT_ERROR;
        }

        $configFile = is_file(ROOTPATH . 'phpunit.xml') ? ROOTPATH . 'phpunit.xml' : ROOTPATH . 'phpunit.dist.xml';

        // Bangun argumen command
        $cmdArgs = [
            escapeshellarg($phpBinary),
            escapeshellarg($phpunitBin),
            '-c',
            escapeshellarg($configFile),
        ];

        // Coverage flag check
        $options = CLI::getOptions();
        if (!isset($options['coverage'])) {
            $cmdArgs[] = '--no-coverage';
        }

        if (isset($options['testdox'])) {
            $cmdArgs[] = '--testdox';
        }

        // Filter argument
        $filter = $options['filter'] ?? $params[0] ?? null;
        if ($filter) {
            $cmdArgs[] = '--filter';
            $cmdArgs[] = escapeshellarg($filter);
            CLI::write("  🔍 Filter Aktif: {$filter}", 'yellow');
        }

        $fullCmd = implode(' ', $cmdArgs);
        CLI::write("  🚀 Menjalankan PHPUnit...", 'light_blue');
        CLI::write('');

        // Eksekusi secara passthrough agar output berwarna tetap muncul
        passthru($fullCmd, $exitCode);

        CLI::write('');
        if ($exitCode === 0) {
            CLI::write('  ✅ SELURUH PENGUJIAN BERJALAN HIJAU (ALL TESTS GREEN)', 'green');
        } else {
            CLI::write('  ❌ TERDAPAT KEGAGALAN DALAM PENGUJIAN (TESTS FAILED)', 'red');
        }
        CLI::write('');

        return $exitCode;
    }
}
