<?php

namespace App\Http\Middleware;

use App\Modules\Umkm\Umkm;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * FR-AUTH-04 / UC-AUTH-04 - Otorisasi Client.
 *
 * Memastikan {umkm} pada URL benar-benar milik pengguna yang login.
 * id_umkm dari URL adalah input pengguna, jadi TIDAK PERNAH dipercaya
 * sebelum dicocokkan dengan kepemilikan di server (NFR-MC-04, NFR-SEC-03).
 */
final class EnsureUmkmAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $umkm = $request->route('umkm');

        // Biasanya sudah berupa model (lihat Route::model di AppServiceProvider).
        // Jika masih berupa id, cari sendiri.
        if (! $umkm instanceof Umkm) {
            $umkm = Umkm::find($umkm);

            abort_if($umkm === null, 404, 'UMKM tidak ditemukan.');

            $request->route()->setParameter('umkm', $umkm);
        }

        if ($user === null || ! Gate::forUser($user)->allows('manage', $umkm)) {
            abort(403, 'Anda tidak memiliki hak akses terhadap data UMKM ini.');
        }

        return $next($request);
    }
}