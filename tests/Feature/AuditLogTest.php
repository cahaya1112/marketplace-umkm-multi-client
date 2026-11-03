<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use App\Modules\Umkm\Umkm;
use App\Support\Audit\AuditLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

/**
 * TC-27 (FR-LOG-01): pencatatan audit log append-only.
 */
class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    private function buatPengguna(string $email, string $peran): User
    {
        return User::create([
            'nama' => 'Pengguna '.$peran,
            'email' => $email,
            'kata_sandi' => 'password123',
            'peran' => $peran,
        ]);
    }

    public function test_pembuatan_umkm_tercatat_lengkap_di_audit_log(): void
    {
        $superAdmin = $this->buatPengguna('admin@example.com', 'SUPER_ADMIN');
        $pemilik = $this->buatPengguna('pemilik@example.com', 'ADMIN_UMKM');

        $this->actingAs($superAdmin);

        $umkm = Umkm::create([
            'id_pengguna' => $pemilik->id_pengguna,
            'nama' => 'Toko Uji',
            'status' => 'AKTIF',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'id_pengguna' => $superAdmin->id_pengguna,
            'peran' => 'SUPER_ADMIN',
            'id_umkm' => $umkm->id_umkm,
            'aksi' => 'CREATE',
            'nama_entitas' => 'umkm',
            'id_entitas' => $umkm->id_umkm,
        ]);

        $log = AuditLog::firstOrFail();
        $this->assertNull($log->data_sebelum);
        $this->assertSame('Toko Uji', $log->data_sesudah['nama']);
        $this->assertNotNull($log->ip_address);
        $this->assertNotNull($log->dibuat_pada);
    }

    public function test_perubahan_status_umkm_menyimpan_data_sebelum_dan_sesudah(): void
    {
        $superAdmin = $this->buatPengguna('admin@example.com', 'SUPER_ADMIN');
        $pemilik = $this->buatPengguna('pemilik@example.com', 'ADMIN_UMKM');
        $umkm = Umkm::create([
            'id_pengguna' => $pemilik->id_pengguna,
            'nama' => 'Toko Uji',
            'status' => 'AKTIF',
        ]);

        $this->actingAs($superAdmin);
        $umkm->update(['status' => 'NONAKTIF']);

        $log = AuditLog::where('aksi', 'UPDATE')->firstOrFail();

        $this->assertSame(['status' => 'AKTIF'], $log->data_sebelum);
        $this->assertSame(['status' => 'NONAKTIF'], $log->data_sesudah);
        $this->assertSame($umkm->id_umkm, $log->id_umkm);
    }

    public function test_aksi_tanpa_pengguna_login_tidak_dicatat(): void
    {
        $pemilik = $this->buatPengguna('pemilik@example.com', 'ADMIN_UMKM');

        Umkm::create([
            'id_pengguna' => $pemilik->id_pengguna,
            'nama' => 'Toko Uji',
            'status' => 'AKTIF',
        ]);

        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_data_sensitif_tidak_masuk_audit_log(): void
    {
        $superAdmin = $this->buatPengguna('admin@example.com', 'SUPER_ADMIN');
        $this->actingAs($superAdmin);

        $log = AuditLogger::catat(
            'UBAH_KATA_SANDI',
            $superAdmin,
            ['nama' => 'Lama', 'kata_sandi' => 'rahasia-lama'],
            ['nama' => 'Baru', 'kata_sandi' => 'rahasia-baru'],
        );

        $this->assertSame(['nama' => 'Lama'], $log->data_sebelum);
        $this->assertSame(['nama' => 'Baru'], $log->data_sesudah);
    }

    public function test_audit_log_tidak_boleh_diubah(): void
    {
        $superAdmin = $this->buatPengguna('admin@example.com', 'SUPER_ADMIN');
        $this->actingAs($superAdmin);
        $log = AuditLogger::catat('CONTOH', $superAdmin);

        $this->expectException(LogicException::class);
        $log->update(['aksi' => 'DIUBAH']);
    }

    public function test_audit_log_tidak_boleh_dihapus(): void
    {
        $superAdmin = $this->buatPengguna('admin@example.com', 'SUPER_ADMIN');
        $this->actingAs($superAdmin);
        $log = AuditLogger::catat('CONTOH', $superAdmin);

        $this->expectException(LogicException::class);
        $log->delete();
    }
}