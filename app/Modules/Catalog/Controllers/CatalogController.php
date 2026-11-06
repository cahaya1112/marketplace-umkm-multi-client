<?php

namespace App\Modules\Catalog\Controllers;

use App\Modules\Catalog\Kategori;
use App\Modules\Catalog\Produk;
use App\Modules\Umkm\Umkm;
use Illuminate\Http\Request;

class CatalogController
{
    // FR-CUS-01 Lihat Katalog + FR-CUS-02 Pencarian dan Filter Produk
    public function index(Request $request)
    {
        $request->validate([
            'q'            => ['nullable', 'string', 'max:100'],
            'kategori'     => ['nullable', 'integer'],
            'umkm'         => ['nullable', 'integer'],
            'ketersediaan' => ['nullable', 'in:tersedia,habis'],
        ]);

        $produk = Produk::query()
            ->with(['umkm', 'kategori', 'stok'])
            ->tampilDiKatalog()
            ->cari($request->query('q'))
            ->when($request->filled('kategori'), fn ($q) => $q->where('id_kategori', $request->integer('kategori')))
            ->when($request->filled('umkm'), fn ($q) => $q->where('id_umkm', $request->integer('umkm')))
            ->when($request->query('ketersediaan') === 'tersedia', fn ($q) => $q->tersedia())
            ->when($request->query('ketersediaan') === 'habis', fn ($q) => $q->habis())
            ->orderByDesc('id_produk')
            ->paginate(12)
            ->withQueryString();

        $daftarKategori = Kategori::orderBy('nama')->get();

        return view('catalog::index', [
            'produk'         => $produk,
            'daftarKategori' => $daftarKategori,
            'kategori'       => $daftarKategori, // dipakai footer layouts.publik
            'daftarUmkm'     => Umkm::where('status', 'AKTIF')->orderBy('nama')->get(),
        ]);
    }

    // FR-CUS-01: detail produk + informasi toko pemilik
    public function show(int $id)
    {
        $produk = Produk::query()
            ->with(['umkm', 'kategori', 'stok'])
            ->tampilDiKatalog()
            ->findOrFail($id);

        return view('catalog::show', [
            'produk'   => $produk,
            'kategori' => Kategori::orderBy('nama')->get(), // dipakai footer layouts.publik
        ]);
    }
}