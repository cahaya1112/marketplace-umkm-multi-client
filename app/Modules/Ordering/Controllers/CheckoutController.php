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
use Illuminate\Support\Facades\Http;
use App\Modules\Payments\Contracts\PaymentGateway;
use App\Modules\Payments\Data\CreatePaymentRequest;
use App\Modules\Payments\Models\Pembayaran;

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
     * Menghitung ongkos kirim setiap UMKM menggunakan RajaOngkir Komerce.
     */
    public function hitungOngkir(Request $request)
    {
        $validated = $request->validate([
            'id_alamat' => 'required|integer',
            'selected_items' => 'required|array|min:1',
            'selected_items.*' => 'required|integer|distinct|min:1',
        ]);

        $idPengguna = auth()->id();

        $alamat = AlamatPembeli::where('id_alamat', $validated['id_alamat'])
            ->where('id_pengguna', $idPengguna)
            ->first();

        if (! $alamat || ! $alamat->id_wilayah_tujuan) {
            return response()->json([
                'message' => 'Alamat atau wilayah tujuan tidak valid.',
            ], 422);
        }

        $keranjang = Keranjang::where('id_pengguna', $idPengguna)
            ->first();

        if (! $keranjang) {
            return response()->json([
                'message' => 'Keranjang tidak ditemukan.',
            ], 422);
        }

        $items = KeranjangItem::with([
            'produk.umkm',
            'permintaanCustom',
        ])
            ->where('id_keranjang', $keranjang->id_keranjang)
            ->whereIn('id_keranjang_item', $validated['selected_items'])
            ->get();

        if ($items->count() !== count($validated['selected_items'])) {
            return response()->json([
                'message' => 'Ada item keranjang yang tidak valid.',
            ], 422);
        }

        foreach ($items as $item) {
            $custom = $item->permintaanCustom;

            if (
                ! $item->produk ||
                ! $item->produk->umkm ||
                (int) $item->jumlah < 1
            ) {
                return response()->json([
                    'message' => 'Data produk atau jumlah tidak valid.',
                ], 422);
            }

            if ($custom && (
                $custom->status_custom !== 'DISETUJUI' ||
                $custom->harga_custom === null
            )) {
                return response()->json([
                    'message' => 'Pesanan custom belum disetujui atau harganya belum tersedia.',
                ], 422);
            }
        }

        $kelompokUmkm = $items->groupBy(
            fn ($item) => $item->produk->id_umkm
        );

        $apiKey = config('services.rajaongkir.api_key');

        $baseUrl = rtrim(
            config('services.rajaongkir.base_url'),
            '/'
        );

        if (! $apiKey || ! $baseUrl) {
            return response()->json([
                'message' => 'Konfigurasi API RajaOngkir belum tersedia.',
            ], 500);
        }

        $hasil = [];

        foreach ($kelompokUmkm as $idUmkm => $daftarItem) {
            $umkm = $daftarItem->first()->produk->umkm;

            // Hitung total berat produk dalam satu UMKM.
            $berat = $daftarItem->sum(function ($item) {
                return (int) $item->produk->berat_gram
                    * (int) $item->jumlah;
            });

            $origin = $umkm->id_wilayah_asal;
            $destination = $alamat->id_wilayah_tujuan;

            if (! $origin || $berat <= 0) {
                return response()->json([
                    'message' => 'Wilayah asal UMKM atau berat produk belum diatur.',
                ], 422);
            }

            try {
                $response = Http::asForm()
                    ->withHeaders([
                        'key' => $apiKey,
                    ])
                    ->timeout(20)
                    ->post($baseUrl . '/calculate/domestic-cost', [
                        'origin' => $origin,
                        'destination' => $destination,
                        'weight' => $berat,
                        'courier' => 'jne:jnt:sicepat',
                        'price' => 'lowest',
                    ]);

                if (! $response->successful()) {
                    logger()->error('RajaOngkir gagal menghitung ongkir.', [
                        'umkm_id' => $idUmkm,
                        'response' => $response->body(),
                    ]);

                    return response()->json([
                        'message' => 'Gagal menghitung ongkir untuk UMKM '
                            . ($umkm->nama ?? 'ID ' . $idUmkm) . '.',
                    ], 502);
                }

                $layanan = collect($response->json('data', []))
                    ->map(function ($item) {
                        return [
                            'kode_kurir' => $item['code'] ?? '',
                            'nama_kurir' => $item['name'] ?? '',
                            'layanan' => $item['service'] ?? '',
                            'deskripsi' => $item['description'] ?? '',
                            'biaya' => (int) ($item['cost'] ?? 0),
                            'estimasi' => $item['etd'] ?? '',
                        ];
                    })
                    ->values();

                $hasil[] = [
                    'id_umkm' => (int) $idUmkm,
                    'nama_umkm' => $umkm->nama ?? 'UMKM',
                    'berat_gram' => $berat,
                    'layanan' => $layanan,
                ];

            } catch (\Throwable $e) {
                logger()->error('Koneksi RajaOngkir gagal.', [
                    'message' => $e->getMessage(),
                    'umkm_id' => $idUmkm,
                ]);

                return response()->json([
                    'message' => 'Tidak dapat terhubung ke layanan RajaOngkir.',
                ], 502);
            }
        }

        return response()->json([
            'data' => $hasil,
        ]);
    }

    /**
     * Memproses hanya item yang dipilih pembeli.
     * Pilihan ongkir disimpan pada masing-masing sub_pesanan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_alamat' => 'required|integer',
            'selected_items' => 'required|array|min:1',
            'selected_items.*' => 'required|integer|distinct|min:1',

            'pengiriman' => 'required|array|min:1',
            'pengiriman.*.kode_kurir' => 'required|string|max:50',
            'pengiriman.*.nama_kurir' => 'required|string|max:100',
            'pengiriman.*.layanan' => 'required|string|max:100',
            'pengiriman.*.biaya' => 'required|integer|min:0',
            'pengiriman.*.estimasi' => 'nullable|string|max:100',
        ]);

        $idPengguna = auth()->id();

        $selectedItemIds = array_map(
            'intval',
            $validated['selected_items']
        );

        $pengiriman = $validated['pengiriman'];

        $hasil = DB::transaction(function () use (
            $request,
            $idPengguna,
            $selectedItemIds,
            $pengiriman
        ) {
            // Validasi alamat milik pembeli.
            $alamat = AlamatPembeli::where('id_alamat', $request->id_alamat)
                ->where('id_pengguna', $idPengguna)
                ->lockForUpdate()
                ->first();

            if (! $alamat || ! $alamat->id_wilayah_tujuan) {
                throw ValidationException::withMessages([
                    'id_alamat' => 'Alamat atau wilayah tujuan tidak valid.',
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

            // Ambil item yang dipilih dan kunci barisnya.
            $items = KeranjangItem::with([
                'produk.umkm',
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

            // Validasi seluruh produk dan custom.
            foreach ($items as $item) {
                $produk = $item->produk;
                $custom = $item->permintaanCustom;

                if (
                    ! $produk ||
                    ! $produk->umkm ||
                    (int) $item->jumlah < 1
                ) {
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

            // Kelompokkan item berdasarkan UMKM.
            $kelompokUmkm = $items->groupBy(
                fn ($item) => $item->produk->id_umkm
            );

            // Pastikan setiap UMKM memiliki pilihan pengiriman.
            $idUmkmPesanan = $kelompokUmkm->keys()
                ->map(fn ($id) => (string) $id)
                ->sort()
                ->values()
                ->all();

            $idUmkmPengiriman = collect(array_keys($pengiriman))
                ->map(fn ($id) => (string) $id)
                ->sort()
                ->values()
                ->all();

            if ($idUmkmPesanan !== $idUmkmPengiriman) {
                throw ValidationException::withMessages([
                    'pengiriman' => 'Pilih layanan pengiriman untuk setiap UMKM.',
                ]);
            }

            /*
             * VALIDASI ULANG ONGKIR
             * Biaya dan layanan diperiksa langsung ke RajaOngkir.
             */
            $pengirimanValid = $this->validasiUlangOngkir(
                $alamat,
                $kelompokUmkm,
                $pengiriman
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
                // Gunakan data ongkir resmi dari RajaOngkir.
                $pilihanOngkir = $pengirimanValid[$idUmkm];

                $subPesanan = SubPesanan::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_umkm' => $idUmkm,
                    'status_sub_pesanan' => 'MENUNGGU_PEMBAYARAN',

                    'kurir_kode' => $pilihanOngkir['kode_kurir'],
                    'kurir_layanan' => $pilihanOngkir['layanan'],
                    'estimasi_pengiriman' => $pilihanOngkir['estimasi'],
                    'biaya_pengiriman' => $pilihanOngkir['biaya'],

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
            ->route('pesanan.detail', $hasil->id_pesanan)
            ->with(
                'success',
                'Pesanan berhasil dibuat dengan kode ' .
                    $hasil->kode_pesanan
            );
    }

    /**
     * Memvalidasi ulang pilihan ongkir langsung ke RajaOngkir.
     *
     * Data biaya dan estimasi yang dikembalikan berasal dari API,
     * bukan dari input browser.
     */
    private function validasiUlangOngkir(
        $alamat,
        $kelompokUmkm,
        array $pengiriman
    ): array {
        $apiKey = config('services.rajaongkir.api_key');

        $baseUrl = rtrim(
            config('services.rajaongkir.base_url'),
            '/'
        );

        if (! $apiKey || ! $baseUrl) {
            throw ValidationException::withMessages([
                'pengiriman' => 'Konfigurasi API RajaOngkir belum tersedia.',
            ]);
        }

        $hasil = [];

        foreach ($kelompokUmkm as $idUmkm => $daftarItem) {
            $umkm = $daftarItem->first()->produk->umkm;

            if (! $umkm || ! $umkm->id_wilayah_asal) {
                throw ValidationException::withMessages([
                    'pengiriman' => 'Wilayah asal UMKM belum tersedia.',
                ]);
            }

            // Hitung ulang berat dari database.
            $berat = $daftarItem->sum(function ($item) {
                return (int) $item->produk->berat_gram
                    * (int) $item->jumlah;
            });

            if ($berat <= 0) {
                throw ValidationException::withMessages([
                    'pengiriman' => 'Berat produk belum diatur.',
                ]);
            }

            try {
                $response = Http::asForm()
                    ->withHeaders([
                        'key' => $apiKey,
                    ])
                    ->timeout(20)
                    ->post($baseUrl . '/calculate/domestic-cost', [
                        'origin' => $umkm->id_wilayah_asal,
                        'destination' => $alamat->id_wilayah_tujuan,
                        'weight' => $berat,
                        'courier' => 'jne:jnt:sicepat',
                        'price' => 'lowest',
                    ]);

                if (! $response->successful()) {
                    logger()->error('Validasi ulang ongkir gagal.', [
                        'umkm_id' => $idUmkm,
                        'response' => $response->body(),
                    ]);

                    throw ValidationException::withMessages([
                        'pengiriman' => 'Gagal memvalidasi ongkir. Silakan hitung ulang.',
                    ]);
                }

                $layananTersedia = collect(
                    $response->json('data', [])
                );

                $pilihan = $pengiriman[$idUmkm] ?? null;

                if (! $pilihan) {
                    throw ValidationException::withMessages([
                        'pengiriman' => 'Pilihan pengiriman UMKM tidak ditemukan.',
                    ]);
                }

                // Cari kurir dan layanan yang sama persis.
                $layananValid = $layananTersedia->first(
                    function ($item) use ($pilihan) {
                        return strtolower($item['code'] ?? '') ===
                                strtolower($pilihan['kode_kurir']) &&
                            ($item['service'] ?? '') === $pilihan['layanan'];
                    }
                );

                if (! $layananValid) {
                    throw ValidationException::withMessages([
                        'pengiriman' => 'Layanan kurir sudah tidak tersedia. Silakan hitung ulang.',
                    ]);
                }

                $biayaAsli = (int) ($layananValid['cost'] ?? 0);
                $biayaDikirim = (int) $pilihan['biaya'];

                if ($biayaAsli !== $biayaDikirim) {
                    throw ValidationException::withMessages([
                        'pengiriman' => 'Biaya ongkir berubah. Silakan hitung ulang ongkir.',
                    ]);
                }

                // Simpan nilai resmi yang dikembalikan API.
                $hasil[$idUmkm] = [
                    'kode_kurir' => $layananValid['code'],
                    'nama_kurir' => $layananValid['name'] ?? '',
                    'layanan' => $layananValid['service'],
                    'biaya' => $biayaAsli,
                    'estimasi' => $layananValid['etd'] ?? '',
                ];

            } catch (ValidationException $e) {
                throw $e;
            } catch (\Throwable $e) {
                logger()->error('Kesalahan validasi ulang ongkir.', [
                    'umkm_id' => $idUmkm,
                    'message' => $e->getMessage(),
                ]);

                throw ValidationException::withMessages([
                    'pengiriman' => 'Tidak dapat memvalidasi ongkir. Silakan coba lagi.',
                ]);
            }
        }

        return $hasil;
    }
}
