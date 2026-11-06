<?php

namespace App\Modules\Catalog\Controllers;

use App\Modules\Catalog\PermintaanCustom;
use Illuminate\Http\Request;

class CustomController extends KelolaController
{
    public function index(Request $request)
    {
        $umkm = $this->umkmSaya();
        $filter = $request->query('status');

        $daftar = PermintaanCustom::query()
            ->with(['produk', 'pembeli'])
            ->whereHas('produk', fn ($q) => $q->where('id_umkm', $umkm->id_umkm))
            ->when(
                in_array($filter, ['PENDING', 'DISETUJUI', 'DITOLAK'], true),
                fn ($q) => $q->where('status_custom', $filter)
            )
            ->orderByRaw("CASE WHEN status_custom = 'PENDING' THEN 0 ELSE 1 END")
            ->orderByDesc('id_custom')
            ->paginate(10)
            ->withQueryString();

        return view('catalog::kelola.custom.index', compact('umkm', 'daftar', 'filter'));
    }

    // Terima atau tolak. Tahap 1: tanpa harga custom (SRS 2.6).
    public function proses(Request $request, int $id, string $aksi)
    {
        $umkm = $this->umkmSaya();

        // Hanya permintaan untuk produk milik UMKM ini (NFR-MC-06).
        $custom = PermintaanCustom::whereHas('produk', fn ($q) => $q->where('id_umkm', $umkm->id_umkm))
            ->findOrFail($id);

        if ($custom->status_custom !== 'PENDING') {
            return back()->with('galat', 'Permintaan ini sudah diproses dan tidak dapat diubah lagi.');
        }

        $data = $request->validate([
            'catatan_umkm' => ['nullable', 'string', 'max:500'],
        ]);

        $custom->status_custom = $aksi === 'setujui' ? 'DISETUJUI' : 'DITOLAK';
        $custom->catatan_umkm = $data['catatan_umkm'] ?? '';
        $custom->save();

        return back()->with('sukses', $aksi === 'setujui' ? 'Permintaan custom disetujui.' : 'Permintaan custom ditolak.');
    }
}