<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\AuditLogModel;

class Staf extends BaseController
{
    protected $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('q');
        $selectedRole = $this->request->getGet('role');
        $order = $this->request->getGet('order') ?? 'ASC';

        $builder = $this->adminModel;

        if (!empty($keyword)) {
            $builder = $builder->groupStart()
                               ->like('nama', $keyword)
                               ->orLike('username', $keyword)
                               ->orLike('email', $keyword)
                               ->groupEnd();
        }

        if (!empty($selectedRole) && in_array($selectedRole, ['admin', 'staf'])) {
            $builder = $builder->where('role', $selectedRole);
        }

        if ($order === 'DESC') {
            $builder = $builder->orderBy('id', 'DESC');
        } else {
            $builder = $builder->orderBy('nama', 'ASC');
        }

        $stafList = $builder->findAll();

        // Hitung statistik
        $totalSemua = $this->adminModel->countAllResults(false);
        $totalAdmin = $this->adminModel->where('role', 'admin')->countAllResults(false);
        $totalStaf  = $this->adminModel->where('role', 'staf')->countAllResults(false);

        $activeFilterCount = 0;
        if (!empty($keyword)) $activeFilterCount++;
        if (!empty($selectedRole)) $activeFilterCount++;

        $data = [
            'title'             => 'Manajemen Akun Staf Pustaka',
            'active_menu'       => 'staf',
            'stafList'          => $stafList,
            'keyword'           => $keyword,
            'selectedRole'      => $selectedRole,
            'order'             => $order,
            'activeFilterCount' => $activeFilterCount,
            'stats'             => [
                'total' => $totalSemua,
                'admin' => $totalAdmin,
                'staf'  => $totalStaf,
            ]
        ];

