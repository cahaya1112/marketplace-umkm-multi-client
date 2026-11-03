<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Umkm\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * TC-03 (FR-AUTH-03) dan TC-04 (FR-AUTH-04):
 * pembatasan akses berdasarkan role dan kepemilikan UMKM.
 */
class OtorisasiUmkmTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: User, 1: Umkm}
     */
    private function buatAdminUmkm(string $email, string $namaToko): array
    {
        $user = User::create([
            'nama' => 'Admin '.$namaToko,
            'email' => $email,
            'kata_sandi' => 'password123',
            'peran' => 'ADMIN_UMKM',
        ]);

        $umkm = Umkm::create([
            'id_pengguna' => $user->id_pengguna,
            'nama' => $namaToko,
            'status' => 'AKTIF',
        ]);

        return [$user, $umkm];
    }

    private function buatPengguna(string $email, string $peran): User
    {
        return User::create([
            'nama' => 'Pengguna '.$peran,
            'email' => $email,
            'kata_sandi' => 'password123',
            'peran' => $peran,
        ]);
    }

    public function test_admin_umkm_dapat_membuka_dashboard_umkm_miliknya(): void
    {
        [$userA, $umkmA] = $this->buatAdminUmkm('a@example.com', 'Toko A');

        $this->actingAs($userA)
            ->get(route('umkm.dashboard', ['umkm' => $umkmA->id_umkm]))
            ->assertOk()
            ->assertSee('Dashboard UMKM: Toko A');
    }

    public function test_admin_umkm_a_ditolak_membuka_data_umkm_b(): void
    {
        [$userA] = $this->buatAdminUmkm('a@example.com', 'Toko A');
        [, $umkmB] = $this->buatAdminUmkm('b@example.com', 'Toko B');

        $this->actingAs($userA)
            ->get(route('umkm.dashboard', ['umkm' => $umkmB->id_umkm]))
            ->assertForbidden();
    }

    public function test_umkm_yang_tidak_ada_menghasilkan_404(): void
    {
        [$userA] = $this->buatAdminUmkm('a@example.com', 'Toko A');

        $this->actingAs($userA)
            ->get(route('umkm.dashboard', ['umkm' => 9999]))
            ->assertNotFound();
    }

    public function test_pembeli_ditolak_masuk_portal_umkm(): void
    {
        [, $umkmA] = $this->buatAdminUmkm('a@example.com', 'Toko A');
        $pembeli = $this->buatPengguna('pembeli@example.com', 'PEMBELI');

        $this->actingAs($pembeli)
            ->get(route('umkm.dashboard', ['umkm' => $umkmA->id_umkm]))
            ->assertForbidden();
    }

    public function test_super_admin_tidak_masuk_lewat_portal_umkm(): void
    {
        [, $umkmA] = $this->buatAdminUmkm('a@example.com', 'Toko A');
        $superAdmin = $this->buatPengguna('admin@example.com', 'SUPER_ADMIN');

        $this->actingAs($superAdmin)
            ->get(route('umkm.dashboard', ['umkm' => $umkmA->id_umkm]))
            ->assertForbidden();
    }

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        [, $umkmA] = $this->buatAdminUmkm('a@example.com', 'Toko A');

        $this->get(route('umkm.dashboard', ['umkm' => $umkmA->id_umkm]))
            ->assertRedirect(route('login'));
    }

    public function test_policy_manage_sesuai_aturan_kepemilikan(): void
    {
        [$userA, $umkmA] = $this->buatAdminUmkm('a@example.com', 'Toko A');
        [$userB] = $this->buatAdminUmkm('b@example.com', 'Toko B');
        $superAdmin = $this->buatPengguna('admin@example.com', 'SUPER_ADMIN');
        $pembeli = $this->buatPengguna('pembeli@example.com', 'PEMBELI');

        $this->assertTrue(Gate::forUser($userA)->allows('manage', $umkmA));
        $this->assertFalse(Gate::forUser($userB)->allows('manage', $umkmA));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('manage', $umkmA));
        $this->assertFalse(Gate::forUser($pembeli)->allows('manage', $umkmA));
    }
}