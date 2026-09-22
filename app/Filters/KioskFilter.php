<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PengaturanModel;

class KioskFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $ip = $request->getIPAddress();

        // 1. Ambil pengaturan kiosk jika ada
        $allowedIpsConfig = '';
        $secretTokenConfig = 'kiosk-sdn12-official-token';

        try {
            $pengaturanModel = new PengaturanModel();
            $allowedIpsConfig = $pengaturanModel->getNilai('kiosk_allowed_ips', '');
            $secretTokenConfig = $pengaturanModel->getNilai('kiosk_secret_token', 'kiosk-sdn12-official-token');
        } catch (\Throwable $e) {
            // Fallback jika database belum termuat
        }

        // 2. Periksa Token Perangkat Kios (via Cookie, Header, atau Query Param)
        $tokenFromCookie = $request->getCookie('kiosk_device_token');
        $tokenFromHeader = $request->getHeaderLine('X-Kiosk-Token');
        $tokenFromQuery  = $request->getGet('kiosk_token');

        $providedToken = $tokenFromQuery ?: ($tokenFromHeader ?: $tokenFromCookie);

        if (!empty($providedToken) && hash_equals($secretTokenConfig, $providedToken)) {
            // Jika token dikirim lewat URL, pasang cookie untuk kemudahan request berikutnya
            if (!empty($tokenFromQuery)) {
                setcookie('kiosk_device_token', $providedToken, [
                    'expires'  => time() + (86400 * 365), // 1 tahun
                    'path'     => '/',
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            }
            return; // Diizinkan via token resmi
        }

        // 3. Periksa IP Allowlist
        // Default allowlist: localhost IPv4 & IPv6, serta private subnets (jaringan lokal sekolah)
        $allowedIps = ['127.0.0.1', '::1', 'localhost'];
        if (!empty($allowedIpsConfig)) {
            $customIps = array_map('trim', explode(',', $allowedIpsConfig));
            $allowedIps = array_merge($allowedIps, $customIps);
        }

        // Cek apakah IP tepat sama
        if (in_array($ip, $allowedIps, true)) {
            return; // Diizinkan via IP localhost/allowlist
        }

        // Cek apakah IP berada di subnet privat lokal (192.168.x.x, 10.x.x.x, 172.16.x.x)
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) === false) {
            // IP privat lokal diizinkan untuk terminal perpustakaan di lingkungan sekolah
            return;
        }

        // 4. Jika tidak lolos, tolak dengan HTTP 403 Forbidden
        if ($request->isAJAX() || $request->getHeaderLine('Accept') === 'application/json') {
            return service('response')
                ->setStatusCode(403)
                ->setJSON([
                    'success' => false,
                    'message' => 'Akses ditolak (403): Perangkat atau jaringan Anda tidak terdaftar sebagai terminal Layanan Mandiri Kios resmi.'
                ]);
        }

        $html = '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>403 Akses Kios Ditolak</title>'
              . '<style>body{font-family:sans-serif;background:#0f172a;color:#f8fafc;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;}'
              . '.card{background:#1e293b;padding:2rem;border-radius:1rem;border:1px solid #334155;max-width:480px;text-align:center;box-shadow:0 10px 25px rgba(0,0,0,0.5);}'
              . 'h1{font-size:1.5rem;color:#f87171;margin-bottom:0.5rem;}p{font-size:0.875rem;color:#94a3b8;line-height:1.5;}'
              . '.badge{display:inline-block;padding:0.25rem 0.75rem;background:#334155;border-radius:9999px;font-size:0.75rem;font-family:monospace;color:#38bdf8;margin:1rem 0;}'
              . 'a{display:inline-block;margin-top:1rem;color:#38bdf8;text-decoration:none;font-size:0.875rem;font-weight:600;}</style></head>'
              . '<body><div class="card">'
              . '<h1>403 - Terminal Kios Tidak Diizinkan</h1>'
              . '<div class="badge">IP Anda: ' . esc($ip) . '</div>'
              . '<p>Layanan Mandiri Siswa (Kiosk) hanya dapat diakses melalui jaringan perpustakaan resmi atau perangkat yang telah dipasangi token otorisasi.</p>'
              . '<a href="' . site_url('/katalog') . '">&larr; Kembali ke Katalog Publik</a>'
              . '</div></body></html>';

        return service('response')->setStatusCode(403)->setBody($html);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
