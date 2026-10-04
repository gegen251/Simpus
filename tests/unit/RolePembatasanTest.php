<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Filters\RoleFilter;
use App\Filters\AuthFilter;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\URI;
use CodeIgniter\HTTP\UserAgent;
use Config\App;

/**
 * RolePembatasanTest
 * =========================================================================
 * Menguji logika otentikasi dan otorisasi berjenjang (RBAC):
 * 1. Tamu (Unauthenticated) dicegat dan diarahkan ke login saat mengakses rute privat.
 * 2. Petugas/Staf ditolak saat mengakses rute khusus Admin Utama (/staf, /pengaturan).
 * 3. Petugas/Staf diizinkan mengakses rute operasional (/buku, /anggota, /peminjaman).
 * 4. Administrator memiliki izin ke seluruh rute sistem.
 * 5. Tamu dengan AJAX menerima HTTP 401, Petugas yang melanggar batas menerima HTTP 403.
 * =========================================================================
 */
final class RolePembatasanTest extends CIUnitTestCase
{
    private RoleFilter $roleFilter;
    private AuthFilter $authFilter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->roleFilter = new RoleFilter();
        $this->authFilter = new AuthFilter();

        // Bersihkan sesi sebelum setiap test
        $_SESSION = [];
    }

    /**
     * Helper membuat request mock
     */
    private function createMockRequest(bool $isAjax = false): IncomingRequest
    {
        $config = new App();
        $uri    = new URI('http://localhost:8080/dashboard');
        $agent  = new UserAgent();

        $request = new IncomingRequest($config, $uri, '', $agent);

        if ($isAjax) {
            $request->setHeader('X-Requested-With', 'XMLHttpRequest');
        }

        return $request;
    }

    /**
     * Test: Tamu unauthenticated dicegat oleh AuthFilter dan diarahkan ke /login
     */
    public function testTamuDicegatAuthFilter(): void
    {
        $request = $this->createMockRequest(false);

        // Session kosong (belum login)
        $response = $this->authFilter->before($request);

        $this->assertNotNull($response, 'Tamu tanpa login harus dicegat');
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $response);
        $this->assertStringContainsString('login', $response->getHeaderLine('Location'));
    }

    /**
     * Test: Tamu via AJAX dicegat dengan status HTTP 401
     */
    public function testTamuAjaxDicegatDenganHttp401(): void
    {
        $request = $this->createMockRequest(true);

        $response = $this->authFilter->before($request);

        $this->assertNotNull($response);
        $this->assertSame(401, $response->getStatusCode());
    }

    /**
     * Test: Staf ditolak saat mencoba mengakses fitur khusus Admin (role:admin)
     */
    public function testStafDitolakPadaFiturKhususAdmin(): void
    {
        // Set sesi sebagai Petugas / Staf
        $_SESSION['logged_in']  = true;
        $_SESSION['admin_id']   = 2;
        $_SESSION['admin_user'] = 'petugas1';
        $_SESSION['admin_role'] = 'staf';

        $request = $this->createMockRequest(false);

        // RoleFilter dengan argumen ['admin']
        $response = $this->roleFilter->before($request, ['admin']);

        $this->assertNotNull($response, 'Staf harus ditolak dari rute khusus admin');
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $response);
        $this->assertStringContainsString('dashboard', $response->getHeaderLine('Location'));
    }

    /**
     * Test: Staf via AJAX ditolak dengan HTTP 403 Forbidden
     */
    public function testStafAjaxDitolakDenganHttp403(): void
    {
        $_SESSION['logged_in']  = true;
        $_SESSION['admin_role'] = 'staf';

        $request = $this->createMockRequest(true);

        $response = $this->roleFilter->before($request, ['admin']);

        $this->assertNotNull($response);
        $this->assertSame(403, $response->getStatusCode());
    }

    /**
     * Test: Staf diizinkan mengakses fitur operasional (role:admin, staf)
     */
    public function testStafDiizinkanPadaFiturOperasional(): void
    {
        $_SESSION['logged_in']  = true;
        $_SESSION['admin_role'] = 'staf';

        $request = $this->createMockRequest(false);

        // Fitur yang mengizinkan admin dan staf
        $response = $this->roleFilter->before($request, ['admin', 'staf']);

        $this->assertNull($response, 'Staf harus diizinkan lewat tanpa redirect');
    }

    /**
     * Test: Administrator diizinkan mengakses seluruh level fitur
     */
    public function testAdminDiizinkanMengaksesSemuaFitur(): void
    {
        $_SESSION['logged_in']  = true;
        $_SESSION['admin_role'] = 'admin';

        $request = $this->createMockRequest(false);

        // 1. Cek rute khusus admin
        $resAdmin = $this->roleFilter->before($request, ['admin']);
        $this->assertNull($resAdmin, 'Admin harus diizinkan pada rute khusus admin');

        // 2. Cek rute umum operasional
        $resGeneral = $this->roleFilter->before($request, ['admin', 'staf']);
        $this->assertNull($resGeneral, 'Admin harus diizinkan pada rute operasional');
    }

    /**
     * Test: RoleFilter mencegat pengguna yang sesi logged_in bernilai false
     */
    public function testRoleFilterMencegatSesiPalsu(): void
    {
        $_SESSION['logged_in']  = false;
        $_SESSION['admin_role'] = 'admin'; // Role terisi tapi logged_in false

        $request = $this->createMockRequest(false);

        $response = $this->roleFilter->before($request, ['admin']);

        $this->assertNotNull($response);
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $response);
        $this->assertStringContainsString('login', $response->getHeaderLine('Location'));
    }
}
