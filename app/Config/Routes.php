<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Root redirect (Jika login ke dashboard, jika belum login ke Katalog OPAC terbuka)
$routes->get('/', function () {
    if (session()->get('logged_in')) {
        return redirect()->to(site_url('/dashboard'));
    }
    return redirect()->to(site_url('/katalog'));
});

// Katalog Publik (OPAC) - Terbuka untuk umum, siswa, dan guru tanpa login
$routes->get('/katalog', 'Katalog::index');
$routes->get('/katalog/detail/(:num)', 'Katalog::detail/$1');

// Layanan Mandiri Siswa (Self-Service Kiosk) - Diproteksi filter Kiosk Resmi
$routes->group('kiosk', ['filter' => 'kiosk'], static function ($routes) {
    $routes->get('/', 'Kiosk::index');
    $routes->post('cek-anggota', 'Kiosk::apiCekAnggota');
    $routes->post('cek-buku', 'Kiosk::apiCekBuku');
    $routes->post('pinjam', 'Kiosk::apiProsesPinjam');
    $routes->post('kembali', 'Kiosk::apiProsesKembali');
});

// Autentikasi
$routes->get('/login', 'Auth::login');
$routes->post('/login/process', 'Auth::loginProcess');
$routes->get('/logout', 'Auth::logout');

// Rute Terproteksi AuthFilter
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Dashboard
    $routes->get('/dashboard', 'Dashboard::index');

    // Manajemen Kategori Buku
    $routes->get('/kategori', 'Kategori::index');
    $routes->post('/kategori/store', 'Kategori::store');
    $routes->post('/kategori/update/(:num)', 'Kategori::update/$1');
    $routes->post('/kategori/delete/(:num)', 'Kategori::delete/$1');

    // Manajemen Koleksi Buku
    $routes->get('/buku', 'Buku::index');
    $routes->post('/buku/store', 'Buku::store');
    $routes->post('/buku/update/(:num)', 'Buku::update/$1');
    $routes->post('/buku/delete/(:num)', 'Buku::delete/$1');
    $routes->match(['get', 'post'], '/buku/cetak-label', 'Buku::cetakLabel');
    $routes->match(['get', 'post'], '/buku/cetak-label/(:num)', 'Buku::cetakLabel/$1');
    $routes->get('/buku/template-import', 'Buku::downloadTemplate');
    $routes->post('/buku/import', 'Buku::importCsv');

    // Manajemen Anggota
    $routes->get('/anggota', 'Anggota::index');
    $routes->post('/anggota/store', 'Anggota::store');
    $routes->post('/anggota/update/(:num)', 'Anggota::update/$1');
    $routes->post('/anggota/delete/(:num)', 'Anggota::delete/$1');
    $routes->get('/anggota/cetak-kartu', 'Anggota::cetakKartu');
    $routes->get('/anggota/cetak-kartu/(:num)', 'Anggota::cetakKartu/$1');
    $routes->get('/anggota/template-import', 'Anggota::downloadTemplate');
    $routes->post('/anggota/import', 'Anggota::importCsv');

    // Transaksi Peminjaman & Perpanjangan
    $routes->get('/peminjaman', 'Peminjaman::index');
    $routes->post('/peminjaman/store', 'Peminjaman::store');
    $routes->post('/peminjaman/perpanjang/(:num)', 'Peminjaman::perpanjang/$1');
    $routes->get('/peminjaman/detail/(:num)', 'Peminjaman::detail/$1');

    // Transaksi Pengembalian & Denda
    $routes->get('/pengembalian', 'Pengembalian::index');
    $routes->post('/pengembalian/save', 'Pengembalian::save');
    $routes->get('/pengembalian/riwayat', 'Pengembalian::riwayat');
    $routes->post('/pengembalian/lunasi-denda/(:num)', 'Pengembalian::lunasiDenda/$1');

    // Fase 3: Notifikasi WhatsApp & Pengingat Sirkulasi
    $routes->get('/notifikasi', 'Notifikasi::index');

    // Laporan & Export Multi-Format (PDF, Word, Excel)
    $routes->get('/laporan', 'Laporan::index');
    $routes->get('/laporan/semua', 'Laporan::semua');
    $routes->get('/laporan/peminjaman', 'Laporan::peminjaman');
    $routes->get('/laporan/pengembalian', 'Laporan::pengembalian');
    $routes->get('/laporan/buku', 'Laporan::buku');
    $routes->get('/laporan/pdf/(:segment)', 'Laporan::exportPdf/$1');
    $routes->get('/laporan/word/(:segment)', 'Laporan::exportWord/$1');
    $routes->get('/laporan/excel/(:segment)', 'Laporan::exportExcel/$1');

    // Modul Khusus Administrator Utama (Diproteksi filter role:admin)
    $routes->group('', ['filter' => 'role:admin'], static function ($routes) {
        // Pengaturan Sistem Perpustakaan
        $routes->get('/pengaturan', 'Pengaturan::index');
        $routes->post('/pengaturan/update', 'Pengaturan::update');

        // Manajemen Akun Staf Pustaka
        $routes->get('/staf', 'Staf::index');
        $routes->get('/staf/detail/(:num)', 'Staf::detail/$1');
        $routes->post('/staf/store', 'Staf::store');
        $routes->post('/staf/update/(:num)', 'Staf::update/$1');
        $routes->post('/staf/reset-password/(:num)', 'Staf::resetPassword/$1');
        $routes->post('/staf/delete/(:num)', 'Staf::delete/$1');

        // Audit Log & Jejak Keamanan Sistem
        $routes->get('/audit-log', 'AuditLog::index');
    });
});
