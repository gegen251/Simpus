<?php

namespace App\Controllers;

use App\Models\AdminModel;

class Auth extends BaseController
{
    protected $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
    }

    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to(site_url('/dashboard'));
        }

        return view('auth/login');
    }

    public function loginProcess()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Username dan password wajib diisi.');
        }

        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');
        $ip = $this->request->getIPAddress();

        $cache = service('cache');
        $lockKey = 'login_lockout_' . md5($ip . '_' . strtolower($username));
        $attemptKey = 'login_attempts_' . md5($ip . '_' . strtolower($username));

        // 1. Cek apakah IP/akun sedang dalam masa blokir (Lockout)
        $lockExpires = $cache->get($lockKey);
        if ($lockExpires && $lockExpires > time()) {
            $remainingMinutes = ceil(($lockExpires - time()) / 60);
            return redirect()->back()->withInput()->with(
                'error',
                "Terlalu banyak percobaan login yang gagal. Akun / perangkat Anda diblokir sementara. Silakan coba kembali dalam {$remainingMinutes} menit."
            );
        }

        // 2. Search by username or email
        $admin = $this->adminModel->groupStart()
                                  ->where('username', $username)
                                  ->orWhere('email', $username)
                                  ->groupEnd()
                                  ->first();

        // 3. Verifikasi password & hitung kegagalan
        if (!$admin || !password_verify($password, $admin['password'])) {
            $attempts = (int) $cache->get($attemptKey) + 1;
            $maxAttempts = 5;
            $lockoutDuration = 900; // 15 menit

            if ($attempts >= $maxAttempts) {
                // Kunci akun / IP selama 15 menit
                $cache->save($lockKey, time() + $lockoutDuration, $lockoutDuration);
                $cache->delete($attemptKey);

                // Catat ke log keamanan
                log_message('warning', "BRUTE-FORCE LOCKOUT: Percobaan login gagal {$maxAttempts}x untuk username '{$username}' dari IP {$ip}.");
                \App\Models\AuditLogModel::record(
                    'LOCKOUT_LOGIN',
                    "Percobaan login gagal {$maxAttempts}x berturut-turut untuk akun '{$username}'. Akses dikunci sementara selama 15 menit.",
                    $admin['id'] ?? null
                );

                return redirect()->back()->withInput()->with(
                    'error',
                    "Terlalu banyak percobaan login gagal (mencapai {$maxAttempts} kali). Akun / perangkat Anda diblokir sementara selama 15 menit."
                );
            }

            // Simpan sisa hitungan percobaan dengan TTL 15 menit
            $cache->save($attemptKey, $attempts, $lockoutDuration);
            $sisa = $maxAttempts - $attempts;

            return redirect()->back()->withInput()->with(
                'error',
                "Username atau password salah. Sisa kesempatan login: {$sisa} kali sebelum diblokir sementara."
            );
        }

        // 4. Jika login berhasil, bersihkan riwayat percobaan gagal
        $cache->delete($attemptKey);
        $cache->delete($lockKey);

        // Regenerasi ID Sesi untuk mencegah Session Fixation
        session()->regenerate(true);

        // Set session
        session()->set([
            'admin_id'       => $admin['id'],
            'admin_nama'     => $admin['nama'],
            'admin_username' => $admin['username'],
            'admin_email'    => $admin['email'],
            'admin_role'     => $admin['role'] ?? 'admin',
            'logged_in'      => true,
        ]);

        \App\Models\AuditLogModel::record(
            'LOGIN',
            "Pengguna {$admin['username']} (Peran: " . ($admin['role'] ?? 'admin') . ") berhasil masuk ke dalam sistem.",
            $admin['id']
        );

        return redirect()->to(site_url('/dashboard'))->with('success', 'Selamat datang kembali, ' . $admin['nama'] . '!');
    }

    public function logout()
    {
        $adminId = session()->get('admin_id');
        $adminUser = session()->get('admin_username') ?? 'Pengguna';
        if ($adminId) {
            \App\Models\AuditLogModel::record(
                'LOGOUT',
                "Pengguna {$adminUser} telah keluar (logout) dari sistem.",
                $adminId
            );
        }
        session()->destroy();
        return redirect()->to(site_url('/login'))->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
