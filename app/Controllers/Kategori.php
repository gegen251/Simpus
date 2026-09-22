<?php

namespace App\Controllers;

use App\Models\KategoriModel;
use App\Models\BukuModel;

class Kategori extends BaseController
{
    protected $kategoriModel;
    protected $bukuModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
        $this->bukuModel     = new BukuModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('q');
        $order = $this->request->getGet('order') ?? 'ASC';

        $kategoriList = $this->kategoriModel->getKategoriWithCount($keyword, $order);

        $activeFilterCount = 0;
        if (!empty($keyword)) $activeFilterCount++;
        if ($order === 'DESC') $activeFilterCount++;

        $data = [
            'title'             => 'Kategori Buku',
            'active_menu'       => 'kategori',
            'kategori'          => $kategoriList,
            'keyword'           => $keyword,
            'order'             => $order,
            'activeFilterCount' => $activeFilterCount,
        ];

        return view('kategori/index', $data);
    }

    public function store()
    {
        $rules = [
            'nama_kategori' => 'required|min_length[3]|max_length[100]|is_unique[kategori_buku.nama_kategori]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors()['nama_kategori'] ?? 'Input kategori tidak valid.');
        }

        $this->kategoriModel->save([
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'keterangan'    => $this->request->getPost('keterangan'),
        ]);

        return redirect()->to(site_url('/kategori'))->with('success', 'Kategori buku berhasil ditambahkan.');
    }

    public function update($id)
    {
        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            return redirect()->to(site_url('/kategori'))->with('error', 'Kategori tidak ditemukan.');
        }

        $namaKategori = $this->request->getPost('nama_kategori');
        $ruleUnique = $namaKategori !== $kategori['nama_kategori'] ? '|is_unique[kategori_buku.nama_kategori]' : '';

        if (!$this->validate(['nama_kategori' => 'required|min_length[3]|max_length[100]' . $ruleUnique])) {
            return redirect()->back()->withInput()->with('error', 'Nama kategori tidak valid atau sudah ada.');
        }

        $this->kategoriModel->update($id, [
            'nama_kategori' => $namaKategori,
            'keterangan'    => $this->request->getPost('keterangan'),
        ]);

        return redirect()->to(site_url('/kategori'))->with('success', 'Kategori buku berhasil diperbarui.');
    }

    public function delete($id)
    {
        $bukuCount = $this->bukuModel->where('kategori_id', $id)->countAllResults();
        if ($bukuCount > 0) {
            return redirect()->to(site_url('/kategori'))->with('error', "Kategori tidak dapat dihapus karena masih memuat $bukuCount koleksi buku.");
        }

        $this->kategoriModel->delete($id);
        return redirect()->to(site_url('/kategori'))->with('success', 'Kategori berhasil dihapus.');
    }
}
