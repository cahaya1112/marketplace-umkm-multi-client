<?php

namespace App\Modules\Ordering\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Stok;
use App\Modules\Catalog\PermintaanCustom;
use App\Modules\Ordering\Models\AlamatPembeli;
use App\Modules\Ordering\Models\Keranjang;
use App\Modules\Ordering\Models\KeranjangItem;
use App\Modules\Ordering\Models\Pesanan;
use App\Modules\Ordering\Models\SubPesanan;
use App\Modules\Ordering\Models\DetailPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    /**
     * Menampilkan checkout berdasarkan item yang dipilih.
     */
    public function index(Request $request)
    {
        $request->validate([
            'selected_items' => 'required|array|min:1',
            'selected_items.*' => 'required|integer|distinct|min:1',
        ]);

        $idPengguna = auth()->id();

        $keranjang = Keranjang::where('id_pengguna', $idPengguna)
            ->first();

        if (! $keranjang) {
            return redirect()->route('keranjang.index')
                ->with('galat', 'Keranjang tidak ditemukan.');
        }

        $selectedItemIds = array_map(
            'intval',
            $request->input('selected_items')
        );

        $items = KeranjangItem::with([
            'produk.umkm',
            'produk.stok',
            'permintaanCustom',
        ])
            ->where('id_keranjang', $keranjang->id_keranjang)
            ->whereIn('id_keranjang_item', $selectedItemIds)
            ->get();

        // Pastikan seluruh item pilihan benar-benar milik keranjang.
        if ($items->count() !== count($selectedItemIds)) {
            return redirect()->route('keranjang.index')
                ->with('galat', 'Ada item pilihan yang tidak valid.');
        }

        foreach ($items as $item) {
            if (! $item->produk) {
                return redirect()->route('keranjang.index')
                    ->with('galat', 'Produk tidak ditemukan.');
            }

            if ($item->permintaanCustom) {
                $custom = $item->permintaanCustom;

                if (
                    $custom->status_custom !== 'DISETUJUI' ||
                    $custom->harga_custom === null
                ) {
                    return redirect()->route('keranjang.index')
                        ->with('galat', 'Harga atau persetujuan custom belum tersedia.');
                }
            }
        }

        // Gunakan hanya item terpilih pada halaman checkout.
        $keranjang->setRelation('item', $items);

        $kelompokUmkm = $items->groupBy(
            fn ($item) => $item->produk->id_umkm
        );

        $totalBelanja = 0;

        foreach ($items as $item) {
            $harga = $item->permintaanCustom
                ? $item->permintaanCustom->harga_custom
                : $item->produk->harga_retail;

            $totalBelanja += $harga * $item->jumlah;
        }

        $alamat = AlamatPembeli::where(
            'id_pengguna',
            $idPengguna
        )->get();

        if ($alamat->isEmpty()) {
            return redirect()->route('keranjang.index')
                ->with('galat', 'Tambahkan alamat pengiriman terlebih dahulu.');
        }

        return view('ordering::checkout.index', compact(
            'keranjang',
            'alamat',
            'kelompokUmkm',
            'totalBelanja',
            'selectedItemIds'
        ));
    }

    /**
     * Memproses hanya item yang dipilih pembeli.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_alamat' => 'required|integer',
            'selected_items' => 'required|array|min:1',
            'selected_items.*' => 'required|integer|distinct|min:1',
        ]);

        $idPengguna = auth()->id();

        $selectedItemIds = array_map(
            'intval',
            $request->input('selected_items')
        );

        $hasil = DB::transaction(function () use (
            $request,
            $idPengguna,
            $selectedItemIds
        ) {
            // Validasi alamat milik pembeli.
            $alamat = AlamatPembeli::where('id_alamat', $request->id_alamat)
                ->where('id_pengguna', $idPengguna)
                ->lockForUpdate()
                ->first();

            if (! $alamat) {
                throw ValidationException::withMessages([
                    'id_alamat' => 'Alamat pengiriman tidak valid.',
                ]);
            }

            // Kunci keranjang pembeli.
            $keranjang = Keranjang::where('id_pengguna', $idPengguna)
                ->lockForUpdate()
                ->first();

            if (! $keranjang) {
                throw ValidationException::withMessages([
                    'keranjang' => 'Keranjang tidak ditemukan.',
                ]);
            }

            // Ambil hanya item yang dipilih dan kunci barisnya.
            $items = KeranjangItem::with([
                'produk',
                'permintaanCustom',
            ])
                ->where('id_keranjang', $keranjang->id_keranjang)
                ->whereIn('id_keranjang_item', $selectedItemIds)
                ->lockForUpdate()
                ->get();

            if ($items->count() !== count($selectedItemIds)) {
                throw ValidationException::withMessages([
                    'selected_items' => 'Item pilihan tidak valid atau sudah berubah.',
                ]);
            }

            // Validasi seluruh produk dan custom sebelum membuat pesanan.
            foreach ($items as $item) {
                $produk = $item->produk;
                $custom = $item->permintaanCustom;

                if (! $produk || $item->jumlah < 1) {
                    throw ValidationException::withMessages([
                        'produk' => 'Data produk atau jumlah tidak valid.',
                    ]);
                }

                if ($custom) {
                    if (
                        $custom->status_custom !== 'DISETUJUI' ||
                        $custom->harga_custom === null
                    ) {
                        throw ValidationException::withMessages([
                            'custom' => 'Pesanan custom belum disetujui UMKM.',
                        ]);
                    }

                    if (
                        $custom->jenis_pesanan === 'CUSTOM_DARI_STOK' &&
                        (int) $item->jumlah !== (int) $custom->jumlah_diminta
                    ) {
                        throw ValidationException::withMessages([
                            'jumlah' => 'Jumlah pesanan custom tidak sesuai.',
                        ]);
                    }
                }
            }

            // Kelompokkan item terpilih berdasarkan UMKM.
            $kelompokUmkm = $items->groupBy(
                fn ($item) => $item->produk->id_umkm
            );

            // Buat kode pesanan.
            do {
                $kodePesanan = 'PSN-' . strtoupper(Str::random(10));
            } while (
                Pesanan::where('kode_pesanan', $kodePesanan)->exists()
            );

            // Simpan pesanan utama beserta snapshot alamat.
            $pesanan = Pesanan::create([
                'id_pengguna' => $idPengguna,
                'id_alamat' => $alamat->id_alamat,
                'kode_pesanan' => $kodePesanan,
                'tanggal_pesanan' => now(),

                'nama_penerima_snapshot' => $alamat->nama_penerima,
                'nomor_telepon_snapshot' => $alamat->nomor_telepon,
                'alamat_lengkap_snapshot' => $alamat->alamat_lengkap,
                'id_wilayah_tujuan_snapshot' => $alamat->id_wilayah_tujuan,
                'kode_pos_snapshot' => $alamat->kode_pos,
            ]);

            foreach ($kelompokUmkm as $idUmkm => $daftarItem) {
                $subPesanan = SubPesanan::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_umkm' => $idUmkm,
                    'status_sub_pesanan' => 'MENUNGGU_PEMBAYARAN',
                    'biaya_pengiriman' => 0,
                    'dibuat_pada' => now(),
                ]);

                foreach ($daftarItem as $item) {
                    $produk = $item->produk;
                    $custom = $item->permintaanCustom;

                    $isCustom = $custom !== null;

                    $hargaCustom = $isCustom
                        ? $custom->harga_custom
                        : null;

                    // Reguler dan custom dari stok mengurangi stok.
                    // Custom langsung tidak mengurangi stok reguler.
                    $kurangiStok = ! $isCustom ||
                        $custom->jenis_pesanan === 'CUSTOM_DARI_STOK';

                    if ($kurangiStok) {
                        $stok = Stok::where(
                            'id_produk',
                            $produk->id_produk
                        )
                            ->lockForUpdate()
                            ->first();

                        if (! $stok || $stok->jumlah_stok < $item->jumlah) {
                            throw ValidationException::withMessages([
                                'stok' => 'Stok produk "' .
                                    $produk->nama .
                                    '" tidak mencukupi.',
                            ]);
                        }

                        $stok->jumlah_stok -= $item->jumlah;
                        $stok->save();
                    }

                    DetailPesanan::create([
                        'id_sub_pesanan' => $subPesanan->id_sub_pesanan,
                        'id_produk' => $produk->id_produk,
                        'id_custom' => $isCustom
                            ? $custom->id_custom
                            : null,

                        'harga_retail' => $produk->harga_retail,
                        'harga_custom' => $hargaCustom,
                        'jumlah' => $item->jumlah,

                        'nama_produk_snapshot' => $produk->nama,
                        'spesifikasi_custom_snapshot' => $isCustom
                            ? $custom->spesifikasi
                            : null,
                    ]);
                }
            }

            // Lepaskan relasi custom hanya untuk item terpilih.
            PermintaanCustom::whereIn(
                'id_keranjang_item',
                $selectedItemIds
            )->update([
                'id_keranjang_item' => null,
            ]);

            // Hapus hanya item yang sudah dibeli.
            KeranjangItem::where('id_keranjang', $keranjang->id_keranjang)
                ->whereIn('id_keranjang_item', $selectedItemIds)
                ->delete();

            return $pesanan;
        });

        return redirect()
            ->route('keranjang.index')
            ->with(
                'success',
                'Pesanan berhasil dibuat dengan kode ' .
                    $hasil->kode_pesanan
            );
    }
}
