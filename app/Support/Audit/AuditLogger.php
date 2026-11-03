<?php

namespace App\Support\Audit;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * FR-LOG-01 / UC-LOG-01 - Pencatatan aktivitas penting.
 *
 * Contoh pemakaian manual:
 *   AuditLogger::catat('VERIFIKASI_UMKM', $umkm, ['status' => 'NONAKTIF'], ['status' => 'AKTIF']);
 */
final class AuditLogger
{
    /**
     * Mencatat satu aktivitas. Hanya mencatat jika ada pengguna terautentikasi
     * (sesuai entry condition UC-LOG-01); jika tidak ada, mengembalikan null.
     *
     * @param  string      $aksi         mis. CREATE, UPDATE, DELETE, atau nama aksi khusus
     * @param  Model       $entitas      model yang terdampak
     * @param  array|null  $dataSebelum  nilai sebelum aksi (null untuk CREATE)
     * @param  array|null  $dataSesudah  nilai sesudah aksi (null untuk DELETE)
     * @param  int|null    $idUmkm       isi manual jika perlu; jika null dicari otomatis
     */
    public static function catat(
        string $aksi,
        Model $entitas,
        ?array $dataSebelum = null,
        ?array $dataSesudah = null,
        ?int $idUmkm = null,
    ): ?AuditLog {
        /** @var User|null $pengguna */
        $pengguna = Auth::user();

        if ($pengguna === null) {
            return null;
        }

        return AuditLog::create([
            'id_pengguna' => $pengguna->getKey(),
            'peran' => $pengguna->peran,
            'id_umkm' => $idUmkm ?? self::cariIdUmkm($entitas, $pengguna),
            'aksi' => $aksi,
            'nama_entitas' => $entitas->getTable(),
            'id_entitas' => $entitas->getKey(),
            'data_sebelum' => self::bersihkan($entitas, $dataSebelum),
            'data_sesudah' => self::bersihkan($entitas, $dataSesudah),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Menentukan UMKM yang terkait dengan aksi.
     */
    private static function cariIdUmkm(Model $entitas, User $pengguna): ?int
    {
        // Entitas itu sendiri adalah UMKM.
        if ($entitas->getTable() === 'umkm') {
            return (int) $entitas->getKey();
        }

        // Entitas milik UMKM (produk, sub_pesanan, dst).
        $idUmkm = $entitas->getAttribute('id_umkm');
        if ($idUmkm !== null) {
            return (int) $idUmkm;
        }

        // Aksi dilakukan Admin UMKM pada entitas yang tidak punya id_umkm.
        if ($pengguna->isAdminUmkm()) {
            return $pengguna->umkm?->id_umkm;
        }

        return null;
    }

    /**
     * Membuang data sensitif (password, token) sebelum disimpan ke log.
     */
    private static function bersihkan(Model $entitas, ?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        $rahasia = array_merge(
            $entitas->getHidden(),
            ['kata_sandi', 'password', 'remember_token'],
        );

        return Arr::except($data, $rahasia);
    }
}