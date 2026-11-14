<?php

namespace App\Modules\Ordering\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Produk;
use App\Modules\Catalog\Stok;
use App\Modules\Ordering\Models\Keranjang;
use App\Modules\Ordering\Models\KeranjangItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KeranjangController extends Controller
{
    /**
     * Menampilkan keranjang milik pembeli.
     */
    public function index()
    {
        $keranjang = Keranjang::firstOrCreate([
            'id_pengguna' => auth()->id(),
        ]);

        $keranjang->load([
            'item.produk.umkm',
            'item.produk.stok',
            'item.permintaanCustom',
        ]);

        $kelompokUmkm = $keranjang->item->groupBy(
            fn ($item) => $item->produk->id_umkm
        );

        return view('ordering::keranjang.index', compact(
            'keranjang',
            'kelompokUmkm'
        ));
    }

    /**
     * Menambahkan produk reguler ke keranjang.
     */
    public function store(Request $request, $id_produk)
    {
        $data = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($id_produk, $data) {
            $produk = Produk::tampilDiKatalog()
                ->whereKey($id_produk)
                ->firstOrFail();

            $stok = Stok::where('id_produk', $produk->id_produk)
                ->lockForUpdate()
                ->first();

            if (!$stok || $stok->jumlah_stok < 1) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Produk sedang habis.',
                ]);
            }

            $keranjang = Keranjang::firstOrCreate([
                'id_pengguna' => auth()->id(),
            ]);

            // Cari item reguler saja.
            // Item yang sudah terhubung dengan custom tidak boleh digabung.
            $item = KeranjangItem::where(
                'id_keranjang',
                $keranjang->id_keranjang
            )->where(
                'id_produk',
                $produk->id_produk
            )->whereDoesntHave('permintaanCustom')
                ->lockForUpdate()
                ->first();

            $jumlahBaru = ($item?->jumlah ?? 0) + $data['jumlah'];

            if ($jumlahBaru > $stok->jumlah_stok) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Jumlah melebihi stok yang tersedia.',
                ]);
            }

            if ($item) {
                $item->update([
                    'jumlah' => $jumlahBaru,
                ]);
            } else {
                KeranjangItem::create([
                    'id_keranjang' => $keranjang->id_keranjang,
                    'id_produk' => $produk->id_produk,
                    'jumlah' => $data['jumlah'],
                ]);
            }
        });

        return back()->with(
            'success',
            'Produk berhasil ditambahkan ke keranjang.'
        );
    }

    /**
     * Memperbarui jumlah produk reguler.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $keranjang = Keranjang::where(
            'id_pengguna',
            auth()->id()
        )->firstOrFail();

        DB::transaction(function () use ($keranjang, $id, $data) {

            $item = KeranjangItem::where(
                'id_keranjang',
                $keranjang->id_keranjang
            )->whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            // Jumlah item custom tidak boleh diubah dari form reguler.
            if ($item->permintaanCustom()->exists()) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Jumlah pesanan custom tidak dapat diubah melalui keranjang.',
                ]);
            }

            $stok = Stok::where('id_produk', $item->id_produk)
                ->lockForUpdate()
                ->first();

            if (!$stok || $data['jumlah'] > $stok->jumlah_stok) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Jumlah melebihi stok yang tersedia.',
                ]);
            }

            $item->update([
                'jumlah' => $data['jumlah'],
            ]);
        });

        return back()->with(
            'success',
            'Jumlah produk berhasil diperbarui.'
        );
    }

    /**
     * Menghapus item dari keranjang.
     */
    public function destroy($id)
    {
        $keranjang = Keranjang::where(
            'id_pengguna',
            auth()->id()
        )->firstOrFail();

        DB::transaction(function () use ($keranjang, $id) {

            $item = KeranjangItem::where(
                'id_keranjang',
                $keranjang->id_keranjang
            )->whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $item->delete();
        });

        return back()->with(
            'success',
            'Produk berhasil dihapus dari keranjang.'
        );
    }
}
