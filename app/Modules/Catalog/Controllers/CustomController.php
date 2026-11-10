<?php

namespace App\Modules\Catalog\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Produk;
use App\Modules\Catalog\PermintaanCustom;
use Illuminate\Http\Request;

class CustomController extends Controller
{
    /**
     * Mengirim permintaan custom dari pembeli.
     */
    public function store(Request $request, int $id_produk)
    {
        abort_unless(auth()->user()->isPembeli(), 403);

        $data = $request->validate([
            'spesifikasi' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        $produk = Produk::tampilDiKatalog()
            ->whereKey($id_produk)
            ->firstOrFail();

        PermintaanCustom::create([
            'id_pengguna' => auth()->id(),
            'id_produk' => $produk->id_produk,
            'id_keranjang_item' => null,
            'spesifikasi' => $data['spesifikasi'],
            'harga_custom' => null,
            'status_custom' => 'PENDING',
            'catatan_umkm' => null,
        ]);

        return back()->with(
            'success',
            'Permintaan custom berhasil dikirim. Silakan tunggu penawaran harga dari UMKM.'
        );
    }

    /**
     * Menampilkan daftar permintaan custom milik pembeli.
     */
    public function index()
    {
        abort_unless(auth()->user()->isPembeli(), 403);

        $daftar = PermintaanCustom::with(['produk.umkm'])
            ->where('id_pengguna', auth()->id())
            ->orderByDesc('id_custom')
            ->paginate(10);

        return view('ordering::custom.index', compact('daftar'));
    }

    /**
     * Menyetujui atau menolak penawaran harga dari UMKM.
     */
    public function keputusan(Request $request, int $id, string $aksi)
    {
        abort_unless(auth()->user()->isPembeli(), 403);
        abort_unless(in_array($aksi, ['setujui', 'tolak'], true), 404);

        $custom = PermintaanCustom::where('id_pengguna', auth()->id())
            ->whereKey($id)
            ->firstOrFail();

        if (
            $custom->status_custom !== 'MENUNGGU_PERSETUJUAN' ||
            $custom->harga_custom === null
        ) {
            return back()->with(
                'galat',
                'Permintaan ini belum memiliki penawaran harga yang bisa diproses.'
            );
        }

        $custom->status_custom = $aksi === 'setujui'
            ? 'DISETUJUI'
            : 'DITOLAK';

        $custom->save();

        $pesan = $aksi === 'setujui'
            ? 'Penawaran harga berhasil disetujui.'
            : 'Penawaran harga berhasil ditolak.';

        return redirect()
            ->route('custom.saya')
            ->with('success', $pesan);
    }
}
