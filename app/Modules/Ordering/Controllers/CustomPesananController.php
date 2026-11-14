<?php

namespace App\Modules\Ordering\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Produk;
use App\Modules\Catalog\Stok;
use App\Modules\Catalog\PermintaanCustom;
use App\Modules\Ordering\Models\Keranjang;
use App\Modules\Ordering\Models\KeranjangItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomPesananController extends Controller
{
    /**
     * Mengirim permintaan custom dari pembeli.
     */
    public function store(Request $request, int $id_produk)
    {
        abort_unless(auth()->user()->isPembeli(), 403);

        $data = $request->validate([
            'jenis_pesanan' => [
                'sometimes',
                'required',
                'in:CUSTOM_LANGSUNG,CUSTOM_DARI_STOK',
            ],
            'jumlah_diminta' => [
                'required_if:jenis_pesanan,CUSTOM_DARI_STOK',
                'nullable',
                'integer',
                'min:1',
            ],
            'spesifikasi' => [
                'required',
                'string',
                'min:10',
                'max:3000',
            ],
        ]);

        // Jika jenis tidak dikirim, pertahankan perilaku lama.
        $jenisPesanan = $data['jenis_pesanan'] ?? 'CUSTOM_LANGSUNG';

        // Custom langsung tetap menggunakan jumlah 1.
        $jumlahDiminta = $jenisPesanan === 'CUSTOM_DARI_STOK'
            ? (int) $data['jumlah_diminta']
            : 1;

        $produk = Produk::tampilDiKatalog()
            ->with('stok')
            ->whereKey($id_produk)
            ->firstOrFail();

        // Periksa stok untuk custom yang berasal dari produk reguler.
        if ($jenisPesanan === 'CUSTOM_DARI_STOK') {
            $stokTersedia = $produk->stok?->jumlah_stok ?? 0;

            if ($jumlahDiminta > $stokTersedia) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'jumlah_diminta' => 'Jumlah yang diminta melebihi stok tersedia.',
                    ]);
            }
        }

        PermintaanCustom::create([
            'id_pengguna' => auth()->id(),
            'id_produk' => $produk->id_produk,
            'id_keranjang_item' => null,
            'jenis_pesanan' => $jenisPesanan,
            'jumlah_diminta' => $jumlahDiminta,
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
     * Menyetujui atau menolak penawaran harga UMKM.
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

        return redirect()
            ->route('custom.saya')
            ->with(
                'success',
                $aksi === 'setujui'
                    ? 'Penawaran harga berhasil disetujui.'
                    : 'Penawaran harga berhasil ditolak.'
            );
    }

    /**
     * Memasukkan permintaan custom yang sudah disetujui ke keranjang.
     */
    public function masukkanKeranjang(int $id)
    {
        abort_unless(auth()->user()->isPembeli(), 403);

        DB::transaction(function () use ($id) {
            $custom = PermintaanCustom::where('id_pengguna', auth()->id())
                ->whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($custom->status_custom !== 'DISETUJUI') {
                abort(403, 'Permintaan custom belum disetujui.');
            }

            if ($custom->harga_custom === null) {
                abort(403, 'Harga custom belum tersedia.');
            }

            // Cegah custom yang sama masuk keranjang berkali-kali.
            if ($custom->id_keranjang_item !== null) {
                return;
            }

            $jumlah = $custom->jenis_pesanan === 'CUSTOM_DARI_STOK'
                ? (int) $custom->jumlah_diminta
                : 1;

            // Periksa kembali stok sebelum memasukkan custom ke keranjang.
            // Stok belum dikurangi pada tahap ini.
            if ($custom->jenis_pesanan === 'CUSTOM_DARI_STOK') {
                $stok = Stok::where('id_produk', $custom->id_produk)
                    ->lockForUpdate()
                    ->first();

                if (!$stok || $stok->jumlah_stok < $jumlah) {
                    abort(422, 'Stok produk tidak mencukupi untuk jumlah pesanan custom ini.');
                }
            }

            $keranjang = Keranjang::firstOrCreate([
                'id_pengguna' => auth()->id(),
            ]);

            // Item custom dibuat terpisah dari produk reguler.
            $item = KeranjangItem::create([
                'id_keranjang' => $keranjang->id_keranjang,
                'id_produk' => $custom->id_produk,
                'jumlah' => $jumlah,
            ]);

            // Hubungkan permintaan custom dengan item keranjang.
            $custom->id_keranjang_item = $item->id_keranjang_item;
            $custom->save();
        });

        return redirect()
            ->route('keranjang.index')
            ->with('success', 'Produk custom berhasil dimasukkan ke keranjang.');
    }
}
