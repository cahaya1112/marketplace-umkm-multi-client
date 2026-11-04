<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Umkm\Umkm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Super Admin platform
        User::updateOrCreate(
            ['email' => 'admin@marketplace.com'],
            [
                'nama' => 'Admin Marketplace',
                'kata_sandi' => 'password123',
                'peran' => 'SUPER_ADMIN',
            ]
        );

        // Admin UMKM pertama + UMKM-nya
        $penjual1 = User::updateOrCreate(
            ['email' => 'penjual@marketplace.com'],
            [
                'nama' => 'Pemilik UMKM',
                'kata_sandi' => 'password123',
                'peran' => 'ADMIN_UMKM',
            ]
        );

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

        // Admin UMKM kedua + UMKM-nya (untuk menguji isolasi antar-UMKM)
        $penjual2 = User::updateOrCreate(
            ['email' => 'penjual2@marketplace.com'],
            [
                'nama' => 'Pemilik UMKM Dua',
                'kata_sandi' => 'password123',
                'peran' => 'ADMIN_UMKM',
            ]
        );

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

        // Pembeli
        User::updateOrCreate(
            ['email' => 'customer@marketplace.com'],
            [
                'nama' => 'Customer Pembeli',
                'kata_sandi' => 'password123',
                'peran' => 'PEMBELI',
            ]
        );
        
        $this->call(KatalogSeeder::class);
    }
}