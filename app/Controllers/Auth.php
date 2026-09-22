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

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Search by username or email
        $admin = $this->adminModel->groupStart()
                                  ->where('username', $username)
                                  ->orWhere('email', $username)
                                  ->groupEnd()
                                  ->first();

        if (!$admin || !password_verify($password, $admin['password'])) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

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

        return redirect()->to(site_url('/dashboard'))->with('success', 'Selamat datang kembali, ' . $admin['nama'] . '!');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('/login'))->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
