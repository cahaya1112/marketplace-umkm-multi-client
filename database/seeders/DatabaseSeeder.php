<?php

namespace Database\Seeders;

use App\Models\User;
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
        // Data Admin Marketplace
        User::updateOrCreate(
            ['email' => 'admin@marketplace.com'],
            [
                'nama' => 'Admin Marketplace',
                'kata_sandi' => 'password123',
                'peran' => 'SUPER_ADMIN',
            ]
        );

        // Data Pemilik UMKM
        User::updateOrCreate(
            ['email' => 'penjual@marketplace.com'],
            [
                'nama' => 'Pemilik UMKM',
                'kata_sandi' => 'password123',
                'peran' => 'ADMIN_UMKM',
            ]
        );

        // Data Customer / Pembeli
        User::updateOrCreate(
            ['email' => 'customer@marketplace.com'],
            [
                'nama' => 'Customer Pembeli',
                'kata_sandi' => 'password123',
                'peran' => 'PEMBELI',
            ]
        );
    }
}