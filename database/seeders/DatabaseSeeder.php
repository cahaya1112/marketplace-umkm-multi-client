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
                'name'     => 'Admin Marketplace',
                'password' => bcrypt('password123'),
                'role'     => 'admin',
            ]
        );

        // Data Pemilik UMKM
        User::updateOrCreate(
            ['email' => 'penjual@marketplace.com'],
            [
                'name'     => 'Pemilik UMKM',
                'password' => bcrypt('password123'),
                'role'     => 'umkm_owner', // <--- Disesuaikan ke 'umkm_owner'
            ]
        );

        // Data Customer / Pembeli
        User::updateOrCreate(
            ['email' => 'customer@marketplace.com'],
            [
                'name'     => 'Customer Pembeli',
                'password' => bcrypt('password123'),
                'role'     => 'customer',
            ]
        );
    }
}