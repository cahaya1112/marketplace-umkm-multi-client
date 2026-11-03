<?php

namespace App\Policies;

use App\Models\User;
use App\Modules\Umkm\Umkm;

class UmkmPolicy
{
    /**
     * Memeriksa apakah pengguna berhak mengelola data UMKM tertentu.
     *
     * - SUPER_ADMIN : boleh (hak administratif lintas UMKM, NFR-MC-07).
     * - ADMIN_UMKM  : hanya jika UMKM tersebut miliknya (NFR-MC-03, NFR-MC-06).
     * - PEMBELI     : tidak pernah.
     */
    public function manage(User $user, Umkm $umkm): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isAdminUmkm()
            && (int) $umkm->id_pengguna === (int) $user->id_pengguna;
    }
}