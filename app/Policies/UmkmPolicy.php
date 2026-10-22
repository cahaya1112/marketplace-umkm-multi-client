<?php

namespace App\Policies;

use App\Models\User;

class UmkmPolicy
{
    /**
     * Memeriksa apakah pengguna berhak mengelola UMKM tertentu.
     */
    public function manage(User $user, $umkm): bool
    {
        // 1. Super Admin memiliki akses penuh ke semua UMKM
        if ($user->isSuperAdmin()) {
            return true;
        }

        // 2. Admin UMKM hanya boleh mengakses jika id_pengguna pada UMKM cocok dengan user yang login
        $ownerId = is_object($umkm) ? $umkm->id_pengguna : null;

        return $user->isAdminUmkm() && $user->id === $ownerId;
    }
}