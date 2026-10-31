<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        // Super Admin menuju dashboard platform.
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Admin UMKM menuju dashboard UMKM miliknya.
        if ($user->isAdminUmkm()) {
            $umkm = $user->umkm;

            if (!$umkm) {
                return redirect()
                    ->route('pembeli.home')
                    ->with('status', 'Akun kamu belum memiliki UMKM.');
            }

            return redirect()->route('umkm.dashboard', [
                'umkm' => $umkm->id_umkm,
            ]);
        }

        // Pembeli menuju halaman utama marketplace.
        if ($user->isPembeli()) {
            return redirect()->route('pembeli.home');
        }

        // Pengguna dengan peran yang tidak dikenali.
        abort(403, 'Peran pengguna tidak dikenali.');
    }
}