        return view('staf/index', $data);
    }

    public function store()
    {
        $rules = [
            'nama'     => 'required|min_length[3]|max_length[100]',
            'username' => 'required|min_length[4]|max_length[50]|is_unique[admin.username]',
            'email'    => 'required|valid_email|is_unique[admin.email]',
            'password' => 'required|min_length[8]',
            'role'     => 'required|in_list[admin,staf]',
        ];

        if (!$this->validate($rules)) {
            $errors = implode('<br>', $this->validator->getErrors());
            return redirect()->back()->withInput()->with('error', $errors);
        }

        $rawPassword = $this->request->getPost('password');
        $nama        = $this->request->getPost('nama');
        $username    = strtolower(trim($this->request->getPost('username')));
        $email       = strtolower(trim($this->request->getPost('email')));
        $role        = $this->request->getPost('role');

        $this->adminModel->save([
            'nama'     => $nama,
            'username' => $username,
            'email'    => $email,
            'password' => password_hash($rawPassword, PASSWORD_DEFAULT),
            'role'     => $role,
        ]);

        $roleText = $role === 'admin' ? 'Administrator' : 'Staf Pustaka';

        // Audit Log
        AuditLogModel::record(
            'TAMBAH_STAF',
            "Menambahkan akun {$roleText} baru: '{$nama}' (@{$username}, {$email})."
        );

        return redirect()->to(site_url('/staf'))->with('success', "Akun {$roleText} baru atas nama '{$nama}' berhasil dibuat.");
    }

    public function detail($id)
    {
        $staf = $this->adminModel->find($id);
        if (!$staf) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data staf tidak ditemukan.'])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => [
                'id'         => $staf['id'],
                'nama'       => $staf['nama'],
                'username'   => $staf['username'],
                'email'      => $staf['email'],
                'role'       => $staf['role'],
                'created_at' => $staf['created_at'],
                'updated_at' => $staf['updated_at'],
            ]
        ]);
    }

    public function update($id)
    {
        $staf = $this->adminModel->find($id);
        if (!$staf) {
            return redirect()->to(site_url('/staf'))->with('error', 'Data akun staf tidak ditemukan.');
        }

        $rules = [
            'nama'     => 'required|min_length[3]|max_length[100]',
            'username' => "required|min_length[4]|max_length[50]|is_unique[admin.username,id,{$id}]",
            'email'    => "required|valid_email|is_unique[admin.email,id,{$id}]",
            'role'     => 'required|in_list[admin,staf]',
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[8]';
        }

        if (!$this->validate($rules)) {
            $errors = implode('<br>', $this->validator->getErrors());
            return redirect()->back()->withInput()->with('error', $errors);
        }

        $newRole = $this->request->getPost('role');

        // Cegah admin tunggal mengubah perannya sendiri menjadi staf
        if ($staf['role'] === 'admin' && $newRole === 'staf') {
            $adminCount = $this->adminModel->where('role', 'admin')->countAllResults();
            if ($adminCount <= 1) {
                return redirect()->back()->with('error', 'Tidak dapat mengubah role: Sistem membutuhkan minimal satu Administrator Utama.');
            }
        }

        $dataUpdate = [
            'id'       => $id,
            'nama'     => $this->request->getPost('nama'),
            'username' => strtolower(trim($this->request->getPost('username'))),
            'email'    => strtolower(trim($this->request->getPost('email'))),
            'role'     => $newRole,
        ];

        if (!empty($password)) {
            $dataUpdate['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->adminModel->save($dataUpdate);

        // Audit Log: Catat perubahan role jika berbeda
        if ($staf['role'] !== $newRole) {
            AuditLogModel::record(
                'UBAH_ROLE',
                "Mengubah hak akses/role akun '{$staf['nama']}' (@{$staf['username']}) dari '{$staf['role']}' menjadi '{$newRole}'."
            );
        }

        AuditLogModel::record(
            'UBAH_STAF',
            "Memperbarui data profil akun '{$dataUpdate['nama']}' (@{$dataUpdate['username']})."
        );

        // Jika mengubah profil diri sendiri, perbarui session
        if ($id == session()->get('admin_id')) {
            session()->set([
                'admin_nama'     => $dataUpdate['nama'],
                'admin_username' => $dataUpdate['username'],
                'admin_email'    => $dataUpdate['email'],
                'admin_role'     => $dataUpdate['role'],
            ]);
        }

        return redirect()->to(site_url('/staf'))->with('success', "Data akun '{$dataUpdate['nama']}' berhasil diperbarui.");
    }

    public function resetPassword($id)
    {
        $staf = $this->adminModel->find($id);
        if (!$staf) {
            return redirect()->to(site_url('/staf'))->with('error', 'Data akun staf tidak ditemukan.');
        }

        $newPassword = $this->request->getPost('new_password') ?? $this->request->getPost('password_baru');
        if (empty($newPassword) || strlen($newPassword) < 8) {
            return redirect()->to(site_url('/staf'))->with('error', 'Password baru minimal 8 karakter.');
        }

        $this->adminModel->update($id, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);

        // Audit Log
        AuditLogModel::record(
            'RESET_PASSWORD',
            "Mereset kata sandi akun petugas '{$staf['nama']}' (@{$staf['username']})."
        );

        return redirect()->to(site_url('/staf'))->with('success', "Kata sandi untuk '{$staf['nama']}' berhasil direset.");
    }

    public function delete($id)
    {
        // Cegah hapus diri sendiri
        if ($id == session()->get('admin_id')) {
            return redirect()->to(site_url('/staf'))->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $staf = $this->adminModel->find($id);
        if (!$staf) {
            return redirect()->to(site_url('/staf'))->with('error', 'Data akun staf tidak ditemukan.');
        }

        // Cegah hapus admin jika hanya tersisa 1
        if ($staf['role'] === 'admin') {
            $adminCount = $this->adminModel->where('role', 'admin')->countAllResults();
            if ($adminCount <= 1) {
                return redirect()->to(site_url('/staf'))->with('error', 'Tidak dapat menghapus: Minimal harus ada 1 Administrator Utama di sistem.');
            }
        }

        // Cek riwayat pelayanan sirkulasi staf untuk mencegah Foreign Key constraint crash
        $peminjamanModel = new \App\Models\PeminjamanModel();
        $pengembalianModel = new \App\Models\PengembalianModel();
        $trxCount = $peminjamanModel->where('admin_id', $id)->countAllResults() + $pengembalianModel->where('admin_id', $id)->countAllResults();

        if ($trxCount > 0) {
            return redirect()->to(site_url('/staf'))->with('warning', "Akun '{$staf['nama']}' tercatat pernah melayani {$trxCount} transaksi sirkulasi. Akun tidak dapat dihapus permanen untuk menjaga jejak audit riwayat perpustakaan. Anda dapat mereset password akun ini.");
        }

        try {
            $this->adminModel->delete($id);

            // Audit Log
            AuditLogModel::record(
                'HAPUS_STAF',
                "Menghapus akun petugas '{$staf['nama']}' (@{$staf['username']}, role: {$staf['role']})."
            );

            return redirect()->to(site_url('/staf'))->with('success', "Akun '{$staf['nama']}' telah berhasil dihapus dari sistem.");
        } catch (\Exception $e) {
            return redirect()->to(site_url('/staf'))->with('error', 'Gagal menghapus staf karena akun masih terikat dengan data sistem.');
        }
    }
}
