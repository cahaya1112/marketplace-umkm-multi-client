<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Umkm\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAndUmkmTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_roles_and_umkm_relation()
    {
        // 1. Tes Role Admin
        $admin = User::create([
            'name' => 'Admin Sistem',
            'email' => 'admin@marketplace.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);
        $this->assertTrue($admin->isAdmin());

        // 2. Tes Role UMKM Owner & Relasi
        $owner = User::create([
            'name' => 'Pak Ahmad Anyaman',
            'email' => 'ahmad@gintangan.com',
            'password' => bcrypt('password123'),
            'role' => 'umkm_owner',
        ]);
        $this->assertTrue($owner->isUmkmOwner());

        $umkm = Umkm::create([
            'user_id' => $owner->id,
            'name' => 'Galeri Anyaman Bambu',
            'slug' => 'galeri-anyaman-bambu',
            'description' => 'Pusat kerajinan anyaman bambu',
        ]);

        // Tes Relasi HasOne dan BelongsTo
        $this->assertEquals('Galeri Anyaman Bambu', $owner->umkm->name);
        $this->assertEquals('Pak Ahmad Anyaman', $umkm->owner->name);

        // 3. Tes Role Customer
        $customer = User::create([
            'name' => 'Siti Pembeli',
            'email' => 'siti@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
        ]);
        $this->assertTrue($customer->isCustomer());
    }
}