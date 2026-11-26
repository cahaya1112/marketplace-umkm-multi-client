<?php

namespace App\Modules\Ordering\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Produk;
use App\Modules\Ordering\Models\Keranjang;
use App\Modules\Ordering\Models\KeranjangItem;
use Illuminate\Http\Request;

class BeliSekarangController extends Controller
{
    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $produk = Produk::with('stok')->findOrFail($id);

        // Pastikan produk memiliki stok yang mencukupi.
        if (
            !$produk->stok ||
            $produk->stok->jumlah_stok < $validated['jumlah']
        ) {
            return back()->with('galat', 'Stok produk tidak mencukupi.');
        }

        $keranjang = Keranjang::firstOrCreate([
            'id_pengguna' => auth()->user()->id_pengguna,
        ]);

        // Cari item produk reguler milik pengguna.
        $item = KeranjangItem::where(
            'id_keranjang',
            $keranjang->id_keranjang
        )
            ->where('id_produk', $produk->id_produk)
            ->whereDoesntHave('permintaanCustom')
            ->first();

        if ($item) {
            $item->update([
                'jumlah' => $validated['jumlah'],
            ]);
        } else {
            $item = KeranjangItem::create([
                'id_keranjang' => $keranjang->id_keranjang,
                'id_produk' => $produk->id_produk,
                'jumlah' => $validated['jumlah'],
            ]);
        }

        return redirect()->route('checkout.index', [
            'selected_items' => [$item->id_keranjang_item],
        ]);
    }
}