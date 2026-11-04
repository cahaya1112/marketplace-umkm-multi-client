<?php

namespace App\Http\Controllers;

use App\Support\UrlGambar;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    // Sub-pesanan yang dianggap sudah dibayar (dihitung sebagai penjualan).
    private const STATUS_DIBAYAR = ['DIPROSES', 'DIKIRIM', 'SELESAI'];

    public function __invoke()
    {
        $terjual = (int) DB::table('detail_pesanan')
            ->join('sub_pesanan', 'sub_pesanan.id_sub_pesanan', '=', 'detail_pesanan.id_sub_pesanan')
            ->whereIn('sub_pesanan.status_sub_pesanan', self::STATUS_DIBAYAR)
            ->sum('detail_pesanan.jumlah');

        $stats = [
            ['icon' => 'store', 'value' => DB::table('umkm')->count(), 'label' => 'Total UMKM', 'warna' => 'bg-[#E4ECE4] text-forest'],
            ['icon' => 'box', 'value' => DB::table('produk')->count(), 'label' => 'Total Produk', 'warna' => 'bg-[#F6EADB] text-tan'],
            ['icon' => 'trend', 'value' => $terjual, 'label' => 'Total Produk Terjual', 'warna' => 'bg-[#E4ECE4] text-forest'],
            ['icon' => 'tag', 'value' => DB::table('kategori')->count(), 'label' => 'Total Kategori', 'warna' => 'bg-[#DCE8F1] text-[#447A9C]'],
        ];

        // ---- Grafik penjualan per UMKM ----
        $penjualan = DB::table('umkm')
            ->leftJoin('sub_pesanan', function ($join) {
                $join->on('sub_pesanan.id_umkm', '=', 'umkm.id_umkm')
                    ->whereIn('sub_pesanan.status_sub_pesanan', self::STATUS_DIBAYAR);
            })
            ->leftJoin('detail_pesanan', 'detail_pesanan.id_sub_pesanan', '=', 'sub_pesanan.id_sub_pesanan')
            ->selectRaw('umkm.id_umkm, umkm.nama, COALESCE(SUM(detail_pesanan.jumlah * COALESCE(detail_pesanan.harga_custom, detail_pesanan.harga_retail)), 0) AS total')
            ->groupBy('umkm.id_umkm', 'umkm.nama')
            ->orderBy('umkm.id_umkm')
            ->get();

        $langkah = $this->langkah((float) $penjualan->max('total'));
        $puncak = $langkah * 4;

        $ticks = [];
        for ($i = 0; $i <= 4; $i++) {
            $ticks[] = $this->ringkas($langkah * $i);
        }

        $grafik = $penjualan->map(fn ($r) => [
            'nama' => $r->nama,
            'total' => (int) $r->total,
            'persen' => $puncak > 0 ? round($r->total / $puncak * 100, 1) : 0,
        ]);

        // ---- UMKM terbaru ----
        $umkmTerbaru = DB::table('umkm')
            ->orderByDesc('id_umkm')
            ->limit(5)
            ->get(['id_umkm', 'nama', 'alamat', 'gambar_logo', 'status'])
            ->map(function ($u) {
                $u->gambar_logo = UrlGambar::dari($u->gambar_logo);

                return $u;
            });

        $nonaktif = DB::table('umkm')->where('status', 'NONAKTIF')->count();

        $menu = [
            ['label' => 'Dashboard', 'icon' => 'grid', 'href' => route('admin.dashboard'), 'active' => true],
            ['label' => 'Pengguna Admin UMKM', 'icon' => 'users', 'href' => '#'],
            ['label' => 'UMKM', 'icon' => 'store', 'href' => '#'],
            ['label' => 'Kategori', 'icon' => 'tag', 'href' => '#'],
            ['label' => 'Monitoring Produk', 'icon' => 'box', 'href' => '#'],
            ['label' => 'Laporan Penjualan', 'icon' => 'chart', 'href' => '#'],
            ['label' => 'Notifikasi', 'icon' => 'bell', 'href' => '#', 'badge' => $nonaktif ?: null],
            ['label' => 'Profil', 'icon' => 'user', 'href' => '#'],
        ];

        $brandSub = 'Super Admin';

        return view('admin.dashboard', compact('stats', 'grafik', 'ticks', 'umkmTerbaru', 'menu', 'brandSub'));
    }

    // Selisih antar garis grafik (kelipatan 1, 2, 5 x 10^n) agar 4 garis cukup menampung nilai tertinggi.
    private function langkah(float $maks): int
    {
        if ($maks <= 0) {
            return 50000;
        }

        $mentah = $maks / 4;
        $pangkat = 10 ** floor(log10($mentah));
        $n = $mentah / $pangkat;
        $bagus = $n <= 1 ? 1 : ($n <= 2 ? 2 : ($n <= 5 ? 5 : 10));

        return (int) max(1, $bagus * $pangkat);
    }

    // 800000 -> "800k", 1500000 -> "1,5jt"
    private function ringkas(int $n): string
    {
        if ($n >= 1000000) {
            return rtrim(rtrim(number_format($n / 1000000, 1, ',', '.'), '0'), ',').'jt';
        }

        if ($n >= 1000) {
            return round($n / 1000).'k';
        }

        return (string) $n;
    }
}