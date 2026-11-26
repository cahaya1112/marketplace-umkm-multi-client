<?php

namespace App\Modules\Ordering\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Ordering\Models\Pesanan;

class RiwayatPesananController extends Controller
{
    /**
     * Menampilkan seluruh riwayat pesanan milik pembeli.
     */
    public function index()
    {
        $pesanan = Pesanan::with([
            'subPesanan.umkm',
            'subPesanan.detail',
            'pembayaran',
        ])
            ->where('id_pengguna', auth()->id())
            ->orderByDesc('tanggal_pesanan')
            ->paginate(10);

        return view('ordering::riwayat.index', compact('pesanan'));
    }

    /**
     * Menampilkan detail satu pesanan milik pembeli.
     */
    public function detail($id)
    {
        $pesanan = Pesanan::with([
            'subPesanan.umkm',
            'subPesanan.detail',
            'pembayaran',
        ])
            ->where('id_pengguna', auth()->id())
            ->where('id_pesanan', $id)
            ->firstOrFail();

        return view('ordering::riwayat.detail', compact('pesanan'));
    }
}