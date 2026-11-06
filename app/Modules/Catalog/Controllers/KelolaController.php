<?php

namespace App\Modules\Catalog\Controllers;

use App\Modules\Umkm\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class KelolaController
{
    // Mengambil UMKM milik pengguna yang login. id_umkm TIDAK pernah diambil dari
    // input pengguna (NFR-MC-04). Selain ADMIN_UMKM akan ditolak (NFR-MC-06).
    protected function umkmSaya(): Umkm
    {
        $user = auth()->user();

        abort_unless($user && $user->isAdminUmkm(), 403, 'Hanya Admin UMKM yang dapat mengakses halaman ini.');

        $umkm = $user->umkm;
        abort_unless($umkm, 403, 'Akun ini belum terhubung dengan UMKM.');

        return $umkm;
    }

    protected function simpanFile(Request $request, string $field, string $folder): ?string
    {
        return $request->hasFile($field)
            ? $request->file($field)->store($folder, 'public')
            : null;
    }

    protected function hapusFile(?string $path): void
    {
        if ($path && ! Str::startsWith($path, ['http://', 'https://'])) {
            Storage::disk('public')->delete($path);
        }
    }
}