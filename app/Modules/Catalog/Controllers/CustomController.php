<?php

namespace App\Modules\Catalog\Controllers;

use App\Modules\Catalog\PermintaanCustom;
use Illuminate\Http\Request;

class CustomController extends KelolaController
{
    // Alur: PENDING (pembeli mengajukan) -> UMKM kirim penawaran harga -> MENUNGGU_PERSETUJUAN
    // -> pembeli memutuskan -> DISETUJUI / DITOLAK. UMKM juga boleh menolak langsung dari PENDING.
    public function index(Request $request)
    {
        $umkm = $this->umkmSaya();
        $filter = $request->query('status');

        $daftar = PermintaanCustom::query()
            ->with(['produk', 'pembeli'])
            ->whereHas('produk', fn ($q) => $q->where('id_umkm', $umkm->id_umkm))
            ->when(
                in_array($filter, ['PENDING', 'MENUNGGU_PERSETUJUAN', 'DISETUJUI', 'DITOLAK'], true),
                fn ($q) => $q->where('status_custom', $filter)
            )
            ->orderByRaw("CASE status_custom WHEN 'PENDING' THEN 0 WHEN 'MENUNGGU_PERSETUJUAN' THEN 1 ELSE 2 END")
            ->orderByDesc('id_custom')
            ->paginate(10)
            ->withQueryString();

        return view('catalog::kelola.custom.index', compact('umkm', 'daftar', 'filter'));
    }

    // Route kelola.custom.proses. {aksi} 'setujui' = kirim penawaran harga, 'tolak' = menolak permintaan.
    public function proses(Request $request, int $id, string $aksi)
    {
        $umkm = $this->umkmSaya();

        // Hanya permintaan untuk produk milik UMKM ini (NFR-MC-06).
        $custom = PermintaanCustom::whereHas('produk', fn ($q) => $q->where('id_umkm', $umkm->id_umkm))
            ->findOrFail($id);

        if ($custom->status_custom !== 'PENDING') {
            return back()->with('galat', 'Hanya permintaan baru yang dapat ditanggapi.');
        }

        if ($aksi === 'setujui') {
            $data = $request->validate([
                'harga_custom' => ['required', 'integer', 'min:1', 'max:1000000000'],
                'catatan_umkm' => ['nullable', 'string', 'max:500'],
            ]);

            $custom->harga_custom = $data['harga_custom'];
            $custom->catatan_umkm = $data['catatan_umkm'] ?? '';
            $custom->status_custom = 'MENUNGGU_PERSETUJUAN';
            $custom->save();

            return back()->with('sukses', 'Penawaran harga dikirim ke pembeli.');
        }

        $data = $request->validate([
            'catatan_umkm' => ['nullable', 'string', 'max:500'],
        ]);

        $custom->catatan_umkm = $data['catatan_umkm'] ?? '';
        $custom->status_custom = 'DITOLAK';
        $custom->save();

        return back()->with('sukses', 'Permintaan custom ditolak.');
    }
}