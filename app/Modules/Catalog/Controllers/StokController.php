<?php

namespace App\Modules\Catalog\Controllers;

use App\Modules\Catalog\Produk;
use App\Modules\Catalog\Stok;
use Illuminate\Http\Request;

class StokController extends KelolaController
{
    public function index(Request $request)
    {
        $umkm = $this->umkmSaya();

        $semua = Produk::with('stok')
            ->where('id_umkm', $umkm->id_umkm)
            ->orderBy('nama')
            ->get();

        $status = fn ($p) => $p->stok?->status ?? 'HABIS';

        $ringkasan = [
            'total'   => $semua->count(),
            'MENIPIS' => $semua->filter(fn ($p) => $status($p) === 'MENIPIS')->count(),
            'HABIS'   => $semua->filter(fn ($p) => $status($p) === 'HABIS')->count(),
        ];

        $filter = $request->query('status');
        $daftar = in_array($filter, ['AMAN', 'MENIPIS', 'HABIS'], true)
            ? $semua->filter(fn ($p) => $status($p) === $filter)->values()
            : $semua;

        return view('catalog::kelola.stok.index', compact('umkm', 'daftar', 'ringkasan', 'filter'));
    }

    public function update(Request $request, int $id)
    {
        $umkm = $this->umkmSaya();

        $produk = Produk::where('id_umkm', $umkm->id_umkm)->findOrFail($id);

        $data = $request->validate([
            'jumlah_stok'   => ['required', 'integer', 'min:0', 'max:1000000'],
            'batas_minimum' => ['required', 'integer', 'min:0', 'max:1000000'],
        ]);

        Stok::updateOrCreate(['id_produk' => $produk->id_produk], $data);

        return back()->with('sukses', 'Stok "' . $produk->nama . '" diperbarui.');
    }
}