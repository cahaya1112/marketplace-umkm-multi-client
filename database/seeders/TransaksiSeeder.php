<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        // Jalankan sekali saja (hindari data ganda).
        if (DB::table('pesanan')->exists()) {
            return;
        }

        $pembeli = DB::table('pengguna')->where('email', 'customer@marketplace.com')->first();
        $umkm = DB::table('umkm')->orderBy('id_umkm')->get();

        if (! $pembeli || $umkm->isEmpty()) {
            return;
        }

        $idAlamat = DB::table('alamat_pembeli')->insertGetId([
            'id_pengguna' => $pembeli->id_pengguna,
            'nama_penerima' => $pembeli->nama,
            'nomor_telepon' => '081200000009',
            'alamat_lengkap' => 'Jl. Contoh No. 1, Banyuwangi',
            'id_wilayah_tujuan' => '3510',
            'kode_pos' => '68411',
        ]);

        // [kode pesanan, berapa hari lalu, urutan UMKM, status sub-pesanan, status bayar, jumlah]
        $daftar = [
            ['ORD-20261101-001', 3, 0, 'DIPROSES', 'PAID', 2],
            ['ORD-20261102-002', 2, 0, 'MENUNGGU_PEMBAYARAN', 'PENDING', 1],
            ['ORD-20261102-003', 2, 0, 'SELESAI', 'PAID', 1],
            ['ORD-20261103-004', 1, 1, 'SELESAI', 'PAID', 3],
        ];

        $detailRetur = null;
        $produkCustom = null;

        foreach ($daftar as [$kode, $hari, $urutan, $statusSub, $statusBayar, $jumlah]) {
            $u = $umkm[$urutan] ?? $umkm[0];
            $produk = DB::table('produk')->where('id_umkm', $u->id_umkm)->orderBy('id_produk')->first();

            if (! $produk) {
                continue;
            }

            $waktu = now()->subDays($hari);

            $idPesanan = DB::table('pesanan')->insertGetId([
                'id_pengguna' => $pembeli->id_pengguna,
                'id_alamat' => $idAlamat,
                'kode_pesanan' => $kode,
                'tanggal_pesanan' => $waktu,
            ]);

            $idSub = DB::table('sub_pesanan')->insertGetId([
                'id_pesanan' => $idPesanan,
                'id_umkm' => $u->id_umkm,
                'status_sub_pesanan' => $statusSub,
                'dibuat_pada' => $waktu,
            ]);

            $idDetail = DB::table('detail_pesanan')->insertGetId([
                'id_sub_pesanan' => $idSub,
                'id_produk' => $produk->id_produk,
                'harga_retail' => $produk->harga_retail,
                'jumlah' => $jumlah,
            ]);

            DB::table('pembayaran')->insert([
                'id_sub_pesanan' => $idSub,
                'metode_pembayaran' => 'BANK_TRANSFER',
                'jumlah_bayar' => $produk->harga_retail * $jumlah,
                'status_pembayaran' => $statusBayar,
                'waktu_bayar' => $statusBayar === 'PAID' ? $waktu : null,
            ]);

            if ($kode === 'ORD-20261102-003') {
                $detailRetur = $idDetail;
            }

            $produkCustom ??= $produk->id_produk;
        }

        if ($produkCustom) {
            DB::table('permintaan_custom')->insert([
                'id_pengguna' => $pembeli->id_pengguna,
                'id_produk' => $produkCustom,
                'spesifikasi' => 'Ukuran lebih besar dengan motif tambahan.',
                'status_custom' => 'PENDING',
            ]);
        }

        if ($detailRetur) {
            DB::table('retur')->insert([
                'id_detail_pesanan' => $detailRetur,
                'jumlah' => 1,
                'alasan' => 'Produk rusak saat diterima',
                'jenis_retur' => 'REFUND',
                'status_retur' => 'DIAJUKAN',
                'waktu_diajukan' => now(),
            ]);
        }
    }
}