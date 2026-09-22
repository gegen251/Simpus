<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // 1. Pastikan sudah login
        if (!$session->get('logged_in')) {
            if ($request->isAJAX()) {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
            }
            return redirect()->to(site_url('/login'))->with('error', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        }

        // 2. Jika ditentukan parameter role (mis. role:admin)
        if (!empty($arguments)) {
            $userRole = $session->get('admin_role') ?? 'staf';
            if (!in_array($userRole, $arguments, true)) {
                if ($request->isAJAX()) {
                    return service('response')
                        ->setStatusCode(403)
                        ->setJSON(['status' => 'error', 'message' => 'Akses ditolak: Anda tidak memiliki izin untuk tindakan ini.']);
                }
                return redirect()->to(site_url('/dashboard'))->with('error', 'Akses ditolak: Halaman ini hanya dapat diakses oleh Administrator Utama.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
