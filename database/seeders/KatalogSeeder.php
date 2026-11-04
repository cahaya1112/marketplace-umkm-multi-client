<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KatalogSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Anyaman', 'Furnitur', 'Rotan'] as $nama) {
            DB::table('kategori')->updateOrInsert(['nama' => $nama], ['nama' => $nama]);
        }

        $kategori = DB::table('kategori')->pluck('id_kategori', 'nama');
        $umkm = DB::table('umkm')->orderBy('id_umkm')->pluck('id_umkm')->values();

        if ($umkm->isEmpty()) {
            return;
        }

        // [nama, harga, kategori, berat gram]
        $daftar = [
            ['Lampu Bambu', 120000, 'Anyaman', 800],
            ['Keranjang Rotan', 85000, 'Rotan', 600],
            ['Tas Anyaman Indah', 75000, 'Anyaman', 400],
            ['Tas Anyaman Bambu', 75000, 'Anyaman', 400],
            ['Keranjang Buah', 50000, 'Anyaman', 500],
            ['Lampu Rotan', 150000, 'Rotan', 900],
            ['Kursi Rotan', 350000, 'Furnitur', 4000],
        ];

        foreach ($daftar as $i => [$nama, $harga, $kat, $berat]) {
            $idUmkm = $umkm[$i % $umkm->count()];

            DB::table('produk')->updateOrInsert(
                ['nama' => $nama, 'id_umkm' => $idUmkm],
                [
                    'id_kategori' => $kategori[$kat],
                    'deskripsi' => $nama.' buatan tangan pengrajin lokal.',
                    'harga_retail' => $harga,
                    'berat_gram' => $berat,
                    'gambar' => '',
                ]
            );

            $idProduk = DB::table('produk')->where('nama', $nama)->where('id_umkm', $idUmkm)->value('id_produk');

            DB::table('stok')->updateOrInsert(
                ['id_produk' => $idProduk],
                ['jumlah_stok' => $nama === 'Keranjang Buah' ? 0 : 25, 'batas_minimum' => 5]
            );
        }
    }
}