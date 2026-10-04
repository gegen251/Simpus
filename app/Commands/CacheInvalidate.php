<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\CacheInvalidator;

class CacheInvalidate extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'cache:invalidate-katalog';
    protected $description = 'Menghapus seluruh cache data dan response katalog OPAC publik.';
    protected $usage       = 'cache:invalidate-katalog';

    public function run(array $params)
    {
        CLI::write('Menghapus cache katalog OPAC...', 'yellow');

        CacheInvalidator::invalidateKatalog();

        CLI::write('✅ Seluruh cache katalog berhasil di-invalidasi.', 'green');
    }
}
