<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Umkm\Umkm;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Super Admin
        User::updateOrCreate(
            ['email' => 'admin@marketplace.com'],
            [
                'nama' => 'Admin Marketplace',
                'kata_sandi' => 'password123',
                'peran' => 'SUPER_ADMIN',
            ]
        );

        // 2. Akun Penjual pertama
        $penjual1 = User::updateOrCreate(
            ['email' => 'penjual@marketplace.com'],
            [
                'nama' => 'Pemilik UMKM',
                'kata_sandi' => 'password123',
                'peran' => 'ADMIN_UMKM',
            ]
        );

        // Data UMKM pertama
        Umkm::updateOrCreate(
            ['id_pengguna' => $penjual1->id_pengguna],
            [
                'nama' => 'Anyaman Bambu Sari',
                'deskripsi' => 'Kerajinan anyaman bambu.',
                'alamat' => 'Banyuwangi, Jawa Timur',
                'kontak' => '081200000001',
                'status' => 'AKTIF',
            ]
        );

        // 3. Akun Penjual kedua
        $penjual2 = User::updateOrCreate(
            ['email' => 'penjual2@marketplace.com'],
            [
                'nama' => 'Pemilik UMKM Dua',
                'kata_sandi' => 'password123',
                'peran' => 'ADMIN_UMKM',
            ]
        );

        // Data UMKM kedua
        Umkm::updateOrCreate(
            ['id_pengguna' => $penjual2->id_pengguna],
            [
                'nama' => 'Anyaman Pandan Lestari',
                'deskripsi' => 'Kerajinan anyaman pandan.',
                'alamat' => 'Banyuwangi, Jawa Timur',
                'kontak' => '081200000002',
                'status' => 'AKTIF',
            ]
        );

        // 4. Akun Customer
        User::updateOrCreate(
            ['email' => 'customer@marketplace.com'],
            [
                'nama' => 'Customer Pembeli',
                'kata_sandi' => 'password123',
                'peran' => 'PEMBELI',
            ]
        );

        // 5. Seeder katalog
        $this->call([
            KatalogSeeder::class,
        ]);
    }
}
