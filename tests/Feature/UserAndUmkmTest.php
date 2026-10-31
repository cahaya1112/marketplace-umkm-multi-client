<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Umkm\Umkm;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAndUmkmTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Pengujian 1:
     * Memastikan pengguna dapat disimpan dengan kolom yang benar
     * dan kata sandi tersimpan dalam bentuk hash.
     */
    public function test_pengguna_dapat_disimpan_dengan_kolom_yang_benar(): void
    {
        $user = User::create([
            'nama' => 'Pengguna Pengujian',
            'email' => 'pengujian@example.com',
            'kata_sandi' => 'password123',
            'peran' => 'PEMBELI',
        ]);

        $this->assertDatabaseHas('pengguna', [
            'id_pengguna' => $user->id_pengguna,
            'nama' => 'Pengguna Pengujian',
            'email' => 'pengujian@example.com',
            'peran' => 'PEMBELI',
        ]);

        $this->assertNotSame(
            'password123',
            $user->fresh()->kata_sandi
        );

        $this->assertTrue(
            password_verify(
                'password123',
                $user->fresh()->kata_sandi
            )
        );
    }

    /**
     * Pengujian 2:
     * Memastikan pengguna dapat memiliki UMKM
     * dan kedua arah relasi berjalan dengan benar.
     */
    public function test_pengguna_dapat_memiliki_umkm(): void
    {
        $user = User::create([
            'nama' => 'Pemilik UMKM',
            'email' => 'pemilik@example.com',
            'kata_sandi' => 'password123',
            'peran' => 'ADMIN_UMKM',
        ]);

        $umkm = Umkm::create([
            'id_pengguna' => $user->id_pengguna,
            'nama' => 'UMKM Pengujian',
            'status' => 'AKTIF',
        ]);

        $this->assertDatabaseHas('umkm', [
            'id_umkm' => $umkm->id_umkm,
            'id_pengguna' => $user->id_pengguna,
            'nama' => 'UMKM Pengujian',
            'status' => 'AKTIF',
        ]);

        $this->assertTrue(
            $user->umkm()
                ->where('id_umkm', $umkm->id_umkm)
                ->exists()
        );

        $this->assertTrue(
            $umkm->pengguna()
                ->where('id_pengguna', $user->id_pengguna)
                ->exists()
        );
    }

    /**
     * Pengujian 3:
     * Memastikan satu pengguna tidak dapat memiliki
     * dua UMKM karena id_pengguna bersifat unik.
     */
    public function test_satu_pengguna_tidak_dapat_memiliki_dua_umkm(): void
    {
        $user = User::create([
            'nama' => 'Pemilik Dua UMKM',
            'email' => 'pemilik-dua@example.com',
            'kata_sandi' => 'password123',
            'peran' => 'ADMIN_UMKM',
        ]);

        Umkm::create([
            'id_pengguna' => $user->id_pengguna,
            'nama' => 'UMKM Pertama',
            'status' => 'AKTIF',
        ]);

        try {
            Umkm::create([
                'id_pengguna' => $user->id_pengguna,
                'nama' => 'UMKM Kedua',
                'status' => 'AKTIF',
            ]);
        } catch (QueryException $exception) {
            $this->assertDatabaseCount('umkm', 1);

            return;
        }

        $this->fail(
            'Database seharusnya menolak pengguna yang memiliki dua UMKM.'
        );
    }

    /**
     * Pengujian 4:
     * Memastikan DatabaseSeeder membuat tiga pengguna
     * dengan peran yang sesuai.
     */
    public function test_database_seeder_membuat_tiga_pengguna_dengan_peran_yang_benar(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->assertDatabaseHas('pengguna', [
            'email' => 'admin@marketplace.com',
            'peran' => 'SUPER_ADMIN',
        ]);

        $this->assertDatabaseHas('pengguna', [
            'email' => 'penjual@marketplace.com',
            'peran' => 'ADMIN_UMKM',
        ]);

        $this->assertDatabaseHas('pengguna', [
            'email' => 'customer@marketplace.com',
            'peran' => 'PEMBELI',
        ]);

        $this->assertDatabaseCount('pengguna', 3);
    }
}