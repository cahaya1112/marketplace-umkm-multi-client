<?php

namespace App\Modules\Payments\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Payments\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /** Menerima notifikasi pembayaran dari Midtrans. */
    public function notification(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|string|max:255',
            'status_code' => 'required|string|max:10',
            'gross_amount' => 'required|string|max:30',
            'signature_key' => 'required|string',
            'transaction_status' => 'required|string|max:50',
            'transaction_id' => 'nullable|string|max:255',
            'fraud_status' => 'nullable|string|max:50',
            'payment_type' => 'nullable|string|max:100',
            'transaction_time' => 'nullable|string|max:100',
            'settlement_time' => 'nullable|string|max:100',
        ]);

        $serverKey = config('services.midtrans.server_key');

        if (empty($serverKey)) {
            Log::error('Midtrans notification ditolak: Server Key belum dikonfigurasi.');
            return response()->json(['message' => 'Konfigurasi pembayaran tidak tersedia.'], 500);
        }

        // Signature: SHA512(order_id + status_code + gross_amount + server_key).
        $signatureExpected = hash(
            'sha512',
            $data['order_id'] . $data['status_code'] . $data['gross_amount'] . $serverKey
        );

        if (! hash_equals($signatureExpected, $data['signature_key'])) {
            Log::warning('Signature notifikasi Midtrans tidak valid.', [
                'order_id' => $data['order_id'],
            ]);
            return response()->json(['message' => 'Signature tidak valid.'], 403);
        }

        $jumlahDariGateway = (float) $data['gross_amount'];
        $statusGateway = strtolower($data['transaction_status']);
        $fraudStatus = strtolower($data['fraud_status'] ?? '');

        $statusPembayaran = match ($statusGateway) {
            'settlement' => 'PAID',
            'capture' => $fraudStatus === 'accept' ? 'PAID' : 'PENDING',
            'pending' => 'PENDING',
            'deny', 'cancel' => 'FAILED',
            'expire' => 'EXPIRED',
            default => null,
        };

        // Status lain seperti refund tidak mengubah status pembayaran pada alur ini.
        if ($statusPembayaran === null) {
            Log::info('Status notifikasi Midtrans tidak mengubah status pembayaran.', [
                'order_id' => $data['order_id'],
                'transaction_status' => $statusGateway,
            ]);
            return response()->json(['message' => 'Notifikasi diterima; tidak ada perubahan status.']);
        }

        $hasil = DB::transaction(function () use (
            $data,
            $jumlahDariGateway,
            $statusPembayaran,
            $statusGateway
        ) {
            $pembayaran = Pembayaran::where('order_id_gateway', $data['order_id'])
                ->lockForUpdate()
                ->first();

            if (! $pembayaran) {
                Log::warning('Pembayaran untuk order_id Midtrans tidak ditemukan.', [
                    'order_id' => $data['order_id'],
                ]);
                return 'NOT_FOUND';
            }

            if ($pembayaran->metode_pembayaran !== 'MIDTRANS') {
                Log::warning('Notifikasi Midtrans mengarah ke pembayaran non-Midtrans.', [
                    'order_id' => $data['order_id'],
                    'metode_pembayaran' => $pembayaran->metode_pembayaran,
                ]);
                return 'WRONG_METHOD';
            }

            // Nilai gross_amount Midtrans dapat berbentuk desimal, sedangkan DB menyimpan rupiah.
            if ((int) $pembayaran->jumlah_bayar !== (int) round($jumlahDariGateway)) {
                Log::warning('Nominal notifikasi Midtrans tidak cocok.', [
                    'order_id' => $data['order_id'],
                    'nominal_database' => $pembayaran->jumlah_bayar,
                    'nominal_gateway' => $data['gross_amount'],
                ]);
                return 'AMOUNT_MISMATCH';
            }

            // Notifikasi berulang tidak boleh menurunkan status yang sudah PAID.
            if ($pembayaran->status_pembayaran === 'PAID') {
                $pembayaran->payload_notifikasi = $data;
                $pembayaran->id_transaksi_gateway =
                    $data['transaction_id'] ?? $pembayaran->id_transaksi_gateway;
                $pembayaran->save();
                return 'OK';
            }

            $pembayaran->status_pembayaran = $statusPembayaran;
            $pembayaran->id_transaksi_gateway =
                $data['transaction_id'] ?? $pembayaran->id_transaksi_gateway;
            $pembayaran->payload_notifikasi = $data;

            if ($statusPembayaran === 'PAID' && empty($pembayaran->waktu_bayar)) {
                $pembayaran->waktu_bayar = now();
            }

            if ($statusPembayaran === 'EXPIRED') {
                $pembayaran->expired_at = now();
            }

            $pembayaran->save();

            // Pesanan diproses hanya setelah pembayaran Midtrans terverifikasi.
            if ($statusPembayaran === 'PAID') {
                DB::table('sub_pesanan')
                    ->where('id_pesanan', $pembayaran->id_pesanan)
                    ->where('status_sub_pesanan', 'MENUNGGU_PEMBAYARAN')
                    ->update(['status_sub_pesanan' => 'DIPROSES']);
            }

            Log::info('Status pembayaran Midtrans diperbarui.', [
                'order_id' => $data['order_id'],
                'status_gateway' => $statusGateway,
                'status_pembayaran' => $statusPembayaran,
            ]);

            return 'OK';
        });

        if ($hasil === 'NOT_FOUND') {
            return response()->json(['message' => 'Data pembayaran tidak ditemukan.'], 404);
        }

        if ($hasil === 'WRONG_METHOD') {
            return response()->json(['message' => 'Metode pembayaran tidak sesuai.'], 422);
        }

        if ($hasil === 'AMOUNT_MISMATCH') {
            return response()->json(['message' => 'Nominal pembayaran tidak sesuai.'], 422);
        }

        return response()->json(['message' => 'Notifikasi pembayaran berhasil diproses.']);
    }
}
