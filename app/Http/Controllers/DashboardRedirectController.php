<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        // 1. Super Admin dialihkan ke Dashboard Platform
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // 2. Admin UMKM dialihkan ke Dashboard Toko UMKM miliknya
        if ($user->isAdminUmkm()) {
            // Placeholder ID toko. Pada implementasi selanjutnya, 
            // ini akan diambil dari relasi tabel UMKM ($user->umkm->id_umkm)
            $umkmId = 'toko-dummy';
            return redirect()->route('umkm.dashboard', ['umkm' => $umkmId]);
        }

        // 3. Pembeli dialihkan ke halaman utama Marketplace
        return redirect()->route('pembeli.home');
    }
}