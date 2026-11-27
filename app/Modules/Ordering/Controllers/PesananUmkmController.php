<?php

namespace App\Modules\Ordering\Controllers;

use App\Modules\Catalog\Controllers\KelolaController;
use App\Modules\Ordering\Models\SubPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PesananUmkmController extends KelolaController
{
    /**
     * Daftar subpesanan yang hanya dimiliki UMKM pengguna login.
     */
    public function index()
    {
        $umkm = $this->umkmSaya();

        $subPesanan = SubPesanan::query()
            ->with(['pesanan.pengguna', 'detail'])
            ->where('id_umkm', $umkm->id_umkm)
            ->orderByDesc('id_sub_pesanan')
            ->paginate(10);

        return view('ordering::umkm.pesanan.index', compact('umkm', 'subPesanan'));
    }

    /**
     * Detail satu subpesanan milik UMKM yang sedang login.
     */
    public function show(int $id)
    {
        $umkm = $this->umkmSaya();

        $subPesanan = SubPesanan::query()
            ->with(['pesanan.pengguna', 'pesanan.pembayaran', 'detail'])
            ->where('id_umkm', $umkm->id_umkm)
            ->findOrFail($id);

        return view('ordering::umkm.pesanan.show', compact('umkm', 'subPesanan'));
    }

    /**
     * Perubahan status pasca-checkout:
     * DIPROSES -> DIKIRIM (wajib resi)
     * DIKIRIM -> SELESAI
     *
     * Status MENUNGGU_PEMBAYARAN tidak diubah dari halaman ini.
     * Status tersebut harus berubah melalui alur pembayaran/webhook.
     */
    public function updateStatus(Request $request, int $id)
    {
        $umkm = $this->umkmSaya();

        $data = $request->validate([
            'status_sub_pesanan' => ['required', 'in:DIKIRIM,SELESAI'],
            'nomor_resi' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($umkm, $id, $data) {
            $subPesanan = SubPesanan::query()
                ->where('id_umkm', $umkm->id_umkm)
                ->whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            $pesanan = $subPesanan->pesanan()
                ->lockForUpdate()
                ->firstOrFail();

            $pembayaran = $pesanan->pembayaran()
                ->lockForUpdate()
                ->first();

            if ($data['status_sub_pesanan'] === 'DIKIRIM') {
                if ($subPesanan->status_sub_pesanan !== 'DIPROSES') {
                    throw ValidationException::withMessages([
                        'status_sub_pesanan' => 'Pesanan hanya dapat dikirim jika statusnya DIPROSES.',
                    ]);
                }

                if (
                    $pembayaran &&
                    $pembayaran->metode_pembayaran === 'MIDTRANS' &&
                    $pembayaran->status_pembayaran !== 'PAID'
                ) {
                    throw ValidationException::withMessages([
                        'status_sub_pesanan' => 'Pesanan Midtrans belum dibayar.',
                    ]);
                }

                $nomorResi = trim((string) ($data['nomor_resi'] ?? ''));

                if ($nomorResi === '') {
                    throw ValidationException::withMessages([
                        'nomor_resi' => 'Nomor resi wajib diisi sebelum pesanan dikirim.',
                    ]);
                }

                $subPesanan->nomor_resi = $nomorResi;
                $subPesanan->status_sub_pesanan = 'DIKIRIM';
                $subPesanan->save();

                return;
            }

            if ($data['status_sub_pesanan'] === 'SELESAI') {
                if ($subPesanan->status_sub_pesanan !== 'DIKIRIM') {
                    throw ValidationException::withMessages([
                        'status_sub_pesanan' => 'Pesanan hanya dapat diselesaikan jika statusnya DIKIRIM.',
                    ]);
                }

                $subPesanan->status_sub_pesanan = 'SELESAI';
                $subPesanan->save();

                // Pembayaran COD adalah pembayaran pada level pesanan induk.
                // Tandai PAID hanya jika semua subpesanan pada pesanan induk selesai.
                if (
                    $pembayaran &&
                    $pembayaran->metode_pembayaran === 'COD' &&
                    $pembayaran->status_pembayaran === 'PENDING'
                ) {
                    $semuaSelesai = SubPesanan::query()
                        ->where('id_pesanan', $pesanan->id_pesanan)
                        ->where('status_sub_pesanan', '!=', 'SELESAI')
                        ->doesntExist();

                    if ($semuaSelesai) {
                        $pembayaran->status_pembayaran = 'PAID';
                        $pembayaran->waktu_bayar = now();
                        $pembayaran->save();
                    }
                }
            }
        });

        return redirect()
            ->route('kelola.pesanan.show', $id)
            ->with('sukses', 'Status pesanan berhasil diperbarui.');
    }
}
