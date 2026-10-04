<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            if ($request->isAJAX()) {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
            }
            return redirect()->to(site_url('/login'))->with('error', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
