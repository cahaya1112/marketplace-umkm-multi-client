<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class KatalogPublikController extends Controller
{
    public function __invoke()
    {
        $umkmAktif = fn () => DB::table('umkm')->where('status', 'AKTIF');

        $produkAktif = fn () => DB::table('produk')
            ->join('umkm', 'umkm.id_umkm', '=', 'produk.id_umkm')
            ->where('umkm.status', 'AKTIF');

        $stats = [
            ['icon' => 'store', 'value' => $umkmAktif()->count(), 'label' => 'UMKM Terdaftar'],
            ['icon' => 'box', 'value' => $produkAktif()->count(), 'label' => 'Produk Tersedia'],
            ['icon' => 'tag', 'value' => DB::table('kategori')->count(), 'label' => 'Kategori'],
            ['icon' => 'trend', 'value' => DB::table('pesanan')->count(), 'label' => 'Transaksi'],
        ];

        $kategori = DB::table('kategori')
            ->orderBy('nama')
            ->limit(6)
            ->get(['id_kategori', 'nama']);

        $produk = $produkAktif()
            ->orderByDesc('produk.id_produk')
            ->limit(8)
            ->get(['produk.id_produk', 'produk.nama', 'produk.harga_retail', 'produk.gambar'])
            ->map(function ($p) {
                $p->gambar = $this->urlGambar($p->gambar);

                return $p;
            });

        $umkm = $umkmAktif()
            ->orderByDesc('id_umkm')
            ->limit(3)
            ->get(['id_umkm', 'nama', 'deskripsi', 'alamat', 'gambar_logo'])
            ->map(function ($u) {
                $u->gambar_logo = $this->urlGambar($u->gambar_logo);

                return $u;
            });

        return view('pembeli.beranda', compact('stats', 'kategori', 'produk', 'umkm'));
    }

    // Path gambar di database diubah jadi URL. File disimpan di storage/app/public (butuh: php artisan storage:link).
    private function urlGambar(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }
}