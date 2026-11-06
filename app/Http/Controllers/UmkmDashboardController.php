<?php

namespace App\Http\Controllers;

use App\Modules\Umkm\Umkm;
use Illuminate\Support\Facades\DB;

class UmkmDashboardController extends Controller
{
    public function __invoke(Umkm $umkm)
    {
        $id = $umkm->id_umkm;

        // ---- Produk dan stok ----
        $produk = DB::table('produk')
            ->leftJoin('stok', 'stok.id_produk', '=', 'produk.id_produk')
            ->where('produk.id_umkm', $id)
            ->selectRaw('COUNT(produk.id_produk) AS total, COALESCE(SUM(CASE WHEN COALESCE(stok.jumlah_stok, 0) > 0 THEN 1 ELSE 0 END), 0) AS tersedia')
            ->first();

        $totalProduk = (int) $produk->total;
        $stokTersedia = (int) $produk->tersedia;
        $stokHabis = $totalProduk - $stokTersedia;

        // ---- Bahan query (selalu dibatasi ke UMKM ini) ----
        $pesananQ = fn () => DB::table('sub_pesanan')
            ->join('pesanan', 'pesanan.id_pesanan', '=', 'sub_pesanan.id_pesanan')
            ->join('pengguna', 'pengguna.id_pengguna', '=', 'pesanan.id_pengguna')
            ->where('sub_pesanan.id_umkm', $id);

        $customQ = fn () => DB::table('permintaan_custom')
            ->join('produk', 'produk.id_produk', '=', 'permintaan_custom.id_produk')
            ->join('pengguna', 'pengguna.id_pengguna', '=', 'permintaan_custom.id_pengguna')
            ->where('produk.id_umkm', $id);

        $returQ = fn () => DB::table('retur')
            ->join('detail_pesanan', 'detail_pesanan.id_detail_pesanan', '=', 'retur.id_detail_pesanan')
            ->join('sub_pesanan', 'sub_pesanan.id_sub_pesanan', '=', 'detail_pesanan.id_sub_pesanan')
            ->join('pesanan', 'pesanan.id_pesanan', '=', 'sub_pesanan.id_pesanan')
            ->join('pengguna', 'pengguna.id_pengguna', '=', 'pesanan.id_pengguna')
            ->join('produk', 'produk.id_produk', '=', 'detail_pesanan.id_produk')
            ->where('sub_pesanan.id_umkm', $id);

        // ---- Angka ringkasan ----
        $pesananBaru = $pesananQ()->where('sub_pesanan.status_sub_pesanan', 'MENUNGGU_PEMBAYARAN')->count();
        $pesananDiproses = $pesananQ()->where('sub_pesanan.status_sub_pesanan', 'DIPROSES')->count();
        $customMenunggu = $customQ()->where('permintaan_custom.status_custom', 'PENDING')->count();
        $returBaru = $returQ()->where('retur.status_retur', 'DIAJUKAN')->count();

        $stats = [
            ['icon' => 'box', 'value' => $totalProduk, 'label' => 'Total Produk', 'warna' => 'bg-[#E4ECE4] text-forest'],
            ['icon' => 'layers', 'value' => $stokTersedia, 'label' => 'Stok Tersedia', 'warna' => 'bg-[#E4ECE4] text-forest'],
            ['icon' => 'box', 'value' => $stokHabis, 'label' => 'Stok Habis', 'warna' => 'bg-[#FBE6E1] text-[#E8694A]'],
            ['icon' => 'cart', 'value' => $pesananBaru, 'label' => 'Pesanan Baru', 'warna' => 'bg-[#DCE8F1] text-[#447A9C]'],
            ['icon' => 'trend', 'value' => $pesananDiproses, 'label' => 'Pesanan Diproses', 'warna' => 'bg-[#DCE8F1] text-[#447A9C]'],
            ['icon' => 'filepen', 'value' => $customMenunggu, 'label' => 'Custom Menunggu', 'warna' => 'bg-[#FBEFD5] text-tan'],
            ['icon' => 'undo', 'value' => $returBaru, 'label' => 'Retur Baru', 'warna' => 'bg-[#FBE6E1] text-[#E8694A]'],
        ];

        // ---- Daftar terbaru ----
        $pesananTerbaru = $pesananQ()
            ->orderByDesc('sub_pesanan.dibuat_pada')
            ->orderByDesc('sub_pesanan.id_sub_pesanan')
            ->limit(4)
            ->get(['pesanan.kode_pesanan', 'pengguna.nama as pembeli', 'sub_pesanan.dibuat_pada', 'sub_pesanan.status_sub_pesanan as status']);

        $customTerbaru = $customQ()
            ->orderByDesc('permintaan_custom.id_custom')
            ->limit(4)
            ->get(['produk.nama as produk', 'pengguna.nama as pembeli', 'permintaan_custom.status_custom as status']);

        $returTerbaru = $returQ()
            ->orderByDesc('retur.id_retur')
            ->limit(4)
            ->get(['retur.alasan', 'pengguna.nama as pembeli', 'retur.status_retur as status']);

        // ---- Notifikasi (diturunkan dari data nyata; belum ada tabel notifikasi) ----
        $notifikasi = collect();

        $pesananQ()
            ->orderByDesc('sub_pesanan.dibuat_pada')
            ->orderByDesc('sub_pesanan.id_sub_pesanan')
            ->limit(2)
            ->get(['pesanan.kode_pesanan', 'pengguna.nama as pembeli'])
            ->each(fn ($p) => $notifikasi->push([
                'judul' => 'Pesanan Baru',
                'isi' => "Pesanan baru {$p->kode_pesanan} dari {$p->pembeli}.",
            ]));

        $c = $customQ()
            ->where('permintaan_custom.status_custom', 'PENDING')
            ->orderByDesc('permintaan_custom.id_custom')
            ->first(['produk.nama as produk', 'pengguna.nama as pembeli']);
        if ($c) {
            $notifikasi->push([
                'judul' => 'Custom Baru',
                'isi' => "Permintaan custom baru dari {$c->pembeli} untuk {$c->produk}.",
            ]);
        }

        $r = $returQ()
            ->where('retur.status_retur', 'DIAJUKAN')
            ->orderByDesc('retur.id_retur')
            ->first(['produk.nama as produk', 'pengguna.nama as pembeli']);
        if ($r) {
            $notifikasi->push([
                'judul' => 'Retur Baru',
                'isi' => "Retur baru dari {$r->pembeli} untuk {$r->produk}.",
            ]);
        }

        // ---- Menu sidebar (selain Dashboard masih '#', halamannya belum dibuat) ----
        $menu = [
            ['label' => 'Dashboard', 'icon' => 'grid', 'href' => route('umkm.dashboard', ['umkm' => $id]), 'active' => true],
            ['label' => 'UMKM Saya', 'icon' => 'store', 'href' => route('kelola.toko.edit')],
            ['label' => 'Produk', 'icon' => 'box', 'href' => route('kelola.produk.index')],
            ['label' => 'Stok', 'icon' => 'layers', 'href' => route('kelola.stok.index')],
            ['label' => 'Pesanan', 'icon' => 'cart', 'href' => '#'],
            ['label' => 'Permintaan Custom', 'icon' => 'filepen', 'href' => route('kelola.custom.index')],
            ['label' => 'Metode Pengiriman', 'icon' => 'truck', 'href' => '#'],
            ['label' => 'Retur', 'icon' => 'undo', 'href' => '#'],
            ['label' => 'Pengembalian Dana', 'icon' => 'dollar', 'href' => '#'],
            ['label' => 'Notifikasi', 'icon' => 'bell', 'href' => '#', 'badge' => $notifikasi->count() ?: null],
            ['label' => 'Profil', 'icon' => 'user', 'href' => '#'],
        ];

        $brandSub = $umkm->nama;

        return view('umkm.dashboard', compact(
            'umkm', 'stats', 'pesananTerbaru', 'customTerbaru', 'returTerbaru', 'notifikasi', 'menu', 'brandSub'
        ));
    }
}