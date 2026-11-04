<?php

namespace App\Http\Controllers;

use App\Support\UrlGambar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KatalogPublikController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

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

        $produkQuery = $produkAktif()->orderByDesc('produk.id_produk');
        if ($q !== '') {
            $produkQuery->where('produk.nama', 'like', '%'.$q.'%');
        }

        $produk = $produkQuery
            ->limit(8)
            ->get(['produk.id_produk', 'produk.nama', 'produk.harga_retail', 'produk.gambar'])
            ->map(function ($p) {
                $p->gambar = UrlGambar::dari($p->gambar);

                return $p;
            });

        $umkm = $umkmAktif()
            ->orderByDesc('id_umkm')
            ->limit(3)
            ->get(['id_umkm', 'nama', 'deskripsi', 'alamat', 'gambar_logo'])
            ->map(function ($u) {
                $u->gambar_logo = UrlGambar::dari($u->gambar_logo);

                return $u;
            });

        $data = compact('stats', 'kategori', 'produk', 'umkm', 'q');

        // Pembeli yang sudah login melihat beranda versi pelanggan (gambar 1).
        $user = $request->user();
        if ($user && $user->isPembeli()) {
            $data['jumlahKeranjang'] = (int) DB::table('keranjang_item')
                ->join('keranjang', 'keranjang.id_keranjang', '=', 'keranjang_item.id_keranjang')
                ->where('keranjang.id_pengguna', $user->id_pengguna)
                ->sum('keranjang_item.jumlah');

            // Pesanan yang menunggu pembayaran atau sedang dikirim.
            $data['jumlahNotifikasi'] = DB::table('sub_pesanan')
                ->join('pesanan', 'pesanan.id_pesanan', '=', 'sub_pesanan.id_pesanan')
                ->where('pesanan.id_pengguna', $user->id_pengguna)
                ->whereIn('sub_pesanan.status_sub_pesanan', ['MENUNGGU_PEMBAYARAN', 'DIKIRIM'])
                ->count();

            return view('pembeli.beranda-login', $data);
        }

        return view('pembeli.beranda', $data);
    }
}